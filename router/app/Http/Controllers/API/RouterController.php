<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\models\Router;
use App\models\User;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {
            $input = $request->only('sapid', 'hostname', 'loopback', 'mac', 'login_id', 'token');
            $validator = \Validator::make($input, ['sapid' => 'unique:routers,sapid',
                'hostname' => 'unique:routers,hostname',
                'loopback' => 'unique:routers,loopback',
                'mac' => 'unique:routers,mac',
                'login_id' => 'required', 'token' => 'required']);
            if ($validator->passes()) {

                $result = User::where('login_id', $input['login_id'])->where('token', $input['token'])->first();
                if ($result) {
                    $result_data = Router::insert(
                        ['sapid' => $input['sapid'], 'hostname' => $input['hostname'], 'loopback' => $input['loopback'], 'mac' => $input['mac']]
                    );
                    if (($result_data)) {
                        $response = ['code' => 200, 'message' => 'Router Created Successfully'];
                    } else {
                        $response = ['code' => 204, 'message' => 'Invalid Data'];
                    }

                } else {
                    $response = ['code' => 204, 'message' => 'Invalid Data  or Token', 'data' => ''];
                }

            } else {
                $response = ['code' => 204, 'message' => 'Invalid or duplicate Data'];
            }

        } else {
            $response = ['code' => 204, 'message' => 'Request should be Post'];
        }
        return response($response)->header('Content-Type', 'application/json');
    }

    public function listByType(Request $request)
    {
        if ($request->isMethod('get')) {
            $input = $request->only('token', 'type', 'login_id');
            $validator = \Validator::make($input, [
                'type' => 'required',
                'login_id' => 'required', 'token' => 'required']);
            if ($validator->passes()) {

                $result = User::where('login_id', trim($input['login_id']))->where('token', trim($input['token']))->first();
                if ($result) {
                    $result_data = Router::where('type', trim($input['type']))->get();
                    if (($result_data)) {
                        $response = ['code' => 200, 'data' => $result_data];
                    } else {
                        $response = ['code' => 204, 'message' => 'Invalid Data'];
                    }

                } else {
                    $response = ['code' => 204, 'message' => 'Invalid Data  or Token', 'data' => ''];
                }

            } else {
                $response = ['code' => 204, 'message' => 'Invalid Data'];
            }

        } else {
            $response = ['code' => 204, 'message' => 'Request should be Get'];
        }
        return response($response)->header('Content-Type', 'application/json');
    }

    public function ListByIp(Request $request)
    {
        if ($request->isMethod('get')) {
            $input = $request->only('token', 'ip','login_id');
            $validator = \Validator::make($input, [
               'login_id' => 'required', 'ip' => 'required', 'token' => 'required']);
            if ($validator->passes()) {

                $result = User::where('login_id', trim($input['login_id']))->where('token', trim($input['token']))->first();
                if ($result) {
                    $result_data = Router::where('loopback', trim($input['ip']))->get();
                    if (($result_data)) {
                        $response = ['code' => 200, 'data' => $result_data];
                    } else {
                        $response = ['code' => 204, 'message' => 'Invalid Data'];
                    }

                } else {
                    $response = ['code' => 204, 'message' => 'Invalid Data  or Token', 'data' => ''];
                }

            } else {
                $response = ['code' => 204, 'message' => 'Invalid Data'];
            }

        } else {
            $response = ['code' => 204, 'message' => 'No get data.'];
        }
        return response($response)->header('Content-Type', 'application/json');
    }

    public function updateByIp(Request $request)
    {
        if ($request->isMethod('post')) {
       
                $input = $request->only('sapid', 'hostname', 'ip', 'mac', 'login_id', 'token');
               // return response($input)->header('Content-Type', 'application/json');
                $validator = \Validator::make($input, ['sapid' => 'required',
                    'hostname' =>'required',
                    'ip' => 'required',
                    'mac' => 'required',
                    'login_id' => 'required', 'token' => 'required']);


            if ($validator->passes()) {

                $result = User::where('login_id', trim($input['login_id']))->where('token', trim($input['token']))->first();
                if ($result) {
                    $result_data = Router::where('loopback', trim($input['ip']))
                        ->update(['sapid' => trim($input['sapid']), 'hostname' => $input['hostname'], 'mac' => $input['mac']]);
                    if (($result_data)) {
                        $response = ['code' => 200, 'message' => 'Updated Successfully'];
                    } else {
                        $response = ['code' => 204, 'message' => 'Invalid Data'];
                    }

                } else {
                    $response = ['code' => 204, 'message' => 'Invalid Data  or Token', 'data' => ''];
                }

            } else {
                $response = ['code' => 204, 'message' => 'Invalid or Data Missing'];
            }

        } else {
            $response = ['code' => 204, 'message' => 'Invalid Request'];
        }
        return response($response)->header('Content-Type', 'application/json');
    }

    public function Delete(Request $request)
    {
        if ($request->isMethod('delete')) {
            $input = $request->only('token', 'ip', 'login_id');
            $validator = \Validator::make($input, [
                'login_id' => 'required', 'ip' => 'required', 'token' => 'required']);

            if ($validator->passes()) {

                $result = User::where('login_id', trim($input['login_id']))->where('token', trim($input['token']))->first();
                if ($result) {
                    $result_data = Router::where('loopback', $input['ip'])->delete();
                    if (($result_data)) {
                        $response = ['code' => 200, 'message' => 'Deleted Successfully'];
                    } else {
                        $response = ['code' => 204, 'message' => 'Invalid Data'];
                    }

                } else {
                    $response = ['code' => 204, 'message' => 'Invalid Data  or Token', 'data' => ''];
                }

            } else {
                $response = ['code' => 204, 'message' => 'Invalid Data'];
            }

        } else {
            $response = ['code' => 204, 'message' => 'Invalid Request'];
        }
        return response($response)->header('Content-Type', 'application/json');
    }
}
