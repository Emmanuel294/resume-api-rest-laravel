<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectResumeRelation extends Model
{
    protected $table = 'projects_resume_relation';

    //Relation Many to One
    public function resume(){
        return $this->belongsTo('App\Models\Resume', 'resume_id');
    }
    public function project(){
        return $this->belongsTo('App\Models\Project', 'project_id');
    }
}
