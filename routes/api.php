<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\{UserController,
    CustomerController,
    UserSettingController,
    AppVersionController,
    QuotationController};


/*
|--------------------------------------------------------------------------
| API Routes..
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('upload-image', function (Request $request) {

    $image = '';

    if ($request->hasFile('image')) {

        $image = (new \App\Services\CustomS3())->uploadDirect($request->file('image'), 'file');

    } else {
        $image = '';
    }

    $data = [
        'message' => 'success',
        'image' => $image
    ];

    return response()->json($data, 200);

});


Route::post('delete-image', function (Request $request) {

    try {
        $image = (new \App\Services\CustomS3())->deleteDirect('file', $request->image);

        $data = [
            'message' => 'success',
            'data' => $image
        ];

        return response()->json($data, 200);
    } catch (\Throwable $t) {
        $data = [
            'message' => 'failed to delete',
            'data' => $t
        ];

        return response()->json($data, 200);
    }
});

Route::post('delete-all-images', function (Request $request) {

    try {
        if ($request->all()) {
            foreach ($request->all() as $file) {
                (new \App\Services\CustomS3())->deleteDirect('file', $file['image']);
            }
        }

        $data = [
            'message' => 'success',

        ];

        return response()->json($data, 200);

    } catch (\Throwable $t) {

        $data = [
            'message' => 'failed to delete',
            'data' => $t
        ];

        return response()->json($data, 200);
    }
});


Route::group(
    [
        'prefix' => 'users'

    ],
    function () {

        Route::post('/', [UserController::class, 'create']);
        Route::put('/', [UserController::class, 'update'])->middleware('auth:sanctum');

        Route::post('/createEmailTemplate', [UserController::class, 'createEmailTemplate']); // ->middleware('auth:sanctum');
        Route::get('/listEmailTemplate', [UserController::class, 'listEmailTemplate']); // ->middleware('auth:sanctum');
        Route::delete('/deleteEmailTemplate/{id}', [UserController::class, 'deleteEmailTemplate']);
        Route::post('/updateEmailTemplate/{id}', [UserController::class, 'updateEmailTemplate']);


        Route::post('/changeAppliedJobStatus', [UserController::class, 'changeAppliedJobStatus']);





        Route::post('/login', [UserController::class, 'login']);
        Route::post('password/forgot', [UserController::class, 'forgot']);
        Route::post('password/validate/token', [UserController::class, 'validateToken']);
        Route::post('password/reset', [UserController::class, 'reset']);
        Route::get('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');



        Route::get('user-smart-search', [UserController::class, 'smartSearch']);
    }
);

Route::group(
    [
        'prefix' => 'job',
        'middleware' => ['auth:sanctum']

    ],
    function () {
        Route::post('/', [\App\Http\Controllers\Api\JobController::class, 'create']);
        Route::get('/', [\App\Http\Controllers\Api\JobController::class, 'index'])->withoutMiddleware('auth:sanctum');
        Route::delete('/{id}', [\App\Http\Controllers\Api\JobController::class, 'delete'])->withoutMiddleware('auth:sanctum');
        Route::post('/{id}', [\App\Http\Controllers\Api\JobController::class, 'update'])->withoutMiddleware('auth:sanctum');

        Route::get('/{id}/appliedSingle', [\App\Http\Controllers\Api\JobController::class, 'appliedSingle'])->withoutMiddleware('auth:sanctum');
        Route::get('/appliedAll', [\App\Http\Controllers\Api\JobController::class, 'appliedAll'])->withoutMiddleware('auth:sanctum');

    }
);


Route::post('apply', [\App\Http\Controllers\Api\JobController::class, 'apply'])->withoutMiddleware('auth:sanctum');



// App Version Api
Route::get('appVersion', [AppVersionController::class, 'version']);
Route::get('quote-generate', [QuotationController::class, 'quoteGenerate']);

Route::get('developer', [\App\Http\Controllers\DevController::class, 'index']);





