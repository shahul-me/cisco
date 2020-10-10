<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('POST')) {
            $input = $request->only('login_id', 'password');
            $validator = \Validator::make($input, ['login_id' => 'required', 'password' => 'required']);
            if ($validator->passes()) {

                $result = User::where('login_id', $input['login_id'])->first();
                if ($result) {
                    if (\Hash::check($request['password'], $result->password)) {
                        $result_data['token'] = $this->generateToken($input['login_id']);
                        $response = ['code' => 200, 'message' => 'Login Successfully - Use this token for your further API Call', 'token' => $result_data['token']];
                    } else {
                        $response = ['code' => 204, 'message' => 'Invalid Credential', 'data' => ''];
                    }
                } else {
                    $response = ['code' => 204, 'message' => 'User not found', 'data' => ''];
                }
            } else {
                $response = ['code' => 204, 'message' => 'Invalid input data', 'data' => ''];
            }
        } else {
            $response = ['code' => 204, 'message' => 'equest should be Post'];
        }
        return response($response)->header('Content-Type', 'application/json');
    }

    public function generateToken($login_id)
    {

        $api_token = Str::random();
        $login_time = date('Y-m-d H:i:s');
        $data = User::where('login_id', $login_id)
            ->update(['token' => $api_token]);
        return $api_token;
    }
}
