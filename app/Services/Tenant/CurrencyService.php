<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\Mail\CustomerDeposit;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Customer;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CurrencyService
{

    public function getCurrencies()
    {
        return Currency::all();
    }

}
