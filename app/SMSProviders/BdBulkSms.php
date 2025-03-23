<?php

namespace App\SMSProviders;

class BdBulkSms
{
    public function send($data)
    {
        $data['details'] = json_decode($data['details']);
        $data['token'] = $data['details']->token;
        $data['to'] = $data['recipent'];
        $data['message'] = $data['message'];
        
        $url = "http://api.greenweb.com.bd/api.php?json";
        
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresult = curl_exec($ch);
        
        // dd($smsresult);
        //Result
        echo $smsresult;
        
        //Error Display
        echo curl_error($ch);
    }
}