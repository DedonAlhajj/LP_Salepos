<ul id="side-main-menu" class="side-menu list-unstyled d-print-none">
    <li><a href="{{route('super.dashboard')}}"> <i class="dripicons-meter"></i><span>{{ __('file.dashboard') }}</span></a></li>
    @if(Auth::guard('super_users')->check() && Auth::guard('super_users')->user()->role == 'admin')
    <li><a href="{{route('Central.packages.index')}}"> <i class="fas fa-box package-icon"></i><span>{{ __('Packages') }}</span></a></li>

    <li><a href=""> <i class="fas fa-user-friends tenant-icon"></i><span>{{ __('Tenants') }}</span></a></li>
    
    <li><a href="{{route('Central.features.index')}}"> <i class="fa fa-cogs"></i><span>{{ __('Features') }}</span></a></li>           
    @endif

 </ul>
