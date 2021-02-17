<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function login()
    {
        return view('main.login');
    }

    public function loginPd($token)
    {
        return response()->json(['code' => 1000, 'token' => $token]);
    }
}