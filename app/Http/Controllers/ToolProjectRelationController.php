<?php

namespace App\Http\Controllers;

use App\Models\ToolProjectRelation;
use Illuminate\Http\Request;

class ToolProjectRelationController extends Controller
{
    public function deleteRelationFromProject($projectId){
        $projectTool = ToolProjectRelation::where('project_id', $projectId)->first();
        if(is_object($projectTool)){
            $projectTool->delete();
            $result = 200;
        }else{
            $result = 400;
        }

        return $result;
    }

    public function deleteRelationFromTool($toolId){
        $projectTool = ToolProjectRelation::where('tool_id', $toolId)->first();
        if(is_object($projectTool)){
            $projectTool->delete();
            $result = 200;
        }else{
            $result = 400;
        }

        return $result;
    }

    public function getProjectsOfToolFromRelation($toolId){
        $projectTool = ToolProjectRelation::where('tool_id', $toolId)->with('project')->get();
        return $projectTool;
    }

    public function getToolsOfProjectFromRelation($projectId){
        $projectTool = ToolProjectRelation::where('project_id', $projectId)->with('tool')->get();
        return $projectTool;
    }
}
