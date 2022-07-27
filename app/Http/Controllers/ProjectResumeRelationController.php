<?php

namespace App\Http\Controllers;

use App\Models\ProjectResumeRelation;
use Illuminate\Http\Request;

class ProjectResumeRelationController extends Controller
{
    public function deleteRelationFromProject($projectId){
        $projectResume = ProjectResumeRelation::where('project_id', $projectId)->first();
        if(is_object($projectResume)){
            $projectResume->delete();
            $result = 200;
        }else{
            $result = 400;
        }

        return $result;
    }

    public function deleteRelationFromResume($resumeId){
        $projectResume = ProjectResumeRelation::where('resume_id', $resumeId)->first();
        if(is_object($projectResume)){
            $projectResume->delete();
            $result = 200;
        }else{
            $result = 400;
        }

        return $result;
    }

    public function getProjectsOfResumeFRomRelation($resumeId){
        $projectResume = ProjectResumeRelation::where('resume_id', $resumeId)->with('project')->get();
        return $projectResume;
    }

    public function getResumesOfProjectFromRelation($projectId){
        $projectResume = ProjectResumeRelation::where('project_id', $projectId)->with('resume')->get();
        return $projectResume;
    }

}
