        <ul id="side-main-menu" class="side-menu list-unstyled d-print-none">
            <li><a href="{{url('/dashboard')}}"> <i class="dripicons-meter"></i><span>{{ __('file.dashboard') }}</span></a></li>


            <li><a href="#people" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-user"></i><span>{{trans('file.People')}}</span></a>
                <ul id="people" class="collapse list-unstyled ">
                    @can('users-index')
                        <li id="user-list-menu"><a href="{{route('user.index')}}">{{trans('file.User List')}}</a></li>
                        <li id="user-trash-menu"><a href="{{route('user.Trashed')}}">User List Trashed</a></li>

                @can('users-add')
                            <li id="user-create-menu"><a href="{{route('user.create')}}">{{trans('file.Add User')}}</a></li>
                        @endcan
                    @endcan

                    @can('customers-index')
                        <li id="customer-list-menu"><a href="{{route('customer.index')}}">{{trans('file.Customer List')}}</a></li>
                        @can('customers-add')
                            <li id="customer-create-menu"><a href="{{route('customer.create')}}">{{trans('file.Add Customer')}}</a></li>
                        @endcan
                    @endcan

                    @can('billers-index')
                        <li id="biller-list-menu"><a href="{{route('biller.index')}}">{{trans('file.Biller List')}}</a></li>
                        @can('billers-add')
                            <li id="biller-create-menu"><a href="{{route('biller.create')}}">{{trans('file.Add Biller')}}</a></li>
                        @endcan
                    @endcan

                    @can('suppliers-index')
                        <li id="supplier-list-menu"><a href="{{route('supplier.index')}}">{{trans('file.Supplier List')}}</a></li>
                        @can('suppliers-add')
                            <li id="supplier-create-menu"><a href="{{route('supplier.create')}}">{{trans('file.Add Supplier')}}</a></li>
                        @endcan
                    @endcan
                </ul>
            </li>


            <li><a href="#setting" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-gear"></i><span>{{trans('file.settings')}}</span></a>
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
