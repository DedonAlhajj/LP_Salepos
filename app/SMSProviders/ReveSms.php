<?php

namespace App\SMSProviders;

use App\Models\ExternalService;

class ReveSms
{
    public function send($data)
    {
        $data['details'] = json_decode($data['details']);
        $data['apikey'] = $data['details']->apikey;
        $data['secretkey'] = $data['details']->secretkey;
        $data['callerID'] = $data['details']->callerID;
        $data['recipent'] = $data['recipent'];
        $data['message'] = $data['message'];

        $url = "http://smpp.revesms.com:7788/sendtext";

        $params = [
            'apikey' => $data['apikey'],
            'secretkey' =>  $data['secretkey'],
            'callerID' =>  $data['callerID'],
            'toUser' => $data['recipent'],
            'messageContent' => $data['message']
        ];

        $url .= '?' . http_build_query($params);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return ['error' => $error_msg];
        }

        curl_close($ch);
        
        // dd($response);
        return json_decode($response, true); 
    }
}