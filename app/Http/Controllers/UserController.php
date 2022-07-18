<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use  App\Models\User;

class UserController extends Controller
{
    public function test(){
        return 'User Controller Test';
    }

    public function register(Request $request){
        //Get user data
        $json = $request->input('json', null);
        $params = json_decode($json);//Object
        $paramsArray = json_decode($json, true);//Array
        
        if(!empty($params) && !empty($paramsArray)){
            //Validate data
            $validate = Validator::make($paramsArray, [
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users',
                'password'  => 'required'
            ]);

            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'User was not created.',
                    'errors' => $validate->errors()
                );
            }else{
                //Hash Password
                $pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);
                //Create user
                $user = new User();
                $user->name = $paramsArray['name'];
                $user->surname = $paramsArray['surname'];
                $user->email = $paramsArray['email'];
                $user->password = $pwd;
                $user->description = $paramsArray['description'];
                
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'User created.',
                    'user' => $user
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Wrong user data.'
            );
        }
        
        return response()->json($data, $data['code']);
    }
}
