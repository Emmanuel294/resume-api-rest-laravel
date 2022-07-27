<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JWTAuth;
use App\Models\Tool;
use Illuminate\Support\Facades\Validator;
class ToolController extends Controller
{
    public function index(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new JWTAuth();
        $user = $jwtAuth->checkToken($token, true);

        if(!empty($user)){
            $tools = Tool::where([
                'user_id' => $user->sub
            ])->get();

            if(is_object($tools)){
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Tools Found',
                    'tools' => $tools
                );
            }else{
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'No Tools Found'
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
            $userId = $user->sub;
            $json = $request->input('json', null);
            $params = json_decode($json);//Object
            $paramsArray = json_decode($json, true);

            if(!empty($params) || !empty($paramsArray)){
                //Validate data
                $paramsArray['user_id'] = $userId;
                $validate = Validator::make($paramsArray, [
                    'user_id'      => 'required',
                    'name'   => 'required'
                ]);

                if($validate->fails()){
                    $data = array(
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Tool was not created.',
                        'errors' => $validate->errors()
                    );
                }else{

                    $tool = new Tool();
                    $tool->name = $paramsArray['name'];
                    $tool->user_id = $paramsArray['user_id'];
                    
                    $tool->save();

                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Tool created.',
                        'tool' => $tool
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

    public function update($toolId, Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new JWTAuth();
        $user = $jwtAuth->checkToken($token, true);

        if(!empty($user)){
            
            $json = $request->input('json', null);
            $paramsArray = json_decode($json, true);

            if(!empty($paramsArray)){

                //Removed not updatable fields
                unset($paramsArray['id']);
                unset($paramsArray['created_at']);
                unset($paramsArray['user_id']);
    
                $toolUpdate = Tool::where('id', $toolId)->update($paramsArray);
    
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Tool Updated Successfully',
                    'tool'  => $toolUpdate
                );
            }else{
                $data = Array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Tool Data Required.'
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
            $tool = Tool::where('id', $id)->where('user_id', $user->sub)->first();
            if(is_object($tool)){
                $toolProjectRelation = new ToolProjectRelationController();
                $deleteRelation = $toolProjectRelation->deleteRelationFromTool($id);
                if($deleteRelation == 200){
                    $tool->delete();
                    $data = Array(
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Tool Deleted.'
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
                    'message' => 'No Tool Found'
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
            $tools = Tool::where([
                'user_id' => $user->sub,
                'id' => $id
            ])->get();

            if(is_object($tools)){
                $toolProjectRelation = new ToolProjectRelationController();
                $toolProjects = $toolProjectRelation->getProjectsOfToolFromRelation($id);
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Tools Found',
                    'tools' => $tools,
                    'projects' => $toolProjects
                );
            }else{
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'No Tools Found'
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
