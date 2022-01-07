<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use \App\Models\{User, PasswordReset, EmailTemplate, ApplyJob};
use App\Jobs\MailerJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

use Auth;

/**
 *
 */
class UserController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:users,email',
            'name' => 'required',
            'password' => 'required'
        ]);

        $user = $request->only(['name', 'email', 'phone', 'password']);

        $data = User::create($user);

        return $this->sendResponse(['user' => $data, 'token' => $data->createToken('MyApp')->plainTextToken], 'User Created Successfully.');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $id = $request->user()->id;

        $user = User::findOrFail($id);

        $user->update($request->only(['name', 'email', 'password', 'address', 'company_name', 'phone_number', 'role']));

        return $this->sendResponse($user, 'User Updated Successfully.');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function smartSearch(Request $request)
    {
        $request->validate([
            'search_text' => 'required',
            'field' => 'required|in:name,email,company_name',
        ]);

        $search_text = $request->search_text;
        $field = $request->field;


        switch ($field) {
            case 'name':
                $res = User::select('name', 'email', 'address', 'company_name', 'phone_number')->where('name', 'LIKE', "%{$search_text}%")->get();
                break;

            case 'email':
                $res = User::select('name', 'email', 'address', 'company_name', 'phone_number')->where('email', 'LIKE', "%{$search_text}%")->get();
                break;

            default:

                // by default search from name column
                $res = User::select('name', 'email', 'address', 'company_name', 'phone_number')->where('name', 'LIKE', "%{$search_text}%")->get();
                break;
        }
        return $this->sendResponse($res);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendMessageResponse('User could not found,Please check your email.', 404);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success = [];
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['user'] = $user;

            return $this->sendResponse($success, 'Login Successfully');

        } else {
            return $this->sendMessageResponse('Wrong Password.', 404);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();


        PasswordReset::where('email', $request->email)->delete();
        $token = mt_rand(100000, 999999);  // creating 6 digit random code
        $passwordReset = new PasswordReset(
            [
                'email' => $user->email,
                'token' => $token,
            ]
        );

        $passwordReset->save();

        MailerJob::dispatch('passwordForgot', $user['email'], ['name' => $user->name, 'code' => $token]);

        return $this->sendResponse(array('token' => $token), 'Code sent via email');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|digits:6',
            'email' => 'required|string|email|exists:users,email'
        ]);

        try {
            PasswordReset::where('token', $request->token)->where('email', $request->email)->firstOrFail();
        } catch (\Throwable $t) {
            return $this->sendMessageResponse('Invalid token or expired', 404);
        }
        return $this->sendMessageResponse('Code Validated');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required'
        ]);

        $passwordReset = PasswordReset::where('token', $request->token)->where('email', $request->email)->firstOrFail();
        $user = User::where('email', $passwordReset->email)->firstOrFail();

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(15)->isPast()) {
            PasswordReset::where('token', $request->token)->where('email', $request->email)->delete();
            return $this->sendMessageResponse('Code Expire', 400);
        }

        $user->password = $request->password;
        $user->save();

        PasswordReset::where('token', $request->token)->where('email', $request->email)->delete();

        MailerJob::dispatch('passwordReset', $user['email'], ['name' => $user->name]);


        return $this->sendResponse([
            'user' => $user,
            'token' => $user->createToken('MyApp')->plainTextToken
        ], 'Reset Done');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        // Revoke all tokens
        auth()->user()->tokens()->delete();

        return $this->sendMessageResponse('Logout Successfully.');
    }


    public function createEmailTemplate(Request $request)
    {

        $data = EmailTemplate::create($request->all());

        return $this->sendResponse($data, 'Created Successfully.');
    }


    public function listEmailTemplate()
    {
        return $this->sendResponse(EmailTemplate::select('*')->simplepaginate());
    }


    public function deleteEmailTemplate(Request $request, $id)
    {
        $job = EmailTemplate::where('id', $id)->delete();
        return $this->sendResponse($job, 'Deleted Successfully.');

    }


    public function updateEmailTemplate(Request $request, $id)
    {

        $job = EmailTemplate::where('id', $id)->update($request->all());


        return $this->sendResponse($job, 'Updated Successfully.');

    }


    public function changeAppliedJobStatus(Request $request)
    {
        try {


            $user = ApplyJob::find($request->id);
            $emailTemplates = EmailTemplate::where('type', $request->status)->first();
            $user->update(['status' => $request->status]);


//            $m1 = Mail::raw($emailTemplates->text, function ($message) use ($user) {
//                $message->to($user->email)
//                    ->subject("Status applied");
//            });

            Mail::send([], [], function ($message) use ($user, $emailTemplates) {
                $message->to($user->email)
                    ->subject("Status applied")
                    ->setBody($emailTemplates->text, 'text/html'); // for HTML rich messages.
            });

        } catch (\Throwable $t) {
            return $t;
        }


        return $this->sendResponse($user, 'Updated Successfully.');

    }


}
