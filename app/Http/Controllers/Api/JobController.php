<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\{ApplyJob, ApplyJobQuestion, MyJob, MyJobQuestion, User, Quotation};
use Illuminate\Support\Facades\Mail;


/**
 *
 */
class JobController extends BaseController
{

    public function create(Request $request)
    {


        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'profile' => 'required',
            'expiry' => 'required',
            // 'questions' => 'required|array',

        ]);

        $job = MyJob::create($request->only(['title', 'description', 'profile', 'expiry']));


        $questions = $request->questions;


        for ($i = 0; $i < count($questions); $i++) {
            $questions[$i]['my_job_id'] = $job->id;
        }

        MyJobQuestion::insert($questions);


        return $this->sendResponse($job, 'Job Created Successfully.');
    }

    public function index()
    {
        return $this->sendResponse(MyJob::select('*')->with('questions')->simplepaginate());
    }

    public function delete(Request $request, $id)
    {
        $job = MyJob::where('id', $id)->delete();
        return $this->sendResponse($job, 'Job Deleted Successfully.');

    }

    public function update(Request $request, $id)
    {
        $job = MyJob::where('id', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'profile' => $request->profile,
            'expiry' => $request->expiry,
        ]);


        $questions = $request->questions;


        for ($i = 0; $i < count($questions); $i++) {

            MyJobQuestion::where('id', $questions[$i]['id'])->update($questions[$i]);

        }


        return $this->sendResponse($job, 'Job Updated Successfully.');

    }


    public function apply(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|unique:apply_jobs',
            'profile' => 'required',
            'resume' => 'required',
            // 'questions' => 'required|array',
        ]);

        $input = $request->only(['name', 'phone', 'email', 'profile', 'resume', 'dob', 'about']);

        if ($request->exists('my_job_id')) {
            $input['my_job_id'] = $request->my_job_id;
        }

        $ajob = ApplyJob::create($input);

        $questions = $request->questions;


        for ($i = 0; $i < count($questions); $i++) {
            $questions[$i]['apply_job_id'] = $ajob->id;
        }


        ApplyJobQuestion::insert($questions);


        Mail::raw("Thanks for job apply", function ($message) use ($request) {
            $message->to($request->email)
                ->subject("Job Apply");
        });


        Mail::raw($request->name . " applied for a new job.", function ($message) use ($request) {
            $message->to("arunkumar@sentisspharma.com")
                ->subject("New Job Apply");
        });


        return $this->sendResponse($ajob, 'Job Applied Successfully.');
    }


    public function appliedSingle(Request $request, $id)
    {
        return $this->sendResponse(ApplyJob::select('*')->with('answered.question')->where('my_job_id', $id)->simplepaginate());
    }


    public function appliedAll(Request $request)
    {
        if ($request->exists('date')) {
            return $this->sendResponse(ApplyJob::select('*')->with('answered.question')->whereDate('created_at',$request->date)->orderBy('id', 'DESC')->simplepaginate());
        }

        return $this->sendResponse(ApplyJob::select('*')->with('answered.question')->orderBy('id', 'DESC')->simplepaginate());

    }


}
