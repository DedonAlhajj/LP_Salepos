<?php

namespace App\Http\Controllers\Central;

use App\Actions\ValidateDomainAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\RegistrationRequest;
use App\Models\Package;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function showForm(Request $request)
    {
        $request->validate([
            'package' => 'required|exists:packages,id',
        ], [
            'package.required' => 'Please select a package before registering.',
            'package.exists' => 'The selected package is invalid.',
        ]);

        $packageId = $request->query('package');

        $package = Package::find($packageId);

        if ($package->is_active !== '1') {
            return redirect()->route('Central.packages.index')
                ->withErrors('The selected package is currently unavailable.');
        }

        // إذا كان كل شيء صحيحًا، عرض صفحة التسجيل
        return view('Central.payingPackage.register', ['packageId' => $packageId]);
    }

    /*<a href="{{ route('register.form', ['package' => $package->id]) }}" class="btn btn-primary">
            Register Now
        </a>
     * */

    public function store(RegistrationRequest  $request)
    {
        // التحقق من صحة البيانات القادمة
        $validated = $request->validated();

        // التحقق من تفرد الدومين
        try {
            app(ValidateDomainAction::class)->execute($validated['domain']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('The domain is already taken or invalid.');
        }

        // تفريغ بيانات التسجيل السابقة في الجلسة (إن وجدت)
        if (session()->has('registration_data')) {
            session()->forget('registration_data');
        }

        // تخزين البيانات في الجلسة
        try {
            session()->put('registration_data', $validated);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Failed to save registration data. Please try again.');
        }

        // إعادة التوجيه إلى صفحة اختيار طريقة الدفع
        return redirect()->route('Central.payment.form');
    }
}
