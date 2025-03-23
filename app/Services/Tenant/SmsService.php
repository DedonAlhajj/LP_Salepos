<?php

namespace App\Services\Tenant;

use App\SMSProviders\BdBulkSms;
use App\SMSProviders\ReveSms;
use App\SMSProviders\TonkraSms;

class SmsService
{
    private $_tonkraSms;
    private $_reveSms;
    private $_bdbulkSms;

    public function __construct(TonkraSms $tonkraSms, ReveSms $reveSms, BdBulkSms $bdBulkSms)
    {
        $this->_tonkraSms = $tonkraSms;
        $this->_reveSms = $reveSms;
        $this->_bdbulkSms = $bdBulkSms;
    }

    public function initialize($data)
    {
        $smsServiceProviderName = $data['sms_provider_name'];

        switch ($smsServiceProviderName) {
            case 'tonkra':
                 $this->_tonkraSms->send($data);
            case 'revesms':
                 $this->_reveSms->send($data);
            case 'bdbulksms':
                 $this->_bdbulkSms->send($data);
            default:
                break;
        }
    }
}
