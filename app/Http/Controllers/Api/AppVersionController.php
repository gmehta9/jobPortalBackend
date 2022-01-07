<?php

namespace App\Http\Controllers\Api;

class AppVersionController extends BaseController
{
    /**
     * App Version Api
     *
     * @return \Illuminate\Http\Response
     */
    public function version()
    {
        $data = [
            'android' => [
                "versionName" => config('app.androidVersion'),
                "forceUpdate" => config('app.forceUpdate')
            ],
            'ios' => [
                "versionName" => config('app.iosVersion'),
                "forceUpdate" => config('app.forceUpdate')
            ],

        ];
        return $this->sendResponse($data);
    }
}
