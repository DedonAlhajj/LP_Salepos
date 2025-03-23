<?php 
namespace App\SMSProviders;

class Twilio
{
    public function initialize($data)
    {
        $endpoint = "https://api.smstoday.net/send";
        $ch = curl_init();
        $array_post = http_build_query(array(
            'text'=> $data['message'],
            'numbers'=>$data['numbers'],
            'api_key'=> 'lwbKCGZjMGsbSfKoOWVXvIEK2Mw3siIwtNJ',
            'password'=> 'Farmer@2018',
            'from'=> 'FarmerTribe'
        ));

        curl_setopt($ch, CURLOPT_URL,$endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$array_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);
        //dd($server_output);
        $message = "SMS sent successfully";
    }
}