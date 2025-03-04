<?php

namespace App\Services\Tenant;

use App\Actions\SendMailAction;
use App\DTOs\TransferData;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\ProductTransfer;
use App\Models\Transfer;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferService
{

    protected MailService $mailService;
    protected ProductTransferService $productTransferService;

    public function __construct(MailService $mailService,ProductTransferService $productTransferService)
    {
        $this->mailService = $mailService;
        $this->productTransferService = $productTransferService;
    }
    public function getFilters($request)
    {
        return [
            'from_warehouse_id' => $request->input('from_warehouse_id', 0),
            'to_warehouse_id' => $request->input('to_warehouse_id', 0),
            'starting_date' => $request->input('starting_date', date("Y-m-d", strtotime("-1 year"))),
            'ending_date' => $request->input('ending_date', date("Y-m-d")),
            'lims_warehouse_list' => Warehouse::all(),
        ];
    }

    public function getTransfers(array $filters)
    {
        return Transfer::with('fromWarehouse', 'toWarehouse', 'user')
            //->filterByDateRange($filters['starting_date'], $filters['ending_date'])
            ->filterByUserAccess()
            ->fromWarehouse($filters['from_warehouse_id'])
            ->toWarehouse($filters['to_warehouse_id'])
            ->get();

    }

    public function getCreateData()
    {
        return [
            'lims_warehouse_list' => Warehouse::all(),
        ];
    }


    /** Store **/

    public function storeTransferData(TransferData $data, $document)
    {
        return DB::transaction(function () use ($data, $document) {
            $transfer = Transfer::create($data->toArray());

            if ($document) {
                $test = [];
                //$transfer->addMedia($document)->toMediaCollection('transfers');
            }

            $this->productTransferService->processProducts($transfer, $data);
            // تجهيز بيانات البريد
            $mailData = $this->prepareMailData($transfer);

            // إرسال الإيميل وتحديث حالة `is_sent`
            $isSent = $this->mailService->sendTransferMail($mailData);
            $transfer->update(['is_sent' => $isSent]);

            return 'Transfer created successfully';
        });
    }


    private function prepareMailData(Transfer $transfer): array
    {
        return [
            'date'          => $transfer->created_at->toDateString(),
            'reference_no'  => $transfer->reference_no,
            'status'        => $transfer->status,
            'total_cost'    => $transfer->total_cost,
            'shipping_cost' => $transfer->shipping_cost,
            'grand_total'   => $transfer->grand_total,
            'from_email'    => optional($transfer->fromWarehouse)->email,
            'to_email'      => optional($transfer->toWarehouse)->email,
            'from_warehouse'=> optional($transfer->fromWarehouse)->name,
            'to_warehouse'  => optional($transfer->toWarehouse)->name,
            'products'      => $this->productTransferService->getProductTransferDataStore($transfer->id),
        ];
    }


    /** Delete */


    public function deleteTransfer(int $transferId): void
    {
        $transfer = Transfer::with('productTransfers')->findOrFail($transferId);

        DB::transaction(function () use ($transfer) {
            foreach ($transfer->productTransfers as $productTransfer) {
                $this->productTransferService->reverseProductTransfer($productTransfer, $transfer);
                $productTransfer->delete();
            }

            // حذف الملف باستخدام InteractsWithMedia
            if ($transfer->hasMedia('transfers')) {
                $transfer->clearMediaCollection('transfers');
            }
            $transfer->delete();
        });
    }

    public function deleteBySelection(array $transferIds)
    {
        DB::transaction(function () use ($transferIds) {
            foreach ($transferIds as $transferId) {
                $this->deleteTransfer($transferId);
            }
        });
    }

}
