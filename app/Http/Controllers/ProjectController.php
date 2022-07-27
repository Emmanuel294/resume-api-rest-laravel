<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JWTAuth;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new JWTAuth();
        $user = $jwtAuth->checkToken($token, true);

        if(!empty($user)){
            $projects = Project::where([
                'user_id' => $user->sub
            ])->get();

            if(is_object($projects)){
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Resumes Found',
                    'projects' => $projects
                );
            }else{
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'No Projects Found'
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
                    'name'   => 'required',
                    'description'   => 'required',
                    'started_date'   => 'required|date'
                ]);

                if($validate->fails()){
                    $data = array(
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Project was not created.',
                        'errors' => $validate->errors()
                    );
                }else{

                    $project = new Project();
                    $project->name = $paramsArray['name'];
                    $project->user_id = $paramsArray['user_id'];
                    $project->description = $paramsArray['description'];
                    
                    $project->save();

                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Project created.',
                        'project' => $project
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

    public function update($projectId, Request $request){
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
    
                $projectUpdate = Project::where('id', $projectId)->update($paramsArray);
    
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Project Updated Successfully',
                    'project'  => $projectUpdate
                );
            }else{
                $data = Array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Project Data Required.'
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
            $project = Project::where('id', $id)->where('user_id', $user->sub)->first();
            if(is_object($project)){
                $resumeProjectRelation = new ProjectResumeRelationController();
                $deleteRelation = $resumeProjectRelation->deleteRelationFromProject($id);
                if($deleteRelation == 200){
                    $project->delete();
                    $data = Array(
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Project Deleted.'
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
                    'message' => 'No Project Found'
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
            $projects = Project::where([
                'user_id' => $user->sub,
                'id' => $id
            ])->get();

            if(is_object($projects)){
                $resumeProjectRelation = new ProjectResumeRelationController();
                $resumeProjects = $resumeProjectRelation->getResumesOfProjectFromRelation($id);

                $toolsProjects = new ToolProjectRelationController();
                $tools = $toolsProjects->getToolsOfProjectFromRelation($id);

                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Project Found',
                    'projects' => $projects,
                    'resumes' => $resumeProjects,
                    'tools' => $tools
                );
            }else{
                $data = Array(
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'No Projects Found'
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
