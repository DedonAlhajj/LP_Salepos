<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class GeneralSettingCentralDTO
{
    public string $site_title;
    public bool $is_rtl;
    public string $phone;
    public string $email;
    public int $free_trial_limit;
    public string $date_format;
    public string $dedicated_ip;
    public string $currency;
    public string $developed_by;
    public string $meta_title;
    public string $meta_description;
    public string $og_title;
    public string $og_description;
    public string $chat_script;
    public string $ga_script;
    public string $fb_pixel_script;
    public string $active_payment_gateway;
    public array $payment_credentials;
    public ?string $site_logo;
    public ?string $og_image;

    public function __construct(Request $request)
    {
        $this->site_title = $request->input('site_title');
        $this->is_rtl = $request->boolean('is_rtl');
        $this->phone = $request->input('phone');
        $this->email = $request->input('email');
        $this->free_trial_limit = (int) $request->input('free_trial_limit');
        $this->date_format = $request->input('date_format');
        $this->dedicated_ip = $request->input('dedicated_ip');
        $this->currency = $request->input('currency');
        $this->developed_by = $request->input('developed_by');
        $this->meta_title = $request->input('meta_title');
        $this->meta_description = $request->input('meta_description');
        $this->og_title = $request->input('og_title');
        $this->og_description = $request->input('og_description');
        $this->chat_script = $request->input('chat_script');
        $this->ga_script = $request->input('ga_script');
        $this->fb_pixel_script = $request->input('fb_pixel_script');
        $this->active_payment_gateway = implode(",", $request->input('active_payment_gateway', []));
        $this->site_logo = $request->file('site_logo');
        $this->og_image = $request->file('og_image');

        // تضمين جميع بيانات الدفع
        $this->payment_credentials = [
            'stripe_public_key' => $request->input('stripe_public_key'),
            'stripe_secret_key' => $request->input('stripe_secret_key'),
            'paypal_client_id' => $request->input('paypal_client_id'),
            'paypal_client_secret' => $request->input('paypal_client_secret'),
            'razorpay_number' => $request->input('razorpay_number'),
            'razorpay_key' => $request->input('razorpay_key'),
            'razorpay_secret' => $request->input('razorpay_secret'),
            'paystack_public_key' => $request->input('paystack_public_key'),
            'paystack_secret_key' => $request->input('paystack_secret_key'),
            'paydunya_master_key' => $request->input('paydunya_master_key'),
            'paydunya_public_key' => $request->input('paydunya_public_key'),
            'paydunya_secret_key' => $request->input('paydunya_secret_key'),
            'paydunya_token' => $request->input('paydunya_token'),
            'ssl_store_id' => $request->input('ssl_store_id'),
            'ssl_store_password' => $request->input('ssl_store_password'),
            'bkash_app_key' => $request->input('bkash_app_key'),
            'bkash_app_secret' => $request->input('bkash_app_secret'),
            'bkash_username' => $request->input('bkash_username'),
            'bkash_password' => $request->input('bkash_password'),
        ];
    }
}

