<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    public function show(){

        $user = DB::select("select * from t1");
        return view('user.profile', ["user" => $user]);
    }

}
