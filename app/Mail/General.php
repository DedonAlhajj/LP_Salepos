<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class General extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public $view;

    public function __construct($data,$view)
    {
        $this->data = $data;
        $this->view = $view;
    }

    public function build()
    {
        return $this->view($this->view,$this->data)->subject('New Date');
    }
}
