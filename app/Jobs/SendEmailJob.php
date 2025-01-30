<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $mailableClass;

    public function __construct(array $data, string $mailableClass)
    {
        $this->data = $data;
        $this->mailableClass = $mailableClass;
    }

    public function handle()
    {
        try {
            Mail::to($this->data['email'])->send(new $this->mailableClass($this->data));
        } catch (\Exception $e) {
            Log::error('Error while sending email: ' . $e->getMessage());
        }
    }
}

