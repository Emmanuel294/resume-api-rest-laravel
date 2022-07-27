<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JWTAuth;
use App\Models\Resume;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ProjectResumeRelationController;
use App\Models\ProjectResumeRelation;

class ResumeController extends Controller
{
    public function index(Request $request){

        $token = $request->header('Authorization');
        $jwtAuth = new JWTAuth();
        $user = $jwtAuth->checkToken($token, true);

        if(!empty($user)){
            $resumes = Resume::where([
                'user_id' => $user->sub
            ])->get();
            
            if(is_object($resumes)){
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Resumes Found',
                    'resumes' => $resumes
                );
            }else{
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'No Resumes Found'
                );
            }
        }else{
            $data = Array(
                'code' => 400,
                'status' => 'error',
                'message' => 'User Data Not Found'
            );
        }

        return response()->json($data, $data['code']);

    }

    public function store(Request $request){

        $token = $request->header('Authorization');
        $jwtAuth = new JWTAuth();
        $user = $jwtAuth->checkToken($token, true);

        if(!empty($user)){
            
            $json = $request->input('json', null);
            $params = json_decode($json);//Object
            $paramsArray = json_decode($json, true);

            if(!empty($params) || !empty($paramsArray)){
                //Validate data
                $validate = Validator::make($paramsArray, [
                    'user_id'      => 'required',
                    'name'   => 'required'
                ]);

                if($validate->fails()){
                    $data = array(
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Resume was not created.',
                        'errors' => $validate->errors()
                    );
                }else{

                    $resume = new Resume();
                    $resume->name = $paramsArray['name'];
                    $resume->user_id = $paramsArray['user_id'];
                    
                    $resume->save();

                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Resume created.',
                        'resume' => $resume
                    );

                }

            }else{
                $data = Array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Wrong Data'
                );
            }

        }else{
            $data = Array(
                'code' => 400,
                'status' => 'error',
                'message' => 'User Data Not Found'
            );
        }

        return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){

        $token = $request->header('Authorization');
        $jwtAuth = new JWTAuth();
        $user = $jwtAuth->checkToken($token, true);

        if(!empty($user)){
            
            $json = $request->input('json', null);
            $paramsArray = json_decode($json, true);

            if(!empty($paramsArray)){

                ///Validate Data
                $validate = Validator::make($paramsArray, [
                    'name'      => 'required|alpha',
                ]);
                //Removed not updatable fields
                unset($paramsArray['id']);
                unset($paramsArray['created_at']);
                unset($paramsArray['user_id']);
    
                $resumeUpdate = Resume::where('id', $id)->update($paramsArray);
    
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Resume Updated Successfully',
                    'resume'  => $resumeUpdate
                );
            }else{
                $data = Array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Resume Data Required.'
                );
            }

        }else{
            $data = Array(
                'code' => 400,
                'status' => 'error',
                'message' => 'User Data Not Found'
            );
        }

        return response()->json($data, $data['code']);

    }

    public function destroy($id, Request $request){

        $token = $request->header('Authorization');
        $jwtAuth = new JWTAuth();
        $user = $jwtAuth->checkToken($token, true);

        if(!empty($user)){
            $resume = Resume::where('id', $id)->where('user_id', $user->sub)->first();
            if(is_object($resume)){
                $resumeProjectRelation = new ProjectResumeRelationController();
                $deleteRelation = $resumeProjectRelation->deleteRelationFromResume($id);
                if($deleteRelation == 200){
                    $resume->delete();
                    $data = Array(
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Resume Deleted'
                    );
                }else{
                    $data = Array(
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'Error Deletiong Record. Please Check Data.'
                    ); 
                }
                
            }else{
                $data = Array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No Resume Found'
                );
            }

        }else{
            $data = Array(
                'code' => 400,
                'status' => 'error',
                'message' => 'User Data Not Found'
            );
        }

        return response()->json($data, $data['code']);

    }

    public function show($id, Request $request){

        $token = $request->header('Authorization');
        $jwtAuth = new JWTAuth();
        $user = $jwtAuth->checkToken($token, true);

        if(!empty($user)){
            $resume = Resume::where([
                'id' => $id,
                'user_id' => $user->sub
            ])->get();
            
            if(is_object($resume)){
                $resumeProjectRelation = new ProjectResumeRelationController();
                $resumeProjects = $resumeProjectRelation->getProjectsOfResumeFRomRelation($id);
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Resumes Found',
                    'resumes' => $resume,
                    'projects' => $resumeProjects
                );
            }else{
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'No Resumes Found'
                );
            }
        }else{
            $data = Array(
                'code' => 400,
                'status' => 'error',
                'message' => 'User Data Not Found'
            );
        }

        return response()->json($data, $data['code']);

    }

}
