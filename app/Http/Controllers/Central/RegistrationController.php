<?php

namespace App\Http\Controllers\Central;

use App\Actions\ValidateDomainAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\RegistrationRequest;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function showForm(Request $request)
    {
        $packageId = $request->query('package');
        return view('Central.payingPackage.register', compact('packageId'));
    }

    public function store(RegistrationRequest  $request)
    {
        $validated = $request->validated();

        // check if domain is taken.
        app(ValidateDomainAction::class)->execute($validated['domain']);

        // save date in session.
        session()->put('registration_data', $validated);

        return redirect()->route('payment.form');
    }
}
