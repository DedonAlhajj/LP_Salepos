<?php

namespace App\Http\Controllers\Central;


use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\SuperUser;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HomeController extends Controller
{

    public function index()
    {
        // الحصول على المستخدم الحالي
        $user = Auth::guard('super_users')->user();

        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return abort(403, 'Unauthorized action.');
        }

        // التحقق من نوع المستخدم باستخدام Gate
        if (Gate::allows('is-user')) {
            return $this->handleUserDashboard($user);
        } elseif (Gate::allows('is-admin')) {
            dd('admin');
            return $this->handleAdminDashboard();
        }

        // في حالة عدم تحقق أي شرط
        return abort(403, 'Unauthorized action.');
    }

    private function handleUserDashboard(SuperUser $user)
    {
        // جلب بيانات المستأجر المرتبطة بالمستخدم
        $tenant = Tenant::with(['package', 'domains'])
            ->where('super_user_id', $user->id)
            ->first();

        // إذا لم يكن هناك مستأجر مرتبط
        if (!$tenant) {
            return view('Central.dashboard', ['message' => 'No data available']);
        }

        // حساب المدة المتبقية على انتهاء الاشتراك
        $remainingDays = Carbon::now()->diffInDays(Carbon::parse($tenant->subscription_end), false);

        // إرسال البيانات إلى الواجهة
        return view('Central.dashboard', [
            'tenant' => $tenant,
            'package' => $tenant->package,
            'domains' => $tenant->domains,
            'remainingDays' => $remainingDays,
        ]);
    }

    private function handleAdminDashboard()
    {
        // جمع البيانات الإحصائية للإدمن
        $adminData = [
            'totalTenants' => Tenant::count(),
            'activePackages' => Package::where('is_active', '1')->count(),
        ];

        // إرسال البيانات إلى الواجهة
        return view('Central.dashboard', [
            'role' => 'admin',
            'adminData' => $adminData,
        ]);
    }}


