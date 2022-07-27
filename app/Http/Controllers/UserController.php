<?php

namespace App\Http\Controllers;

use App\Helpers\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use  App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
                $pwd = hash('sha256', $params->password);
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

    public function login(Request $request){
        $jwtAuth = new JWTAuth();

        //Get params
        $json  = $request->input('json',  null);
        $params  = json_decode($json);
        $paramsArray= json_decode($json,  true);

        //Validate data
        $validate = Validator::make($paramsArray, [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if($validate->fails()){
            $signUp = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Unable to identify user.',
                'errors' => $validate->errors()
            );
        }else{
            //Hash password
            $pwd = hash('sha256', $params->password);

            $signUp = $jwtAuth->signup($params->email,  $pwd);

            if(!empty($params->getToken)){
                $signUp = $jwtAuth->signup($params->email,  $pwd, true);
            }
        }

        return response()->json($signUp, 200);
    }

    public function update(Request $request){
        
        $json = $request->input('json', null);
        $paramsArray = json_decode($json, true);
        $token = $request->header('Authorization');
        $jwtAuth = new JWTAuth();
        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken && !empty($paramsArray)){

            
            $user = $jwtAuth->checkToken($token, true);

            ///Validate Data
            $validate = Validator::make($paramsArray, [
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha'
            ]);

            //Removed not updatable fields
            unset($paramsArray['id']);
            unset($paramsArray['created_at']);
            unset($paramsArray['password']);
            unset($paramsArray['email']);
            unset($paramsArray['remember_token']);

            $userUpdate = User::where('id', $user->sub)->update($paramsArray);

            $data = Array(
                'code' => 200,
                'status' => 'success',
                'message' => 'User Updated Successfully',
                'user'  => $userUpdate
            );
        }else{
            $data = Array(
                'code' => 400,
                'status' => 'error',
                'message' => 'User Data Required.'
            );
        }
        
        return response()->json($data, $data['code']);
    }

    public function uploadFile(Request $request){

        //Get image from request
        $image = $request->file('file0');

        //Validate image
        $validator = Validator::make($request->all(), [
            'file0' =>  'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        if(!$image || $validator->fails()){
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error uploading image'
            );
        }
        else{
            $imageName = time().$image->getClientOriginalName();
            
            Storage::disk('users')->put($imageName, File::get($image));
            
            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $imageName
            );

        }

        return response()->json($data, $data['code']);
    }

    public function getImage($fileName){
        $isset = Storage::disk('users')->exists($fileName);
        if($isset){
            $file = Storage::disk('users')->get($fileName);

            return new Response($file, 200);
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Image not found'
            );

            return response()->json($data, $data['code']);
        }
    }

    public function detail($id){
        $user = User::find($id);

        if(is_object($user)){
            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => 'User Found',
                'user' => $user
            );
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'User not found'
            );
        }

        return response()->json($data, $data['code']);

    }

}
