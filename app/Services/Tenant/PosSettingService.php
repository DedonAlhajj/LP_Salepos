<?php

namespace App\Services\Tenant;


use App\Actions\SendMailAction;
use App\Mail\CustomerDeposit;
use App\Models\Deposit;
use App\Models\Customer;
use App\Models\PosSetting;
use App\Models\Product_Warehouse;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosSettingService
{

    public function getStripePublicKey()
    {
        return PosSetting::select('stripe_public_key')->latest()->first();
    }
}
