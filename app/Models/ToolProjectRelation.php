<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolProjectRelation extends Model
{
    protected $table = 'tools_projects_relation';

    //Relation Many to One
    public function tool(){
        return $this->belongsTo('App\Models\Tool', 'tool_id');
    }
    public function project(){
        return $this->belongsTo('App\Models\Project', 'project_id');
    }
}
