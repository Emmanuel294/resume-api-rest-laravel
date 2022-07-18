<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Resume;
class TestController extends Controller
{
    //
    public function test(Request $request){
        return "Funcion de pruebas de user";
    }

    public function testORM(){
        $resumes = Resume::all();
        foreach($resumes as $resume){
            echo "<h1>".$resume->name."</h1>";
        }
        die();
    }
}
