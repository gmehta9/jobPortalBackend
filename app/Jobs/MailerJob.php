<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\{PasswordForgot, PasswordReset};
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class MailerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mailType, $recipient, $content;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mailType, $recipient, $content = null)
    {


        $this->mailType = $mailType;
        $this->recipient = $recipient;
        $this->content = $content;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        switch ($this->mailType) {
            case 'passwordForgot':
                Mail::to($this->recipient)->send(new PasswordForgot($this->content));
                break;

            case 'passwordReset':
                Mail::to($this->recipient)->send(new PasswordReset($this->content));
                break;

            default:
                # code...
                break;
        }
    }
}
