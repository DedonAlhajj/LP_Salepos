<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function sendPayment(Request $request);

    public function callBack(Request $request);

    public function filterDataThatGoToPaymentGateway($name,$email,$price,$currency);

    public function dataThatCameFromPaymentGateway($response);
}
