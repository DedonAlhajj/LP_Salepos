        <ul id="side-main-menu" class="side-menu list-unstyled d-print-none">
            <li><a href="{{url('/dashboard')}}"> <i class="dripicons-meter"></i><span>{{ __('file.dashboard') }}</span></a></li>

            <li><a href="#setting" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-gear"></i><span>{{trans('file.settings')}}</span></a>
                <ul id="setting" class="collapse list-unstyled ">

                        <li id="role-menu"><a href="{{route('role.index')}}">{{trans('file.Role Permission')}}</a></li>

                </ul>
            </li>
        </ul>
