<?php 

namespace App\SMSProviders;

use Clickatell\Rest;
use Clickatell\ClickatellException;

class Clickatell
{
    public function send($data)
    {
        try {
            $clickatell = new \Clickatell\Rest(env('CLICKATELL_API_KEY'));
            foreach ($data['numbers'] as $number) {
                $result = $clickatell->sendMessage(['to' => [$number], 'content' => $data['message']]);
            }
        }
        catch (ClickatellException $e) {
            return redirect()->back()->with('not_permitted', 'Please setup your <a href="sms_setting">SMS Setting</a> to send SMS.');
        }
        $message = "SMS sent successfully";
    }
}