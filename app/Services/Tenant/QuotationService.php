<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\DTOs\QuotationDTO;
use App\Mail\QuotationDetails;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\PosSetting;
use App\Models\Product;
use App\Models\ProductQuotation;
use App\Models\ProductVariant;
use App\Models\Quotation;
use App\Models\Unit;
use App\Models\Variant;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuotationService
{


    protected SupplierService $supplierService;
    protected WarehouseService $warehouseService;
    protected CustomerService $customerService;
    protected BillerService $billerService;
    protected TaxCalculatorService $taxCalculatorService;
    protected ProductWarehouseService $productWarehouseService;
    protected SendMailAction $sendMailAction;
    protected ProductService $productService;
    protected ProductQuotationService $productQuotationService;

    public function __construct(
        SendMailAction $sendMailAction,
        SupplierService $supplierService,
        WarehouseService $warehouseService,
        CustomerService $customerService,
        BillerService $billerService,
        TaxCalculatorService $taxCalculatorService,
        ProductWarehouseService $productWarehouseService,
        ProductService $productService,
        ProductQuotationService $productQuotationService
    ) {
        $this->sendMailAction = $sendMailAction;
        $this->supplierService = $supplierService;
        $this->warehouseService = $warehouseService;
        $this->customerService = $customerService;
        $this->billerService = $billerService;
        $this->taxCalculatorService = $taxCalculatorService;
        $this->productWarehouseService = $productWarehouseService;
        $this->productService = $productService;
        $this->productQuotationService = $productQuotationService;
    }

    /** index */
    public function getQuotations(array $filters)
    {
        $user = Auth::guard('web')->user();

        return Quotation::with(['biller', 'customer', 'supplier', 'user','warehouse'])
            ->filterByUserAccess($user)
            ->filterByWarehouse($filters['warehouse_id'])
           // ->filterByDateRange($filters['starting_date'], $filters['ending_date'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getFilters($request)
    {
        return [
            'warehouse_id' => $request->input('warehouse_id', 0),
            'warehouses' => Warehouse::all(),
            'order_dir' => "desc",
            'starting_date' => $request->input('starting_date', now()->subYear()->toDateString()),
            'ending_date' => $request->input('ending_date', now()->format('Y-m-d')),
        ];
    }

    public function productQuotationData($id)
    {
        // جلب جميع بيانات المنتجات المرتبطة بالعرض مع العلاقات اللازمة دفعة واحدة
        $productQuotations = ProductQuotation::with([
            'product:id,name',
            'variant:id,item_code',
            'saleUnit:id,unit_code',
            'productBatch:id,batch_no'
        ])->where('quotation_id', $id)->get();

        // تجهيز البيانات لإرسالها بنفس الشكل المتوقع في الجافاسكريبت
        return $productQuotations->map(function ($item) {
            return [
                'name'       => $item->product->name . ($item->variant ? ' [' . $item->variant->item_code . ']' : ''),
                'batch_no'   => optional($item->productBatch)->batch_no ?? '-',
                'qty'        => $item->qty,
                'unit_code'  => optional($item->saleUnit)->unit_code ?? '',
                'subtotal'   => $item->total,
                'tax'        => $item->tax,
                'tax_rate'   => $item->tax_rate,
                'discount'   => $item->discount,
            ];
        });
    }


    /** get Create Quotation Data */
    public function getCreateQuotationData()
    {
        return [
            'billers' => $this->billerService->getBillers(),
            'warehouses' => $this->warehouseService->getWarehouses(),
            'customers' => $this->customerService->getCustomers(),
            'suppliers' => $this->supplierService->getSuppliers(),
            'taxes' => $this->taxCalculatorService->getTaxes(),
            'currency' => Currency::find(1),
        ];
    }

    /** Get all product data for the given warehouse id. */
    public function getProduct($id)
    {
        try {
        return $this->productWarehouseService->getProduct($id);
        } catch (\Exception $e) {
            Log::error("Error in quotation getProduct: " . $e->getMessage());
            throw new \Exception('Error in quotation getProduct: ' . $e->getMessage());
        }
    }


    /** Store */

    public function createQuotation(array $data)
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = Auth::id();
            $data['reference_no'] = 'qr-' . now()->format("Ymd-His");

            $document = $this->prepareData($data);

            $quotation = Quotation::create($data);

            if (isset($data['document'])) {
                $quotation->addMedia($document)->toMediaCollection('quotations');
            }

            // Send mail if the offer is approved
            $mailData = ($quotation->quotation_status == 2) ? $this->prepareMailData($data, $quotation) : null;

            // Save product details associated with the quote
            $this->storeProductQuotations($data, $quotation, $mailData);

            // Send mail if available
            $message = $this->sendQuotationMail($mailData);
            DB::commit();
            return ['message' => $message, 'redirect' => 'quotations'];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error in quotation createQuotation: " . $e->getMessage());
            throw new \Exception('Error in quotation getProduct: ' . $e->getMessage());
        }

    }

    private function prepareData(array $data)
    {
        $document = $data['document'] ?? null;

        if (isset($data['document'])) {
            unset($data['document']);
        }

        return $document;
    }

    private function prepareMailData(array $data, Quotation $quotation)
    {
        $customer = Customer::findOrFail($data['customer_id']);
        if (!$customer || !$customer->email) {
            return false;
        }
        return [
            'email' => $customer->email,
            'reference_no' => $quotation->reference_no,
            'total_qty' => $quotation->total_qty,
            'total_price' => $quotation->total_price,
            'order_tax' => $quotation->order_tax,
            'order_tax_rate' => $quotation->order_tax_rate,
            'order_discount' => $quotation->order_discount,
            'shipping_cost' => $quotation->shipping_cost,
            'grand_total' => $quotation->grand_total,
            'products' => [],
            'unit' => [],
            'total' => []
        ];
    }

    private function storeProductQuotations(array $data, Quotation $quotation, &$mailData)
    {
        $products = [];

        foreach ($data['product_id'] as $i => $id) {
            $product = Product::findOrFail($id);
            $saleUnitId = ($data['sale_unit'][$i] != 'n/a') ? Unit::where('unit_name', $data['sale_unit'][$i])->value('id') : 0;

            $variantId = $product->is_variant ?
                ProductVariant::where('product_id', $id)
                    ->whereHas('variant', fn($query) => $query->where('code', $data['product_code'][$i]))
                    ->value('variant_id')
                : null;

            $products[] = [
                'quotation_id' => $quotation->id,
                'product_id' => $id,
                'product_batch_id' => $data['product_batch_id'][$i],
                'qty' => $mailData['qty'][$i] = $data['qty'][$i],
                'sale_unit_id' => $saleUnitId,
                'net_unit_price' => $data['net_unit_price'][$i],
                'discount' => $data['discount'][$i],
                'tax_rate' => $data['tax_rate'][$i],
                'tax' => $data['tax'][$i],
                'total' => $mailData['total'][$i] = $data['subtotal'][$i],
                'variant_id' => $variantId
            ];

            $mailData['products'][$i] = $variantId ?
                "{$product->name} [" . Variant::find($variantId)->name . "]"
                : $product->name;

            $mailData['unit'][$i] = $saleUnitId ? Unit::find($saleUnitId)->unit_code : '';
        }

        ProductQuotation::insert($products);
    }

    private function sendQuotationMail($mailData)
    {
        if (!$this->sendMailAction->execute($mailData, QuotationDetails::class)) {
            $message = __('Quotation created successfully. Please setup your mail settings to send mail.');
        } else {
            $message = __('Quotation created successfully.');
        }

        return $message;
    }


    /** Send Email to Customer "quotation" */
    public function sendMail(int $quotationId): string
    {
        // **✅ Optimize queries via Eager Loading to reduce the number of queries**
        $quotation = Quotation::with(['customer', 'productQuotations.product', 'productQuotations.variant', 'productQuotations.saleUnit'])
            ->findOrFail($quotationId);

        $customer = $quotation->customer;


        if (!$customer || !$customer->email) {
            return 'Customer doesn\'t have an email!';
        }

        // **✅ Intelligently prepare email data without additional inquiries**
        $mailData = [
            'email'          => $customer->email,
            'reference_no'   => $quotation->reference_no,
            'total_qty'      => $quotation->total_qty,
            'total_price'    => $quotation->total_price,
            'order_tax'      => $quotation->order_tax,
            'order_tax_rate' => $quotation->order_tax_rate,
            'order_discount' => $quotation->order_discount,
            'shipping_cost'  => $quotation->shipping_cost,
            'grand_total'    => $quotation->grand_total,
            'products'       => [],
            'unit'           => [],
            'qty'            => [],
            'total'          => [],
        ];

        foreach ($quotation->productQuotations as $productQuotation) {
            $productName = $productQuotation->product->name;
            if ($productQuotation->variant) {
                $productName .= ' [' . $productQuotation->variant->name . ']';
            }
            $mailData['products'][] = $productName;
            $mailData['unit'][] = $productQuotation->saleUnit->unit_code ?? '';
            $mailData['qty'][] = $productQuotation->qty;
            $mailData['total'][] = $productQuotation->total;
        }

        if (!$this->sendMailAction->execute($mailData, QuotationDetails::class)) {
            return 'Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
        }

        return 'Mail sent successfully.';
    }

    /** Get Create Sale Data */
    public function getCreateSaleData(int $quotationId): array
    {
        return [
            'customers' => $this->customerService->getCustomers(),
            'warehouses' => $this->warehouseService->getWarehouses(),
            'billers' => $this->billerService->getBillers(),
            'taxes' => $this->taxCalculatorService->getTaxes(),
            'quotation' => Quotation::with('productQuotations.product')->findOrFail($quotationId),
            'posSetting' => PosSetting::latest()->first()
        ];
    }

    /** Get Create Purchase Data */
    public function getCreatePurchaseData(int $quotationId): array
    {
        return [
            'suppliers' => $this->supplierService->getSuppliers(),
            'warehouses' => $this->warehouseService->getWarehouses(),
            'taxes' => $this->taxCalculatorService->getTaxes(),
            'quotation' => Quotation::with('productQuotations.product')->findOrFail($quotationId),
            'productsWithoutVariant' => $this->productService->getProductsWithoutVariant(),
            'productsWithVariant' => $this->productService->getProductsWithVariant(),
        ];
    }

    /** delete Quotations */
    public function deleteQuotations(array $quotationIds): bool
    {
        DB::beginTransaction();

        try {
            $quotations = Quotation::whereIn('id', $quotationIds)->get();

            foreach ($quotations as $quotation) {
                // حذف جميع الـ ProductQuotation المرتبطة بالـ Quotation
                ProductQuotation::where('quotation_id', $quotation->id)->delete();

                // حذف المستندات من Media Library
                if ($quotation->hasMedia('documents')) {
                    $quotation->clearMediaCollection('documents');
                }

                // حذف الـ Quotation نفسه
                $quotation->delete();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting quotations: ' . $e->getMessage());
            return false;
        }
    }

    /** delete Quotation By Id */
    public function deleteQuotationById(int $id): bool
    {
        DB::beginTransaction();

        try {
            $quotation = Quotation::findOrFail($id);

            // حذف جميع الـ ProductQuotation المرتبطة بالـ Quotation
            ProductQuotation::where('quotation_id', $id)->delete();

            // حذف المستندات من Media Library
            if ($quotation->hasMedia('documents')) {
                $quotation->clearMediaCollection('documents');
            }

            // حذف الـ Quotation نفسه
            $quotation->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting quotation ID ' . $id . ': ' . $e->getMessage());
            return false;
        }
    }

    /** Get Edit Data */

    public function getEditData($id): array
    {
        $quotation = Quotation::with([
            'customer',
            'warehouse',
            'biller',
            'supplier',
            'productQuotations.product',
            'productQuotations.variant',
            'productQuotations.productBatch'
        ])->findOrFail($id);

        return [
            'lims_biller_list' => $this->billerService->getBillers(),
            'lims_warehouse_list' => $this->warehouseService->getWarehouses(),
            'lims_customer_list' => $this->customerService->getCustomers(),
            'lims_supplier_list' => $this->supplierService->getSuppliers(),
            'lims_tax_list' => $this->taxCalculatorService->getTaxes(),
            'currency' => Currency::find(1),
            'quotation'  => $quotation,
            'products'   => $this->productQuotationService->prepareProductData($quotation)
        ];
    }

    /** Update Quotation */

    public function updateQuotation(int $id, QuotationDTO $dto): string
    {
        try {
            return DB::transaction(function () use ($id, $dto) {
                $quotation = Quotation::findOrFail($id);

                // ✅ تحديث المستند باستخدام Media Library بدلاً من الطرق التقليدية
                if (isset($data['document'])) {
                    $quotation->clearMediaCollection('quotation_documents');
                    $quotation->addMedia($data['document'])->toMediaCollection('quotation_documents');
                }

                $quotation->update($dto->toArray());

                // تحديث أو إنشاء ProductQuotation
                $this->productQuotationService->updateProductQuotations($id, $dto);

                // إرسال البريد إذا كانت الحالة "مرسلة"
                return $this->handleEmailSending($quotation, $dto);
            });
        } catch (\Exception $e) {
            Log::error("Error in quotation update Quotation: " . $e->getMessage());
            throw new \Exception('Error in update Quotation: ' . $e->getMessage());
        }
    }

    private function handleEmailSending(Quotation $quotation, QuotationDTO $dto): string
    {
        if ($quotation->quotation_status !== 2) {
            return __('Quotation updated successfully.');
        }

        $customer = Customer::find($dto->customer_id);
        if (!$customer?->email) {
            return __('Quotation updated successfully.');
        }

        if (!$this->sendMailAction->execute($dto->prepareMailData($customer, $quotation), QuotationDetails::class)) {
            return __('Quotation updated successfully. Please setup your mail settings to send mail.');
        }

        return __('Quotation updated successfully.');
    }



}

