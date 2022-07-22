<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use DomainException;
use UnexpectedValueException;

class JWTAuth{

    public $key;

    public function  __construct(){
        $this->key = 'SuPerSecureK3YWiThNumS234';
    }

    public function signup($email, $password, $getToken = null){
        //Look for user with credentials
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();

        //Validate data
        $signUp = false;
        if(is_object($user)){
            $signUp = true;
        }

        //Generate token
        if($signUp){
            $token = array(
                'sub'   =>  $user->id,
                'email' =>  $user->email,
                'namme' =>  $user->name,
                'surname'   =>  $user->surname,
                'iat'   =>   time(),
                'exp'   =>   time() + (7 * 24 * 60 *  60)
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded  = JWT::decode($jwt, $this->key, ['HS256']);

            //Return  decoded token
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decoded;
            }

        }else{
            $data = array(
                'status' => 'error',
                'message' => 'Incorrect Login.'
            );
        }
        //Return decoded data(Token)

        return $data;

    }

    public  function checkToken($jwt, $getIdentity  = false){
        $auth= false;

        try{
            $decode = JWT::decode($jwt, $this->key, ['HS256']);
        }catch(UnexpectedValueException $ex){
            $auth = false;
        }catch(DomainException $ex){
            $auth = false;
        }

        if(!empty($decode) && is_object($decode) && isset($decode->sub)){
            $auth = true;
        }else{
            $auth = false;
        }

        if($getIdentity){
            return $decode;
        }

        return $auth;

    }

}
