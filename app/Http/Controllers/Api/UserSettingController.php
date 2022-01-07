<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use \App\Models\UserSetting;

use Auth;

/**
 *
 */
class UserSettingController extends BaseController
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Update(Request $request)
    {
        $request->validate([
            'tax' => 'required',
        ]);

        $id = $request->user()->id;

        $userSetting = UserSetting::findOrFail($id);

        $userSetting->update($request->only(['tax', 'exterior_doors_price_per_square_footage', 'interior_doors_price_per_square_footage', 'windows_price_per_square_footage', 'gate_price_per_square_footage', 'exterior_doors_installation_price', 'interior_doors_installation_price', 'windows_installation_price', 'gate_installation_price', 'note_to_customer', 'cc', 'terms_and_condition']));

        return $this->sendResponse($userSetting, 'User Setting Updated Successfully.');

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userSetting(Request $request)
    {
        $userSetting = UserSetting::select('*')->where('user_id', $request->user()->id)->first();

        return $this->sendResponse($userSetting);
    }

}
