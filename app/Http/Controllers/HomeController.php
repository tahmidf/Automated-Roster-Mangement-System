<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;
use Auth;
use App\Invoice;
use App\Softwarepayment;

use Cache;
use DB;
use Session;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->is_admin){
            return Redirect::to('/login');
        }

            return view('home',compact('message'));
    }
    public function logout() {
        Session::flush();
        Cache::flush();
        Auth::logout();
        Session::put('message','Successfully Logout!');
        return Redirect::to('/');
    }


}
