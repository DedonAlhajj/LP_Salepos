<?php

namespace App\DTOs;


class SmsDTO
{
    public string $providerName;
    public array $providerDetails;
    public string $message;
    public array $recipients;

    public function __construct(string $providerName, array $providerDetails, string $message, array $recipients)
    {
        $this->providerName = $providerName;
        $this->providerDetails = $providerDetails;
        $this->message = $message;
        $this->recipients = $recipients;
    }

    public static function fromRequest(array $data, array $providerDetails): self
    {
        return new self(
            $providerDetails['name'],
            $providerDetails['details'],
            $data['message'],
            explode(',', $data['mobile'])
        );
    }

    public function toArray(): array
    {
        return [
            'sms_provider_name' => $this->providerName,
            'details' => $this->providerDetails,
            'message' => $this->message,
            'recipent' => $this->recipients,
        ];
    }
}
