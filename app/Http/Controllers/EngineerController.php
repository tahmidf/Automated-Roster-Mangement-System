<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Auth;
use App\Model\Employee;
use App\User;
use DB;
use Cache;
use Session;
class EngineerController extends Controller
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
     public function index()
     {
         $employee = Employee::orderBy('priority_gateway','desc')->get();
         return view('engineers',compact('employee'));
     }
     public function edit($Id)
     {
         $employee = Employee::find($Id);
         return $employee;
     }
     public function create(Request $request)
     {
         DB::beginTransaction();

         try{
             $engineers = new Employee();
             $engineers->name = $request['data']['name'];
             $engineers->gender = $request['data']['gender'];
             $engineers->priority_gateway = $request['data']['div'];
             $engineers->experience_number = $request['data']['experience_number'];
             $engineers->employee_id = $request['data']['employee_id'];
             $engineers->created_by = Auth::user()->id;
             $engineers->save();

         }
         catch(ValidationException $e)
        {
            DB::rollback();
            return -1;
        } catch(\Exception $e)
        {
            DB::rollback();
            return -1;
        }
        DB::commit();
        return 1;
     }
     public function update(Request $request,$ID){
         DB::beginTransaction();
         try{
             $engineers = Employee::find($ID);
             $engineers->name = $request['data']['name'];
             $engineers->gender = $request['data']['gender'];
             $engineers->priority_gateway = $request['data']['div'];
             $engineers->experience_number = $request['data']['experience_number'];

             $engineers->save();

         }
         catch(ValidationException $e)
        {
            DB::rollback();
            return -1;
        } catch(\Exception $e)
        {
            DB::rollback();
            return -1;
        }
        DB::commit();
        return 1;
     }


}
