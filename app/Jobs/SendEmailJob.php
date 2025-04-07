<?php

namespace App\Jobs;

use App\Mail\General;
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
    public $view;

    public function __construct(array $data, string $mailableClass,string $view = null)
    {
        $this->data = $data;
        $this->mailableClass = $mailableClass;
        $this->view = $view;
    }

    public function handle()
    {
        try {
            if ($this->view == null){
                Mail::to($this->data['email'])->send(new $this->mailableClass($this->data));
            }else{
                Log::info('General email worked: ');
                Mail::to($this->data['email'])->send(new General($this->data,$this->view));
            }
        } catch (\Exception $e) {
            Log::error('Error while sending email: ' . $e->getMessage());
        }
    }
}

