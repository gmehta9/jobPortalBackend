<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordForgot extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->name = $user['name'];
        $this->code = $user['code'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('passwordForgotMail')->with([
            'name' => $this->name,
            'code' => $this->code,
        ]);
    }
}
