<ul id="side-main-menu" class="side-menu list-unstyled d-print-none">
    <li><a href="{{url('/dashboard')}}"> <i class="dripicons-meter"></i><span>{{ __('file.dashboard') }}</span></a></li>

    <li><a href="#product" aria-expanded="false" data-toggle="collapse"> <i
                class="dripicons-list"></i><span>{{__('file.product')}}</span><span></a>
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

    <li><a href="#setting" aria-expanded="false" data-toggle="collapse"> <i
                class="dripicons-gear"></i><span>{{trans('file.settings')}}</span></a>
        <ul id="setting" class="collapse list-unstyled ">
            @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Owner'))
                <li id="role-menu"><a href="{{route('role.index')}}">{{trans('file.Role Permission')}}</a></li>
            @endif
            <li id="user-menu"><a
                    href="{{route('user.profile', ['user' => Auth::guard('web')->user()->id])}}">{{trans('file.User Profile')}}</a>
            </li>

            @can('mail_setting')
                <li id="mail-setting-menu"><a href="{{route('setting.mail')}}">{{trans('file.Mail Setting')}}</a></li>
            @endcan
        </ul>
    </li>
</ul>
