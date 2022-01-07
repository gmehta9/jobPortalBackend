<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 *
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     */
    function developer(Request $request)
    {

    }

    /**
     * @param $result
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message = null)
    {
        $response = [
            'data' => $result,
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        return response()->json($response);
    }

    /**
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessageResponse($message, $status = 200)
    {
        return response()->json(['message' => $message], $status);
    }
}
