<?php

namespace App\Http\Controllers\AuthTenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TenantLoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantAuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('Tenant.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        // طباعة CSRF Token لتتأكد من أنه يتم استخدام نفس التوكن مع الطلبات
        logger('CSRF Tokenkkkkkkk: ' . csrf_token());

        // طباعة Session Tenant ID للتحقق من أنه يتم الاحتفاظ ببيانات المستأجر داخل الجلسة
        logger('Session Tenant ID: ' . session('tenant_id'));

        // أكمل باقي معالجة الطلب
        // يمكنك هنا إضافة أي خطوات أخرى للتحقق أو الطباعة
        logger('Request Data:', $request->all());
        $tenantId = tenant('id');
        $user = User::where('email', $request->email)
            ->where('tenant_id', $tenantId)  // التأكد من أن اليوزر ينتمي للمستأجر الصحيح
            ->first();

        if ($user && Auth::guard('web')->attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])) {

            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
