<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function login()
    {
        return view('main.login');
    }
}