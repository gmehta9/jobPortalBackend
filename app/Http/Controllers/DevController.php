<?php

namespace App\Http\Controllers;

use \App\Models\{User, PasswordReset, EmailTemplate, ApplyJob};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Class DevController
 * @package App\Http\Controllers
 */
class DevController extends Controller
{
    public function index()
    {


        $user = ApplyJob::find(7);
        $emailTemplates = EmailTemplate::where('type', 'Accept')->first();

        return Mail::send([], [], function ($message) use ($user, $emailTemplates) {
            $message->to($user->email)
                ->subject("Status applied")
                ->setBody($emailTemplates->text, 'text/html'); // for HTML rich messages.
        });


    }
}
