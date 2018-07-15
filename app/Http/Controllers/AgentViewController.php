<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgentViewController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(\Auth::user()->role == 'admin'){
            return redirect()->route('assign_test');
        }else{
            return view('your_assignments');
        }
    }

}
