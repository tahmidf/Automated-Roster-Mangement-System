<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Auth;
use App\Softwarepayment;
use App\User;
use App\Model\RuleSetting;
use DB;
use Cache;
use Session;
class ManagementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function users() {
        $users = User::select('*')->orderBy('is_admin','desc')->get();
        return view('user_manage',compact('users'));
    }
    public function user_info($ID) {
       $user = User::find($ID);
       return $user;
   }
   public function update_user_info(Request $request) {
          $data = array();
          $data['name'] = $request->name;
          if($request->password==1)
          $data['password'] = bcrypt($request['newPassword']);
           User::where('id',$request->ID)
           ->update($data);
       Session::put('message','Save information successfully');
       return Redirect::back();
   }
   public function save_user(Request $request) {

       $value = explode(" ", $request->name);
       $userName = strtolower($value[0]);
       if (User::where('username', '=', $userName)->exists()) {
           // user found
           Session::put('info','Duplicate username found! please check.');
           return Redirect::back();
       }
       User::create([
           'name' => $request->name,
           'username' => $userName,
           'category' => "Admin",
           'is_admin' => 1,
           'password' => bcrypt($request['password']),
       ]);

       Session::put('message','Save information successfully');
       return Redirect::back();
   }
   /////////////
   public function settings()
   {
       $settings = RuleSetting::all();
       return view('settings',compact('settings'));
   }
   public function settingsRule($ID,$option)
   {
    $settings = RuleSetting::find($ID);
    if($option==1)
    $settings->value=false;
    else
    $settings->value=true;
    $settings->save();
    return redirect()->back();
    ;
   }
}
