<?php

declare(strict_types=1);

use App\Http\Controllers\AuthTenant\TenantAuthenticatedSessionController;
use App\Http\Controllers\AuthTenant\TenantRegisteredUserController;
use App\Http\Controllers\Tenant\AccountsController;
use App\Http\Controllers\Tenant\AttendanceController;
use App\Http\Controllers\Tenant\CustomFieldController;
use App\Http\Controllers\Tenant\DepartmentController;
use App\Http\Controllers\Tenant\DiscountController;
use App\Http\Controllers\Tenant\DiscountPlanController;
use App\Http\Controllers\Tenant\EmployeeController;
use App\Http\Controllers\Tenant\HolidayController;
use App\Http\Controllers\Tenant\IncomeController;
use App\Http\Controllers\Tenant\AdjustmentController;
use App\Http\Controllers\Tenant\CategoryController;
use App\Http\Controllers\Tenant\ExpenseCategoryController;
use App\Http\Controllers\Tenant\ExpenseController;
use App\Http\Controllers\Tenant\IncomeCategoryController;
use App\Http\Controllers\Tenant\MoneyTransferController;
use App\Http\Controllers\Tenant\NotificationController;
use App\Http\Controllers\Tenant\PayrollController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\PurchaseController;
use App\Http\Controllers\Tenant\QuotationController;
use App\Http\Controllers\Tenant\ReturnController;
use App\Http\Controllers\Tenant\ReturnPurchaseController;
use App\Http\Controllers\Tenant\SmsTemplateController;
use App\Http\Controllers\Tenant\StockCountController;
use App\Http\Controllers\Tenant\SupplierController;
use App\Http\Controllers\Tenant\BillerController;
use App\Http\Controllers\Tenant\CustomerController;
use App\Http\Controllers\Tenant\HomeController;
use App\Http\Controllers\Tenant\RoleController;
use App\Http\Controllers\Tenant\SettingController;
use App\Http\Controllers\Tenant\TableController;
use App\Http\Controllers\Tenant\TransferController;
use App\Http\Controllers\Tenant\UserController;
use App\Http\Controllers\Tenant\WarehouseController;
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
    Route::get('/documentation', [HomeController::class, 'documentation']);
    Route::get('/ecommerce-documentation', [HomeController::class, 'ecomDocumentation']);

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


            Route::controller(TransferController::class)->group(function () {
                Route::prefix('transfers')->group(function () {
                    Route::post('transfer-data', 'transferData')->name('transfers.data');
                    Route::get('product_transfer/{id}', 'productTransferData');
                    Route::get('transfer_by_csv', 'transferByCsv');
                    Route::get('getproduct/{id}', 'getProduct')->name('transfer.getproduct');
                    Route::get('lims_product_search', 'limsProductSearch')->name('product_transfer.search');
                    Route::post('deletebyselection', 'deleteBySelection');
                });
                Route::post('importtransfer', 'importTransfer')->name('transfer.import');
            });
            Route::resource('transfers', TransferController::class);




            Route::controller(ReturnController::class)->group(function () {
                Route::prefix('return-sale')->group(function () {
                    Route::post('return-data', 'returnData');
                    Route::get('getcustomergroup/{id}', 'getCustomerGroup')->name('return-sale.getcustomergroup');
                    Route::post('sendmail', 'sendMail')->name('return-sale.sendmail');
                    Route::get('getproduct/{id}', 'getProduct')->name('return-sale.getproduct');
                    Route::get('lims_product_search', 'limsProductSearch')->name('product_return-sale.search');
                    Route::get('product_return/{id}', 'productReturnData');
                    Route::post('deletebyselection', 'deleteBySelection');
                });
            });
            Route::resource('return-sale', ReturnController::class);

            Route::controller(CashRegisterController::class)->group(function () {
                Route::prefix('cash-register')->group(function () {
                    Route::get('/', 'index')->name('cashRegister.index');
                    Route::get('check-availability/{warehouse_id}', 'checkAvailability')->name('cashRegister.checkAvailability');
                    Route::post('store', 'store')->name('cashRegister.store');
                    Route::get('getDetails/{id}', 'getDetails');
                    Route::get('showDetails/{warehouse_id}', 'showDetails');
                    Route::post('close', 'close')->name('cashRegister.close');
                });
            });

            Route::controller(ReturnPurchaseController::class)->group(function () {
                Route::prefix('return-purchase')->group(function () {
                    Route::post('return-data', 'returnData');
                    Route::get('getcustomergroup/{id}', 'getCustomerGroup')->name('return-purchase.getcustomergroup');
                    Route::post('sendmail', 'sendMail')->name('return-purchase.sendmail');
                    Route::get('getproduct/{id}', 'getProduct')->name('return-purchase.getproduct');
                    Route::get('lims_product_search', 'limsProductSearch')->name('product_return-purchase.search');
                    Route::get('product_return/{id}', 'productReturnData');
                    Route::post('deletebyselection', 'deleteBySelection');
                });
            });
            Route::resource('return-purchase', ReturnPurchaseController::class);


            //accounting routes
            Route::controller(AccountsController::class)->group(function () {
                Route::prefix('accounts')->group(function () {

                Route::get('make-default/{id}', 'makeDefault');
                Route::get('balancesheet', 'balanceSheet')->name('accounts.balancesheet');
                Route::post('account-statement', 'accountStatement')->name('accounts.statement');
                Route::get('accounts/all', 'accountsAll')->name('account.all');
                });
            });
            Route::resource('accounts', AccountsController::class);



            Route::resource('money-transfers', MoneyTransferController::class);


            //HRM routes
            Route::post('departments/deletebyselection', [DepartmentController::class,'deleteBySelection']);
            Route::resource('departments', DepartmentController::class);


            Route::post('employees/deletebyselection', [EmployeeController::class, 'deleteBySelection']);
            Route::resource('employees', EmployeeController::class);


            Route::post('payroll/deletebyselection', [PayrollController::class, 'deleteBySelection']);
            Route::resource('payroll', PayrollController::class);


            Route::post('attendance/delete/{date}/{employee_id}', [AttendanceController::class, 'delete'])->name('attendances.delete');
            Route::post('attendance/deletebyselection', [AttendanceController::class, 'deleteBySelection']);
            Route::post('attendance/importDeviceCsv', [AttendanceController::class, 'importDeviceCsv'])->name('attendances.importDeviceCsv');
            Route::resource('attendance', AttendanceController::class);

            Route::controller(HolidayController::class)->group(function () {
                Route::post('holidays/deletebyselection', 'deleteBySelection');
                Route::get('approve-holiday/{id}', 'approveHoliday')->name('approveHoliday');
                Route::get('holidays/my-holiday/{year}/{month}', 'myHoliday')->name('myHoliday');
            });
            Route::resource('holidays', HolidayController::class);

            //Sms Template
            Route::resource('smstemplates',SmsTemplateController::class);
            Route::resource('unit', UnitController::class);
            Route::controller(UnitController::class)->group(function () {
                Route::post('importunit', 'importUnit')->name('unit.import');
                Route::post('unit/deletebyselection', 'deleteBySelection');
                Route::get('unit/lims_unit_search', 'limsUnitSearch')->name('unit.search');
            });

            Route::resource('currency', CurrencyController::class);

            Route::resource('custom-fields', CustomFieldController::class);


            Route::controller(WarehouseController::class)->group(function () {
                Route::post('importwarehouse', 'importWarehouse')->name('warehouse.import');
                Route::post('warehouse/deletebyselection', 'deleteBySelection');
                Route::get('warehouse/lims_warehouse_search', 'limsWarehouseSearch')->name('warehouse.search');
                Route::get('warehouse/all', 'warehouseAll')->name('warehouse.all');
            });
            Route::resource('warehouse', WarehouseController::class);


            Route::resource('tables', TableController::class);

            Route::resource('discount-plans', DiscountPlanController::class);
            Route::resource('discounts', DiscountController::class);
            Route::get('discounts/product-search/{code}', [DiscountController::class,'productSearch']);

            Route::controller(NotificationController::class)->group(function () {
                Route::prefix('notifications')->group(function () {
                    Route::get('/', 'index')->name('notifications.index');
                    Route::post('store', 'store')->name('notifications.store');
                    Route::get('mark-as-read', 'markAsRead');
                });
            });
            Route::controller(SaasInstallController::class)->group(function () {
                Route::prefix('saas')->group(function () {
                    Route::get('install/step-1', 'saasInstallStep1')->name('saas-install-step-1');
                    Route::get('install/step-2', 'saasInstallStep2')->name('saas-install-step-2');
                    Route::get('install/step-3', 'saasInstallStep3')->name('saas-install-step-3');
                    Route::post('install/process', 'saasInstallProcess')->name('saas-install-process');
                    Route::get('install/step-4', 'saasInstallStep4')->name('saas-install-step-4');
                });
            });

            Route::post('woocommerce-install', [AddonInstallController::class,'woocommerceInstall'])->name('woocommerce.install');

            Route::post('ecommerce-install', [AddonInstallController::class,'ecommerceInstall'])->name('ecommerce.install');

            Route::controller(CustomerGroupController::class)->group(function () {
                Route::post('importcustomer_group', 'importCustomerGroup')->name('customer_group.import');
                Route::post('customer_group/deletebyselection', 'deleteBySelection');
                Route::get('customer_group/lims_customer_group_search', 'limsCustomerGroupSearch')->name('customer_group.search');
                Route::get('customer_group/all', 'customerGroupAll')->name('customer_group.all');
            });
            Route::resource('customer_group', CustomerGroupController::class);


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
