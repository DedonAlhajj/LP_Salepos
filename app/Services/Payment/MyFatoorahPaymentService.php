<?php

namespace App\Services\Payment;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MyFatoorahPaymentService extends BasePaymentService implements PaymentGatewayInterface
{
    /**
     * Create a new class instance.
     */
    protected $api_key;
    public function __construct()
    {
        $this->base_url = env("MYFATOORAH_BASE_URL");
        $this->api_key = env("MYFATOORAH_API_KEY");
        $this->header = [
            'accept' => 'application/json',
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . $this->api_key,
        ];
    }

    public function sendPayment(Request $request): array
    {
        $data = $request->all();
        $data['NotificationOption']="LNK";
        $data['Language']="en";
        $data['CallBackUrl']='https://' .config('tenancy.central_domains')[0].'/api/payment/callback';
        $response = $this->buildRequest('POST', '/v2/SendPayment', $data);
        //handel payment response data and return it
         if($response->getData(true)['success']){
             return ['success' => true,'url' => $response->getData(true)['data']['Data']['InvoiceURL']];
        }
         return ['success' => false,'url'=>route('payment.failed')];
    }

    public function callBack(Request $request): array
    {
        $data=[
            'KeyType' => 'paymentId',
            'Key' => $request->input('paymentId'),
        ];
        $response=$this->buildRequest('POST', '/v2/getPaymentStatus', $data);
        $response_data=$response->getData(true);

        Storage::put('myfatoorah_response.json',json_encode([
            'myfatoorah_callback_response'=>$request->all(),
            'myfatoorah_response_status'=>$response_data
        ]));

        if($response_data['data']['Data']['InvoiceStatus']==='Paid'){

            return ['success' => true, 'response_data' => $response_data];
        }

        return ['success' => false, 'response_data' => $response_data];
    }

    public function filterDataThatGoToPaymentGateway($name,$email,$price,$currency){
        return [
            "CustomerName" => e($name),
            "CustomerEmail" => e($email),
            "InvoiceValue" => (float) $price,
            "DisplayCurrencyIso" => $currency,
        ];
    }

    //$email, $currency, $PaymentGateway, $transaction_id, $reference_number
    public function dataThatCameFromPaymentGateway($response)
    {
        return [
            $response['response_data']['data']['Data']['CustomerEmail'],
            $response['response_data']['data']['Data']['InvoiceTransactions'][0]['Currency'],
            $response['response_data']['data']['Data']['InvoiceTransactions'][0]['PaymentGateway'],
            $response['response_data']['data']['Data']['InvoiceTransactions'][0]['TransactionId'],
            $response['response_data']['data']['Data']['InvoiceTransactions'][0]['ReferenceId'],
        ];
    }
}
