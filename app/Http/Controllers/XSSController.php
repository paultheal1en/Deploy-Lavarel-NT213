<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class XSSController extends Controller
{
    public function index(Request $request){
        $name=$request->input('name');
        // $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        return view('xss')->with('name',$name);
    }
}