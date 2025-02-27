<?php

declare(strict_types=1);

use App\Http\Controllers\AuthTenant\TenantAuthenticatedSessionController;
use App\Http\Controllers\AuthTenant\TenantRegisteredUserController;
use App\Http\Controllers\Tenant\IncomeController;
use App\Http\Controllers\Tenant\AdjustmentController;
use App\Http\Controllers\Tenant\CategoryController;
use App\Http\Controllers\Tenant\ExpenseCategoryController;
use App\Http\Controllers\Tenant\ExpenseController;
use App\Http\Controllers\Tenant\IncomeCategoryController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\PurchaseController;
use App\Http\Controllers\Tenant\StockCountController;
use App\Http\Controllers\Tenant\SupplierController;
use App\Http\Controllers\Tenant\BillerController;
use App\Http\Controllers\Tenant\CustomerController;
use App\Http\Controllers\Tenant\HomeController;
use App\Http\Controllers\Tenant\RoleController;
use App\Http\Controllers\Tenant\SettingController;
use App\Http\Controllers\Tenant\UserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return 'Welcome to your store! Tenant ID: ' . tenant('id');
    });

    Route::group(['middleware' => ['common']], function () {

        Route::group(['middleware' => 'auth:web'], function() {
            Route::controller(HomeController::class)->group(function () {
                Route::get('home', 'home');
            });
        });



        Route::group(['middleware' => ['auth:web', 'active']], function() {


            Route::controller(HomeController::class)->group(function () {
                Route::get('/dashboard', 'index')->name('tenant.dashboard');
                Route::get('switch-theme/{theme}', 'switchTheme')->name('switchTheme');
            });

            Route::resource('role',RoleController::class);
            Route::controller(RoleController::class)->group(function () {
                Route::get('role/permission/{id}', 'permission')->name('role.permission');
                Route::post('role/set_permission', 'setPermission')->name('role.setPermission');
            });

            Route::controller(UserController::class)->group(function () {
                Route::get('user/profile/{user}', 'profile')->name('user.profile');
                Route::put('user/update_profile/{user}', 'profileUpdate')->name('user.profileUpdate');
                Route::put('user/changepass/{id}', 'changePassword')->name('user.password');
                Route::post('user/deletebyselection', 'deleteBySelection');
                Route::get('user/notification', 'notificationUsers')->name('user.notification');
                Route::get('user/all', 'allUsers')->name('user.all');
                Route::get('user/Trashed', 'indexTrashed')->name('user.Trashed');
                Route::post('users/{id}restore', 'restore')->name('users.restore');

            });
            Route::resource('user', UserController::class);


            Route::controller(CustomerController::class)->group(function () {
                Route::post('importcustomer', 'importCustomer')->name('customer.import');
                Route::get('customer/getDeposit/{id}', 'getDeposit');
                Route::post('customer/add_deposit', 'addDeposit')->name('customer.addDeposit');
                Route::post('customer/update_deposit', 'updateDeposit')->name('customer.updateDeposit');
                Route::post('customer/deleteDeposit', 'deleteDeposit')->name('customer.deleteDeposit');
                Route::post('customer/deletebyselection', 'deleteBySelection');
                Route::get('customer/lims_customer_search', 'limsCustomerSearch')->name('customer.search');
                Route::post('customers/clear-due', 'clearDue')->name('customer.clearDue');
                Route::post('customers/customer-data', 'customerData');
                Route::get('customers/all', 'customersAll')->name('customer.all');
            });
            Route::resource('customer', CustomerController::class)->except('show');


            Route::controller(BillerController::class)->group(function () {
                Route::post('importbiller', 'importBiller')->name('biller.import');
                Route::post('biller/deletebyselection', 'deleteBySelection');
                Route::get('biller/lims_biller_search', 'limsBillerSearch')->name('biller.search');
                Route::get('biller/Trashed', 'indexTrashed')->name('biller.Trashed');
                Route::post('biller/{id}restore', 'restore')->name('biller.restore');
            });
            Route::resource('biller', BillerController::class);


            Route::controller(SupplierController::class)->group(function () {
                Route::post('importsupplier', 'importSupplier')->name('supplier.import');
                Route::post('supplier/deletebyselection', 'deleteBySelection');
                Route::post('suppliers/clear-due', 'clearDue')->name('supplier.clearDue');
                Route::get('suppliers/all', 'suppliersAll')->name('supplier.all');
            });
            Route::resource('supplier', SupplierController::class)->except('show');

            Route::resource('products',ProductController::class)->except([ 'show']);
            Route::controller(ProductController::class)->group(function () {
                Route::post('products/product-data', 'productData');
                Route::get('products/gencode', 'generateCode');
                Route::get('products/search', 'search');
                Route::get('products/saleunit/{id}', 'saleUnit');
                Route::get('products/getdata/{id}/{variant_id}', 'getData');
                Route::get('products/product_warehouse/{id}', 'productWarehouseData');
                Route::get('products/print_barcode','printBarcode')->name('product.printBarcode');
                Route::get('products/lims_product_search', 'limsProductSearch')->name('product.search');
                Route::post('products/deletebyselection', 'deleteBySelection');
                Route::post('products/update', 'updateProduct');
                Route::get('products/variant-data/{id}','variantData');
                Route::get('products/history', 'history')->name('products.history');
                Route::post('products/sale-history-data', 'saleHistoryData');
                Route::post('products/purchase-history-data', 'purchaseHistoryData');
                Route::post('products/sale-return-history-data', 'saleReturnHistoryData');
                Route::post('products/purchase-return-history-data', 'purchaseReturnHistoryData');

                Route::post('importproduct', 'importProduct')->name('product.import');
                Route::post('exportproduct', 'exportProduct')->name('product.export');
                Route::get('products/all-product-in-stock', 'allProductInStock')->name('product.allProductInStock');
                Route::get('products/show-all-product-online', 'showAllProductOnline')->name('product.showAllProductOnline');
                Route::get('check-batch-availability/{product_id}/{batch_no}/{warehouse_id}', 'checkBatchAvailability');
            });

            Route::controller(TaxController::class)->group(function () {
                Route::post('importtax', 'importTax')->name('tax.import');
                Route::post('tax/deletebyselection', 'deleteBySelection');
                Route::get('tax/lims_tax_search', 'limsTaxSearch')->name('tax.search');
            });
            Route::resource('tax', TaxController::class);


            Route::controller(BrandController::class)->group(function () {
                Route::post('importbrand', 'importBrand')->name('brand.import');
                Route::post('brand/deletebyselection', 'deleteBySelection');
                Route::get('brand/lims_brand_search', 'limsBrandSearch')->name('brand.search');
            });
            Route::resource('brand', BrandController::class);

            Route::controller(CategoryController::class)->group(function () {
                Route::post('category/import', 'import')->name('category.import');
                Route::post('category/deletebyselection', 'deleteBySelection');
                Route::post('category/category-data', 'categoryData');
            });
            Route::resource('category', CategoryController::class);


            Route::controller(AdjustmentController::class)->group(function () {
                Route::get('qty_adjustment/getproduct/{id}', 'getProduct')->name('adjustment.getproduct');
                Route::get('qty_adjustment/lims_product_search', 'limsProductSearch')->name('product_adjustment.search');
                Route::post('qty_adjustment/deletebyselection', 'deleteBySelection');
            });
            Route::resource('qty_adjustment', AdjustmentController::class);

            Route::controller(StockCountController::class)->group(function () {
                Route::post('stock-count/finalize', 'finalize')->name('stock-count.finalize');
                Route::get('stock-count/stockdif/{id}', 'stockDif');
                Route::get('stock-count/{id}/qty_adjustment', 'qtyAdjustment')->name('stock-count.adjustment');
            });

            Route::get('/stock-count/{stockCount}/download-initial', [StockCountController::class, 'downloadInitialFile'])
                ->name('stock-count.download-initial');

            Route::get('/stock-count/{stockCount}/download-final', [StockCountController::class, 'downloadFinalFile'])
                ->name('stock-count.download-final');

            Route::resource('stock-count', StockCountController::class);

            Route::controller(PurchaseController::class)->group(function () {
                Route::prefix('purchases')->group(function () {
                    Route::post('purchase-data', 'purchaseData')->name('purchases.data');
                    Route::get('product_purchase/{id}', 'productPurchaseData');
                    Route::get('lims_product_search', 'limsProductSearch')->name('product_purchase.search');
                    Route::post('add_payment', 'addPayment')->name('purchase.add-payment');
                    Route::get('getpayment/{id}', 'getPayment')->name('purchase.get-payment');
                    Route::post('updatepayment', 'updatePayment')->name('purchase.update-payment');
                    Route::post('deletepayment', 'deletePayment')->name('purchase.delete-payment');
                    Route::get('purchase_by_csv', 'purchaseByCsv');
                    Route::get('duplicate/{id}', 'duplicate')->name('purchase.duplicate');
                    Route::post('deletebyselection', 'deleteBySelection');
                });
                Route::post('importpurchase', 'importPurchase')->name('purchase.import');
            });
            Route::resource('purchases', PurchaseController::class);



            Route::controller(ExpenseCategoryController::class)->group(function () {
                Route::get('expense_categories/gencode', 'generateCode');
                Route::post('expense_categories/import', 'import')->name('expense_category.import');
                Route::post('expense_categories/deletebyselection', 'deleteBySelection');
                Route::get('expense_categories/all', 'expenseCategoriesAll')->name('expense_category.all');;
            });
            Route::resource('expense_categories', ExpenseCategoryController::class);


            Route::controller(ExpenseController::class)->group(function () {
                Route::post('expenses/expense-data', 'expenseData')->name('expenses.data');
                Route::post('expenses/deletebyselection', 'deleteBySelection');
            });
            Route::resource('expenses', ExpenseController::class);


            // IncomeCategory & Income Start
            Route::controller(IncomeCategoryController::class)->group(function () {
                Route::get('income_categories/gencode', 'generateCode');
                Route::post('income_categories/import', 'import')->name('income_category.import');
                Route::post('income_categories/deletebyselection', 'deleteBySelection');
                Route::get('income_categories/all', 'incomeCategoriesAll')->name('income_category.all');;
            });
            Route::resource('income_categories', IncomeCategoryController::class);


            Route::controller(IncomeController::class)->group(function () {
                Route::post('incomes/income-data', 'incomeData')->name('incomes.data');
                Route::post('incomes/deletebyselection', 'deleteBySelection');
            });
            Route::resource('incomes', IncomeController::class);
            // IncomeCategory & Income End

            Route::controller(QuotationController::class)->group(function () {
                Route::prefix('quotations')->group(function () {
                    Route::post('quotation-data', 'quotationData')->name('quotations.data');
                    Route::get('product_quotation/{id}','productQuotationData');
                    Route::get('lims_product_search', 'limsProductSearch')->name('product_quotation.search');
                    Route::get('getcustomergroup/{id}', 'getCustomerGroup')->name('quotation.getcustomergroup');
                    Route::get('getproduct/{id}', 'getProduct')->name('quotation.getproduct');
                    Route::get('{id}/create_sale', 'createSale')->name('quotation.create_sale');
                    Route::get('{id}/create_purchase', 'createPurchase')->name('quotation.create_purchase');
                    Route::post('sendmail', 'sendMail')->name('quotation.sendmail');
                    Route::post('deletebyselection', 'deleteBySelection');
                });
            });
            Route::resource('quotations', QuotationController::class);

            Route::controller(SettingController::class)->group(function () {
                Route::prefix('setting')->group(function () {
                    Route::get('general_setting', 'generalSetting')->name('setting.general');
                    Route::post('general_setting_store', 'generalSettingStore')->name('setting.generalStore');

                    Route::get('reward-point-setting', 'rewardPointSetting')->name('setting.rewardPoint');
                    Route::post('reward-point-setting_store', 'rewardPointSettingStore')->name('setting.rewardPointStore');

                    Route::get('general_setting/change-theme/{theme}', 'changeTheme');
                    Route::get('mail_setting', 'mailSetting')->name('setting.mail');
                    Route::get('sms_setting', 'smsSetting')->name('setting.sms');
                    Route::get('createsms', 'createSms')->name('setting.createSms');
                    Route::post('sendsms', 'sendSMS')->name('setting.sendSms');
                    Route::get('hrm_setting', 'hrmSetting')->name('setting.hrm');
                    Route::post('hrm_setting_store', 'hrmSettingStore')->name('setting.hrmStore');
                    Route::post('mail_setting_store', 'mailSettingStore')->name('setting.mailStore');
                    Route::post('sms_setting_store', 'smsSettingStore')->name('setting.smsStore');
                    Route::get('pos_setting', 'posSetting')->name('setting.pos');
                    Route::post('pos_setting_store', 'posSettingStore')->name('setting.posStore');
                    Route::get('empty-database', 'emptyDatabase')->name('setting.emptyDatabase');
                });
                Route::get('backup', 'backup')->name('setting.backup');
            });


            Route::controller(ReportController::class)->group(function () {
                Route::prefix('report')->group(function () {
                    Route::get('product_quantity_alert', 'productQuantityAlert')->name('report.qtyAlert');
                    Route::get('daily-sale-objective', 'dailySaleObjective')->name('report.dailySaleObjective');
                    Route::post('daily-sale-objective-data', 'dailySaleObjectiveData');
                    Route::get('product-expiry', 'productExpiry')->name('report.productExpiry');
                    Route::get('warehouse_stock', 'warehouseStock')->name('report.warehouseStock');
                    Route::get('daily_sale/{year}/{month}', 'dailySale');
                    Route::post('daily_sale/{year}/{month}', 'dailySaleByWarehouse')->name('report.dailySaleByWarehouse');
                    Route::get('monthly_sale/{year}', 'monthlySale');
                    Route::post('monthly_sale/{year}', 'monthlySaleByWarehouse')->name('report.monthlySaleByWarehouse');
                    Route::get('daily_purchase/{year}/{month}', 'dailyPurchase');
                    Route::post('daily_purchase/{year}/{month}', 'dailyPurchaseByWarehouse')->name('report.dailyPurchaseByWarehouse');
                    Route::get('monthly_purchase/{year}', 'monthlyPurchase');
                    Route::post('monthly_purchase/{year}', 'monthlyPurchaseByWarehouse')->name('report.monthlyPurchaseByWarehouse');
                    Route::get('best_seller', 'bestSeller');
                    Route::post('best_seller', 'bestSellerByWarehouse')->name('report.bestSellerByWarehouse');
                    Route::post('profit_loss', 'profitLoss')->name('report.profitLoss');
                    Route::get('product_report', 'productReport')->name('report.product');
                    Route::post('product_report_data', 'productReportData');
                    Route::post('purchase', 'purchaseReport')->name('report.purchase');
                    Route::post('sale_report', 'saleReport')->name('report.sale');
                    Route::get('challan-report', 'challanReport')->name('report.challan');
                    Route::post('sale-report-chart', 'saleReportChart')->name('report.saleChart');
                    Route::post('payment_report_by_date', 'paymentReportByDate')->name('report.paymentByDate');
                    Route::post('warehouse_report', 'warehouseReport')->name('report.warehouse');
                    Route::post('warehouse-sale-data', 'warehouseSaleData');
                    Route::post('warehouse-purchase-data', 'warehousePurchaseData');
                    Route::post('warehouse-expense-data', 'warehouseExpenseData');
                    Route::post('warehouse-quotation-data', 'warehouseQuotationData');
                    Route::post('warehouse-return-data', 'warehouseReturnData');
                    Route::post('user_report', 'userReport')->name('report.user');
                    Route::post('user-sale-data', 'userSaleData');
                    Route::post('user-purchase-data', 'userPurchaseData');
                    Route::post('user-expense-data', 'userExpenseData');
                    Route::post('user-quotation-data', 'userQuotationData');
                    Route::post('user-payment-data', 'userPaymentData');
                    Route::post('user-transfer-data', 'userTransferData');
                    Route::post('user-payroll-data', 'userPayrollData');
                    Route::post('biller_report', 'billerReport')->name('report.biller');
                    Route::post('biller-sale-data','billerSaleData');
                    Route::post('biller-quotation-data','billerQuotationData');
                    Route::post('biller-payment-data','billerPaymentData');
                    Route::post('customer_report', 'customerReport')->name('report.customer');
                    Route::post('customer-sale-data', 'customerSaleData');
                    Route::post('customer-payment-data', 'customerPaymentData');
                    Route::post('customer-quotation-data', 'customerQuotationData');
                    Route::post('customer-return-data', 'customerReturnData');
                    Route::post('customer-group', 'customerGroupReport')->name('report.customer_group');
                    Route::post('customer-group-sale-data', 'customerGroupSaleData');
                    Route::post('customer-group-payment-data', 'customerGroupPaymentData');
                    Route::post('customer-group-quotation-data', 'customerGroupQuotationData');
                    Route::post('customer-group-return-data', 'customerGroupReturnData');
                    Route::post('supplier', 'supplierReport')->name('report.supplier');
                    Route::post('supplier-purchase-data', 'supplierPurchaseData');
                    Route::post('supplier-payment-data', 'supplierPaymentData');
                    Route::post('supplier-return-data', 'supplierReturnData');
                    Route::post('supplier-quotation-data', 'supplierQuotationData');
                    Route::post('customer-due-report', 'customerDueReportByDate')->name('report.customerDueByDate');
                    Route::post('customer-due-report-data', 'customerDueReportData');
                    Route::post('supplier-due-report', 'supplierDueReportByDate')->name('report.supplierDueByDate');
                    Route::post('supplier-due-report-data', 'supplierDueReportData');
                });
            });

        });
        require __DIR__ . '/authTenant.php';
    });

});
