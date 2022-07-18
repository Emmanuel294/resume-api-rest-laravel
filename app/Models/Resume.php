<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    protected $table = 'resumes';

    //Relation Many to One
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
