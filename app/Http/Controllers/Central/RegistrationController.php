<?php

namespace App\Http\Controllers\Central;

use App\Actions\ValidateDomainAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\RegistrationRequest;
use App\Models\Package;
use App\Models\PendingUser;
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


    public function store(RegistrationRequest $request)
    {
        $validated = $request->validated();

        try {
            // البحث عن سجل بنفس البريد الإلكتروني
            $pendingUser = PendingUser::updateOrCreate(
                ['email' => $validated['email']], // الشرط
                [
                    'name' => $validated['name'],
                    'password' => bcrypt($validated['password']),
                    'store_name' => $validated['store_name'],
                    'domain' => $validated['domain'],
                    'package_id' => $validated['package_id'],
                    'operation_type' => $validated['OperationType'],
                    'status' => 'pending',
                    'expires_at' => now()->addHours(24),
                ]
            );


        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Failed to save registration data. Please try again.');
        }

        return redirect()->route('payment.choose', ['token' => encrypt($pendingUser->id)]);

    }
}
