<?php

namespace App\SMSProviders;

use App\Contracts\Sms\SendSmsInterface;
use App\Contracts\Sms\CheckBalanceInterface;
use App\Models\ExternalService;

class TonkraSms //implements SendSmsInterface, CheckBalanceInterface
{
    public function send($data)
    {
        $data['details'] = json_decode($data['details']);
        $data['api_token'] = $data['details']->api_token;
        $data['sender_id'] = $data['details']->sender_id;

        $headers = array(
            "Authorization: Bearer ".$data['api_token']."",
            "Accept: application/json",
        );
        $params = [
            "recipient" => $data['recipent'],
            "sender_id" => $data['sender_id'],
            "type" => 'plain',
            "message" => $data['message'],
        ];
        $params = json_encode($params);
        $url = "https://sms.tonkra.com/api/v3/sms/send";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        $resp = curl_exec($curl);
        curl_close($curl);
        // return dd($resp);

    }

    public function balance()
    {
        $tonkra = ExternalService::where('name','tonkra')->first();

        if(empty($tonkra))
        {
            return 0;
        }

        $details = json_decode($tonkra->details);
        $api_token = $details->api_token  ?? '';

        $headers = [
            "Authorization: Bearer ".$api_token."",
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        $url = "https://sms.tonkra.com/api/v3/balance";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, true);
        $responseData = $responseData['data']['remaining_balance'] ?? 0;

        $responseData = preg_replace("/[^0-9]/", "", $responseData);
        // $responseData = intval($responseData);
        return $responseData;
    }
}
