<?php

namespace App\Http\Controllers\Tenant;


use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\SuperUser;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{

    public function index()
    {
        // الحصول على المستخدم الحالي
        $user = Auth::guard('web')->user();


        return view('Tenant.index');

    }

    public function home()
    {
        return view('Tenant.home');
    }

    public function switchTheme($theme)
    {
        setcookie('theme', $theme, time() + (86400 * 365), "/");
    }
}


