<ul id="side-main-menu" class="side-menu list-unstyled d-print-none">
    <li><a href="{{url('/dashboard')}}"> <i class="dripicons-meter"></i><span>{{ __('file.dashboard') }}</span></a></li>



    <li><a href="#purchase" aria-expanded="false" data-toggle="collapse"> <i
                class="dripicons-card"></i><span>{{trans('file.Purchase')}}</span></a>
        <ul id="purchase" class="collapse list-unstyled ">
            <li id="purchase-list-menu"><a href="{{route('purchases.index')}}">{{trans('file.Purchase List')}}</a></li>

            <li id="purchase-create-menu"><a href="{{route('purchases.create')}}">{{trans('file.Add Purchase')}}</a>
            </li>
            <li id="purchase-import-menu"><a
                    href="{{url('purchases/purchase_by_csv')}}">{{trans('file.Import Purchase By CSV')}}</a></li>

        </ul>
    </li>
    <li><a href="#sale" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-cart"></i><span>{{trans('file.Sale')}}</span></a>
        <ul id="sale" class="collapse list-unstyled ">
{{--            <li id="sale-list-menu"><a href="{{route('sales.index')}}">{{trans('file.Sale List')}}</a></li>--}}
{{--            <li><a href="{{route('sale.pos')}}">POS</a></li>--}}
{{--            <li id="sale-create-menu"><a href="{{route('sales.create')}}">{{trans('file.Add Sale')}}</a></li>--}}
{{--            <li id="sale-import-menu"><a href="{{url('sales/sale_by_csv')}}">{{trans('file.Import Sale By CSV')}}</a>--}}
{{--            </li>--}}
            <li id="packing-list-menu"><a href="{{route('packingSlip.index')}}">{{trans('file.Packing Slip List')}}</a>
            </li>
            <li id="challan-list-menu"><a href="{{route('challan.index')}}">{{trans('file.Challan List')}}</a></li>
            <li id="delivery-menu"><a href="{{route('delivery.index')}}">{{trans('file.Delivery List')}}</a></li>
            <li id="gift-card-menu"><a href="{{route('gift_cards.index')}}">{{trans('file.Gift Card List')}}</a></li>
            <li id="coupon-menu"><a href="{{route('coupons.index')}}">{{trans('file.Coupon List')}}</a></li>
            <li id="courier-menu"><a href="{{route('couriers.index')}}">{{trans('file.Courier List')}}</a></li>
        </ul>
    </li>

    <li>
        <a href="#product" aria-expanded="false" data-toggle="collapse">
            <i
                class="dripicons-list"></i>
            <span>{{__('file.product')}}</span>
        </a>

        <ul id="product" class="collapse list-unstyled ">
            @can('category')
                <li id="category-menu"><a href="{{route('category.index')}}">{{__('file.category')}}</a></li>
            @endcan
            @can('products-index')
                <li id="product-list-menu"><a href="{{route('products.index')}}">{{__('file.product_list')}}</a>
                </li>

                @can('products-add')
                    <li id="product-create-menu"><a
                            href="{{route('products.create')}}">{{__('file.add_product')}}</a></li>
                @endcan
            @endcan
            @can('print_barcode')
                <li id="printBarcode-menu"><a
                        href="{{route('product.printBarcode')}}">{{__('file.print_barcode')}}</a></li>
            @endcan
            @can('adjustment')
                <li id="adjustment-list-menu"><a
                        href="{{route('qty_adjustment.index')}}">{{trans('file.Adjustment List')}}</a></li>
                <li id="adjustment-create-menu"><a
                        href="{{route('qty_adjustment.create')}}">{{trans('file.Add Adjustment')}}</a></li>
            @endcan
            @can('stock_count')
                <li id="stock-count-menu"><a
                        href="{{route('stock-count.index')}}">{{trans('file.Stock Count')}}</a></li>
            @endcan
        </ul>
    </li>
    @can("expenses-index")
        <li><a href="#expense" aria-expanded="false" data-toggle="collapse"> <i
                    class="dripicons-wallet"></i><span>{{trans('file.Expense')}}</span></a>
            <ul id="expense" class="collapse list-unstyled ">
                <li id="exp-cat-menu"><a
                        href="{{route('expense_categories.index')}}">{{trans('file.Expense Category')}}</a></li>
                <li id="exp-list-menu"><a href="{{route('expenses.index')}}">{{trans('file.Expense List')}}</a></li>

                @can("expenses-add")
                    <li><a id="add-expense" href=""> {{trans('file.Add Expense')}}</a></li>
                @endcan
            </ul>
        </li>
    @endcan
    @can("incomes-index")
        <li><a href="#income" aria-expanded="false" data-toggle="collapse"> <i
                    class="dripicons-rocket"></i><span>{{trans('file.Income')}}</span></a>
            <ul id="income" class="collapse list-unstyled ">
                <li id="income-cat-menu"><a
                        href="{{route('income_categories.index')}}">{{trans('file.Income Category')}}</a></li>
                <li id="income-list-menu"><a href="{{route('incomes.index')}}">{{trans('file.Income List')}}</a></li>

                @can("incomes-add")
                    <li><a id="add-income" href=""> {{trans('file.Add Income')}}</a></li>
                @endcan
            </ul>
        </li>
    @endcan

    @can("quotes-index")
        <li><a href="#quotation" aria-expanded="false" data-toggle="collapse"> <i
                    class="dripicons-document"></i><span>{{trans('file.Quotation')}}</span></a>
            <ul id="quotation" class="collapse list-unstyled ">
                <li id="quotation-list-menu"><a
                        href="{{route('quotations.index')}}">{{trans('file.Quotation List')}}</a></li>

                @can("quotes-add")
                    <li id="quotation-create-menu"><a
                            href="{{route('quotations.create')}}">{{trans('file.Add Quotation')}}</a></li>
                @endcan
            </ul>
        </li>
    @endcan



    @can("transfers-index")
        <li><a href="#transfer" aria-expanded="false" data-toggle="collapse"> <i
                    class="dripicons-export"></i><span>{{trans('file.Transfer')}}</span></a>
            <ul id="transfer" class="collapse list-unstyled ">
                <li id="transfer-list-menu"><a href="{{route('transfers.index')}}">{{trans('file.Transfer List')}}</a>
                </li>
                @can("transfers-add")
                    <li id="transfer-create-menu"><a
                            href="{{route('transfers.create')}}">{{trans('file.Add Transfer')}}</a></li>
                    <li id="transfer-import-menu"><a
                            href="{{url('transfers/transfer_by_csv')}}">{{trans('file.Import Transfer By CSV')}}</a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcan

    @can("returns-index","purchase-return-index")
        <li><a href="#return" aria-expanded="false" data-toggle="collapse"> <i
                    class="dripicons-return"></i><span>{{trans('file.return')}}</span></a>
            <ul id="return" class="collapse list-unstyled ">
                @can("returns-index")
                    <li id="sale-return-menu"><a href="{{route('return-sale.index')}}">{{trans('file.Sale')}}</a></li>
                @endcan
                @can("purchase-return-index")
                    <li id="purchase-return-menu"><a
                            href="{{route('return-purchase.index')}}">{{trans('file.Purchase')}}</a></li>
                @endcan
            </ul>
        </li>
    @endcan
    @can("account-index", "money-transfer" , "balance-sheet" ,"account-statement")
        <li class=""><a href="#account" aria-expanded="false" data-toggle="collapse"> <i
                    class="dripicons-briefcase"></i><span>{{trans('file.Accounting')}}</span></a>
            <ul id="account" class="collapse list-unstyled ">
                @can("account-index")
                    <li id="account-list-menu"><a href="{{route('accounts.index')}}">{{trans('file.Account List')}}</a>
                    </li>
                    <li><a id="add-account" href="">{{trans('file.Add Account')}}</a></li>
                @endcan
                @can("money-transfer")
                    <li id="money-transfer-menu"><a
                            href="{{route('money-transfers.index')}}">{{trans('file.Money Transfer')}}</a></li>
                @endcan
                @can("balance-sheet")
                    <li id="balance-sheet-menu"><a
                            href="{{route('accounts.balancesheet')}}">{{trans('file.Balance Sheet')}}</a></li>
                @endcan
                @can("account-statement")
                    <li id="account-statement-menu"><a id="account-statement"
                                                       href="">{{trans('file.Account Statement')}}</a></li>
                @endcan
            </ul>
        </li>
    @endcan

    <li class=""><a href="#hrm" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-user-group"></i><span>HRM</span></a>
        <ul id="hrm" class="collapse list-unstyled ">
            @can("department")
                <li id="dept-menu"><a href="{{route('departments.index')}}">{{trans('file.Department')}}</a></li>
            @endcan
            @can("employees-index")
                <li id="employee-menu"><a href="{{route('employees.index')}}">{{trans('file.Employee')}}</a></li>
            @endcan
            @can("attendance")
                <li id="attendance-menu"><a href="{{route('attendance.index')}}">{{trans('file.Attendance')}}</a></li>
            @endcan
            @can("payroll")
                <li id="payroll-menu"><a href="{{route('payroll.index')}}">{{trans('file.Payroll')}}</a></li>
            @endcan
            @can("holiday")
                <li id="holiday-menu"><a href="{{route('holidays.index')}}">{{trans('file.Holiday')}}</a></li>
            @endcan
        </ul>
    </li>


    <li><a href="#people" aria-expanded="false" data-toggle="collapse"> <i
                class="dripicons-user"></i><span>{{trans('file.People')}}</span></a>
        <ul id="people" class="collapse list-unstyled ">
            @can('users-index')
                <li id="user-list-menu"><a href="{{route('user.index')}}">{{trans('file.User List')}}</a></li>
                <li id="user-trash-menu"><a href="{{route('user.Trashed')}}">User List Trashed</a></li>

                @can('users-add')
                    <li id="user-create-menu"><a href="{{route('user.create')}}">{{trans('file.Add User')}}</a></li>
                @endcan
            @endcan

            @can('customers-index')
                <li id="customer-list-menu"><a href="{{route('customer.index')}}">{{trans('file.Customer List')}}</a>
                </li>
                @can('customers-add')
                    <li id="customer-create-menu"><a
                            href="{{route('customer.create')}}">{{trans('file.Add Customer')}}</a></li>
                @endcan
            @endcan

            @can('billers-index')
                <li id="biller-list-menu"><a
                        href="{{route('biller.index')}}">{{trans('file.Biller List')}}</a></li>
                <li id="biller-trash-menu"><a href="{{route('biller.Trashed')}}">Biller List Trashed</a></li>
                @can('billers-add')
                    <li id="biller-create-menu"><a
                            href="{{route('biller.create')}}">{{trans('file.Add Biller')}}</a></li>
                @endcan
            @endcan

            @can('suppliers-index')
                <li id="supplier-list-menu"><a href="{{route('supplier.index')}}">{{trans('file.Supplier List')}}</a>
                </li>
                @can('suppliers-add')
                    <li id="supplier-create-menu"><a
                            href="{{route('supplier.create')}}">{{trans('file.Add Supplier')}}</a></li>
                @endcan
            @endcan
        </ul>
    </li>
    <li><a href="#report" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-document-remove"></i><span>{{trans('file.Reports')}}</span></a>
        <ul id="report" class="collapse list-unstyled ">
                <li id="profit-loss-report-menu">

                    {!! Form::open(['route' => 'report.profitLoss', 'method' => 'post', 'id' => 'profitLoss-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <a id="profitLoss-link" href="">{{trans('file.Summary Report')}}</a>

                    {!! Form::close() !!}
                </li>

                <li id="best-seller-report-menu">
                    <a href="{{url('report/best_seller')}}">{{trans('file.Best Seller')}}</a>
                </li>
                <li id="product-report-menu">
                    {!! Form::open(['route' => 'report.product', 'method' => 'get', 'id' => 'product-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <input type="hidden" name="warehouse_id" value="0" />
                    <a id="report-link" href="">{{trans('file.Product Report')}}</a>
                    {!! Form::close() !!}
                </li>
                <li id="daily-sale-report-menu">
                    <a href="{{url('report/daily_sale/'.date('Y').'/'.date('m'))}}">{{trans('file.Daily Sale')}}</a>
                </li>
                <li id="monthly-sale-report-menu">
                    <a href="{{url('report/monthly_sale/'.date('Y'))}}">{{trans('file.Monthly Sale')}}</a>
                </li>
                <li id="daily-purchase-report-menu">
                    <a href="{{url('report/daily_purchase/'.date('Y').'/'.date('m'))}}">{{trans('file.Daily Purchase')}}</a>
                </li>

                <li id="monthly-purchase-report-menu">
                    <a href="{{url('report/monthly_purchase/'.date('Y'))}}">{{trans('file.Monthly Purchase')}}</a>
                </li>

                <li id="sale-report-menu">
                    {!! Form::open(['route' => 'report.sale', 'method' => 'post', 'id' => 'sale-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <input type="hidden" name="warehouse_id" value="0" />
                    <a id="sale-report-link" href="">{{trans('file.Sale Report')}}</a>
                    {!! Form::close() !!}
                </li>

            <li id="challan-report-menu"><a href="{{route('report.challan')}}"> {{trans('file.Challan Report')}}</a></li>
                <li id="sale-report-chart-menu">
                    {!! Form::open(['route' => 'report.saleChart', 'method' => 'post', 'id' => 'sale-report-chart-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <input type="hidden" name="warehouse_id" value="0" />
                    <input type="hidden" name="time_period" value="weekly" />
                    <a id="sale-report-chart-link" href="">{{trans('file.Sale Report Chart')}}</a>
                    {!! Form::close() !!}
                </li>
                <li id="payment-report-menu">
                    {!! Form::open(['route' => 'report.paymentByDate', 'method' => 'post', 'id' => 'payment-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <a id="payment-report-link" href="">{{trans('file.Payment Report')}}</a>
                    {!! Form::close() !!}
                </li>
                <li id="purchase-report-menu">
                    {!! Form::open(['route' => 'report.purchase', 'method' => 'post', 'id' => 'purchase-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <input type="hidden" name="warehouse_id" value="0" />
                    <a id="purchase-report-link" href="">{{trans('file.Purchase Report')}}</a>
                    {!! Form::close() !!}
                </li>
                <li id="customer-report-menu">
                    <a id="customer-report-link" href="">{{trans('file.Customer Report')}}</a>
                </li>
                <li id="customer-report-menu">
                    <a id="customer-group-report-link" href="">{{trans('file.Customer Group Report')}}</a>
                </li>
                <li id="due-report-menu">
                    {!! Form::open(['route' => 'report.customerDueByDate', 'method' => 'post', 'id' => 'customer-due-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m-d', strtotime('-1 year'))}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <a id="due-report-link" href="">{{trans('file.Customer Due Report')}}</a>
                    {!! Form::close() !!}
                </li>
                <li id="supplier-report-menu">
                    <a id="supplier-report-link" href="">{{trans('file.Supplier Report')}}</a>
                </li>
                <li id="supplier-due-report-menu">
                    {!! Form::open(['route' => 'report.supplierDueByDate', 'method' => 'post', 'id' => 'supplier-due-report-form']) !!}
                    <input type="hidden" name="start_date" value="{{date('Y-m-d', strtotime('-1 year'))}}" />
                    <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
                    <a id="supplier-due-report-link" href="">{{trans('file.Supplier Due Report')}}</a>
                    {!! Form::close() !!}
                </li>
                <li id="warehouse-report-menu">
                    <a id="warehouse-report-link" href="">{{trans('file.Warehouse Report')}}</a>
                </li>

                <li id="warehouse-stock-report-menu">
                    <a href="{{route('report.warehouseStock')}}">{{trans('file.Warehouse Stock Chart')}}</a>
                </li>
                <li id="productExpiry-report-menu">
                    <a href="{{route('report.productExpiry')}}">{{trans('file.Product Expiry Report')}}</a>
                </li>
                <li id="qtyAlert-report-menu">
                    <a href="{{route('report.qtyAlert')}}">{{trans('file.Product Quantity Alert')}}</a>
                </li>

                <li id="daily-sale-objective-menu">
                    <a href="{{route('report.dailySaleObjective')}}">{{trans('file.Daily Sale Objective Report')}}</a>
                </li>
                <li id="user-report-menu">
                    <a id="user-report-link" href="">{{trans('file.User Report')}}</a>
                </li>
                <li id="biller-report-menu">
                    <a id="biller-report-link" href="">{{trans('file.Biller Report')}}</a>
                </li>
        </ul>
    </li>
    @if(!auth()->user()->hasRole('Customer'))
            <li><a href="{{url('addon-list')}}" id="addon-list"> <i class="dripicons-flag"></i><span>{{trans('file.Addons')}}</span></a></li>
        @if (in_array('woocommerce',explode(',',$general_setting->modules)))
            <li><a href="{{route('woocommerce.index')}}"> <i class="fa fa-wordpress"></i><span>WooCommerce</span></a></li>
        @endif
        @if(in_array('ecommerce',explode(',',$general_setting->modules)))
            <li><a href="#ecommerce" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-shopping-bag"></i><span>eCommerce</span></a>
                <ul id="ecommerce" class="collapse list-unstyled ">
                    @include('ecommerce::Tenant.layout.sidebar-menu')
                </ul>
            </li>
        @endif
    @endif
    <li><a href="#setting" aria-expanded="false" data-toggle="collapse"> <i
                class="dripicons-gear"></i><span>{{trans('file.settings')}}</span></a>
        <ul id="setting" class="collapse list-unstyled ">
            @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Owner'))
                <li id="role-menu"><a href="{{route('role.index')}}">{{trans('file.Role Permission')}}</a></li>
                <li><a href="{{route('smstemplates.index')}}">{{trans('file.SMS Template')}}</a></li>
                <li id="custom-field-list-menu"><a
                        href="{{route('custom-fields.index')}}">{{trans('file.Custom Field List')}}</a></li>
            @endif
            <li id="discount-plan-list-menu"><a
                    href="{{route('discount-plans.index')}}">{{trans('file.Discount Plan')}}</a></li>

            <li id="discount-list-menu"><a href="{{route('discounts.index')}}">{{trans('file.Discount')}}</a></li>
            <li id="notification-list-menu">
                <a href="{{route('notifications.index')}}">{{trans('file.All Notification')}}</a>
            </li>
            <li id="notification-menu">
                <a href="" id="send-notification">{{trans('file.Send Notification')}}</a>
            </li>

            <li id="warehouse-menu"><a href="{{route('warehouse.index')}}">{{trans('file.Warehouse')}}</a></li>

            <li id="table-menu"><a href="{{route('tables.index')}}">{{trans('file.Tables')}}</a></li>

            <li id="customer-group-menu"><a
                    href="{{route('customer_group.index')}}">{{trans('file.Customer Group')}}</a></li>

            <li id="brand-menu"><a href="{{route('brand.index')}}">{{trans('file.Brand')}}</a></li>

            <li id="unit-menu"><a href="{{route('unit.index')}}">{{trans('file.Unit')}}</a></li>

            <li id="currency-menu"><a href="{{route('currency.index')}}">{{trans('file.Currency')}}</a></li>

            <li id="tax-menu"><a href="{{route('tax.index')}}">{{trans('file.Tax')}}</a></li>


            <li id="user-menu"><a
                    href="{{route('user.profile', ['user' => Auth::guard('web')->user()->id])}}">{{trans('file.User Profile')}}</a>
            </li>
            <li id="create-sms-menu"><a href="{{route('setting.createSms')}}">{{trans('file.Create SMS')}}</a></li>

            <li><a href="{{route('setting.backup')}}">{{trans('file.Backup Database')}}</a></li>

            <li id="general-setting-menu"><a href="{{route('setting.general')}}">{{trans('file.General Setting')}}</a>
            </li>

            @can('mail_setting')
                <li id="mail-setting-menu"><a href="{{route('setting.mail')}}">{{trans('file.Mail Setting')}}</a></li>
            @endcan

            <li id="reward-point-setting-menu"><a
                    href="{{route('setting.rewardPoint')}}">{{trans('file.Reward Point Setting')}}</a></li>
            <li id="sms-setting-menu"><a href="{{route('setting.sms')}}">{{trans('file.SMS Setting')}}</a></li>
            <li id="pos-setting-menu"><a href="{{route('setting.pos')}}">POS {{trans('file.settings')}}</a></li>
            <li id="hrm-setting-menu"><a href="{{route('setting.hrm')}}"> {{trans('file.HRM Setting')}}</a></li>
            <li id="languages"><a href="{{url('languages/')}}"> {{trans('file.Languages')}}</a></li>


        </ul>
    </li>

    @if(!auth()->user()->hasRole('Customer'))
        <li><a href="#documentation" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-information"></i><span>{{trans('file.Documentation')}}</span></a>
            <ul id="documentation" class="collapse list-unstyled ">
                <li><a target="_blank" href="{{url('/documentation')}}"><span>SalePro</span></a></li>
                <li><a target="_blank" href="{{url('/ecommerce-documentation')}}"><span>SalePro eCommerce</span></a></li>
            </ul>
        </li>
    @endif
</ul>
