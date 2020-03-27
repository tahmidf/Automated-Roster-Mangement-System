<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Auth;
use App\Model\Employee;
use App\Model\RuleRoster;
use App\Model\Roster;
use App\Model\RosterShift;
use DateInterval;
use DateTime;
use DatePeriod;
use Cache;
use DB;
use Hoa;
use Log;
use PDF;
use Excel;
use Carbon\Carbon;
use Session;
class RMSController extends Controller
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
        $rosters = Roster::orderBy('id','desc')->get();
        return view('roster',compact('rosters'));
    }
    public function add_roster()
    {
        $employeeIIG = Employee::orderBy('experience_number','desc')->where('priority_gateway', 'IIG')->get();
        $employeeIGW = Employee::orderBy('experience_number','desc')->where('priority_gateway', 'IGW')->get();
        $rosters = Roster::orderBy('id','desc')->get();
        return view('add_new_roster',compact('employeeIGW',"employeeIIG",'rosters'));
    }

    public function create(Request $request)
    {
        $dataGet = $request->all();
        DB::beginTransaction();
        $latestRosterID=-1;
        $size = sizeof($dataGet['data']);
        $startDate = Carbon::parse($dataGet['data'][$size-1]['startDate']);
        $endDate = Carbon::parse($dataGet['data'][$size-1]['endDate']);

        $weekLastDate = Carbon::parse($dataGet['data'][$size-1]['startDate'])->addWeek();
        $dataGet = $dataGet['data'];
        //return $dataGet;
        try {
                $dataRoster = new Roster();
                $dataRoster->start_date = $startDate->format('Y-m-d');
                $dataRoster->end_date = $endDate->format('Y-m-d');
                $dataRoster->created_by = Auth::user()->id;
                $dataRoster->save();
                $latestRosterID= $dataRoster->id;

             if($latestRosterID!=-1){

                 $tempDate = $startDate;
                 $length = $weekLastDate->diffInDays($startDate);
                 if($length==7){
                      for ($i=0; $i < sizeof($dataGet)-1; $i++) {
                         $tempDate = $startDate;
                         for ($day=0; $day <$length ; $day++) {
                             $data = new RosterShift();
                             $data->roster_id = $latestRosterID;
                             $data->employee_id = $dataGet[$i][$day]['employee_id'];

                                if($day==0){
                                    $data->shift_time= $tempDate->format('l');
                                    $data->shift=  $dataGet[$i][$day]['sun'];
                                }
                                else if($day==1){
                                    $data->shift_time= $tempDate->format('l');
                                    $data->shift=      $dataGet[$i][$day]['mon'];
                                }
                                else if($day==2){
                                    $data->shift_time=$tempDate->format('l');
                                    $data->shift=  $dataGet[$i][$day]['tue'];
                                }
                                else if($day==3){
                                    $data->shift_time=$tempDate->format('l');;
                                    $data->shift=  $dataGet[$i][$day]['wed'];

                                }
                                else if($day==4){
                                    $data->shift_time=$tempDate->format('l');
                                    $data->shift=  $dataGet[$i][$day]['thu'];
                                }
                                else if($day==5){
                                    $data->shift_time=$tempDate->format('l');
                                    $data->shift=  $dataGet[$i][$day]['fri'];
                                }
                                else if($day==6){
                                    $data->shift_time=$tempDate->format('l');
                                    $data->shift=  $dataGet[$i][$day]['sat'];
                                }

                                $data->date = $tempDate->format('Y-m-d');
                                $data->created_by = Auth::user()->id;
                                $tempDate = $tempDate->copy()->addDays(1);

                                $data->save();
                        }
                     }


                 }
                 else {
                     return -1;
                 }

         }
         else{
             return -1;

         }

        } catch(ValidationException $e)
        {
            DB::rollback();
            return -1;
        } catch(\Exception $e)
        {
            DB::rollback();
            return -1;
        }
        DB::commit();
        Session::put('message', 'Date saved successfully!');
        return 1;
    }
    public function edit($ID)
    {
        $roster = Roster::find($ID);
        $rosterShift = RosterShift::where('roster_id',$roster->id)->get();

        $employee = Employee::whereHas('roster', function($q) use ($ID){
            $q->where('roster_id',$ID);
        })->orderBy('experience_number','desc')->get();

        $rosterArray = new \stdClass;
        $rosterArray->IIG = [];
        $rosterArray->IGW = [];
        foreach ($employee as $key => $value) {
            if ($value->priority_gateway=="IIG")
                $rosterArray->IIG [] =$this->mapArrayRoster($roster,$value,$rosterShift);
            else
                $rosterArray->IGW [] =$this->mapArrayRoster($roster,$value,$rosterShift);
        }
        $weekLastDate = Carbon::parse($roster->start_date)->addDays(6);
        $dutyShift = $this->createDummyRosterArray($roster->start_date,$weekLastDate->format('Y-m-d'));


        return view('edit_roster',compact('rosterArray','roster','dutyShift'));
    }
    public function update(Request $request,$ID)
    {
        $dataGet = $request->all();
        DB::beginTransaction();
        $rosterID=$ID;
        $dataGet = $dataGet['data'];
        try {
                      for ($i=0; $i < sizeof($dataGet); $i++) {
                         for ($day=0; $day <7 ; $day++) {
                                $data = new \stdClass;
                                if($day==0){
                                    $data->shift=  $dataGet[$i][$day]['sun'];
                                    $data->day = "Sunday";
                                }
                                else if($day==1){
                                    $data->shift=  $dataGet[$i][$day]['mon'];
                                    $data->day = "Monday";
                                }
                                else if($day==2){
                                    $data->shift=  $dataGet[$i][$day]['tue'];
                                    $data->day = "Tuesday";
                                }
                                else if($day==3){
                                    $data->shift=  $dataGet[$i][$day]['wed'];
                                    $data->day = "Wednesday";

                                }
                                else if($day==4){
                                    $data->shift=  $dataGet[$i][$day]['thu'];
                                    $data->day = "Thursday";
                                }
                                else if($day==5){
                                    $data->shift=  $dataGet[$i][$day]['fri'];
                                    $data->day = "Friday";
                                }
                                else if($day==6){
                                    $data->shift=  $dataGet[$i][$day]['sat'];
                                    $data->day = "Saturday";
                                }
                                RosterShift::where('roster_id',$rosterID)->where('shift_time', $data->day)
                                ->where('employee_id',$dataGet[$i][$day]['employee_id'])
                                ->update(['shift'=>$data->shift]);
                        }
                     }

        } catch(ValidationException $e)
        {
            DB::rollback();
            return -1;
        } catch(\Exception $e)
        {
            DB::rollback();
            return -1;
        }
        DB::commit();
        Session::put('message', 'Date saved successfully!');
        return 1;
    }
    public function pdfWeekRoster($ID)
    {
        $roster = Roster::find($ID);
        $rosterShift = RosterShift::where('roster_id',$roster->id)->get();

        $employee = Employee::whereHas('roster', function($q) use ($ID){
            $q->where('roster_id',$ID);
        })->orderBy('experience_number','desc')->get();
        
        $rosterArray = new \stdClass;
        $rosterArray->IIG = [];
        $rosterArray->IGW = [];
        foreach ($employee as $key => $value) {
            if ($value->priority_gateway=="IIG")
                $rosterArray->IIG [] =$this->mapArrayRoster($roster,$value,$rosterShift);
            else
                $rosterArray->IGW [] =$this->mapArrayRoster($roster,$value,$rosterShift);
        }
        $weekLastDate = Carbon::parse($roster->start_date)->addDays(6);
        $dutyShift = $this->createDummyRosterArray($roster->start_date,$weekLastDate->format('Y-m-d'));


    //    return view('weekly_roster_pdf',compact('rosterArray','roster','dutyShift'));
        $pdf = PDF::loadView('weekly_roster_pdf', compact('rosterArray','roster','dutyShift'))->setPaper('a4', 'landscape');
        return $pdf->download('Weekly Roster '.Carbon::parse($roster->start_date)->format("Y-m-d").' to '.
        Carbon::parse($roster->end_date)->format("Y-m-d").'.pdf');
    }
    public function previous_roster($Id)
    {
        $roster = Roster::find($Id);
        $rosterShift = RosterShift::where('roster_id',$roster->id)->get();

        $employee = Employee::orderBy('experience_number','desc')->get();
        $rosterDuty = array();
        $rosterArray = new \stdClass;
        $rosterArray->IIG = [];
        $rosterArray->IGW = [];
        foreach ($employee as $key => $value) {
            if ($value->priority_gateway=="IIG")
                $rosterArray->IIG [] =$this->mapArrayRoster($roster,$value,$rosterShift);
            else
                $rosterArray->IGW [] =$this->mapArrayRoster($roster,$value,$rosterShift);
        }
        $rosterDuty[]=$rosterArray;
        return $rosterDuty;
    }
    public function pdfMonthRoster($ID){
        $roster = Roster::find($ID);
        $rosterShift = RosterShift::where('roster_id',$roster->id)->get();

        $employee = Employee::orderBy('experience_number','desc')->get();

        $rosterArray = new \stdClass;
        $rosterArray->IIG = [];
        $rosterArray->IGW = [];
        foreach ($employee as $key => $value) {
            if ($value->priority_gateway=="IIG")
                $rosterArray->IIG [] =$this->mapArrayRoster($roster,$value,$rosterShift);
            else
                $rosterArray->IGW [] =$this->mapArrayRoster($roster,$value,$rosterShift);
        }

        $dutyShift = $this->createDummyRosterArray($roster->start_date,$roster->end_date);
        $numberofDay= sizeof($dutyShift->duty);
        //return $dutyShift->duty;
        $rosterArrayMonth = $this->makeArrayMonthpdf($roster,$rosterArray,$employee);
        //return view('monthly_roster_pdf',compact('rosterArrayMonth','roster','numberofDay','monthName','dutyShift'));


        // $pdf = PDF::loadView('monthly_roster_pdf', compact('rosterArrayMonth','roster','numberofDay','monthName','dutyShift'))->setPaper('a4', 'landscape');
        // return $pdf->download('Monthly Roster '.Carbon::parse($roster->start_date)->format("Y-m-d").' to '.
        // Carbon::parse($roster->end_date)->format("Y-m-d").'.pdf');
    }
    public function excel_monthly_roster($ID)
    {
        $roster = Roster::find($ID);
        $rosterShift = RosterShift::where('roster_id',$roster->id)->get();

        $employee = Employee::orderBy('experience_number','desc')->get();

        $rosterArray = new \stdClass;
        $rosterArray->IIG = [];
        $rosterArray->IGW = [];
        foreach ($employee as $key => $value) {
            if ($value->priority_gateway=="IIG")
                $rosterArray->IIG [] =$this->mapArrayRoster($roster,$value,$rosterShift);
            else
                $rosterArray->IGW [] =$this->mapArrayRoster($roster,$value,$rosterShift);
        }

        $dutyShift = $this->createDummyRosterArray($roster->start_date,$roster->end_date);
        $numberofDay= sizeof($dutyShift->duty);
        //return $dutyShift->duty;
        $rosterArrayMonth = $this->makeArrayMonthpdf($roster,$rosterArray,$employee);
        //return view('monthly_roster_pdf',compact('rosterArrayMonth','roster','numberofDay','monthName','dutyShift'));

        Excel::create('Monthly_roster '.Carbon::parse($roster->start_date)->format("Y-m-d").'_'.Carbon::parse($roster->end_date)->format("Y-m-d"),
            function($excel) use ($rosterArrayMonth,$dutyShift,$numberofDay) {
            $excel->sheet('rosters', function($sheet) use ($rosterArrayMonth,$dutyShift,$numberofDay) {

                $sheet->loadView('excel_monthly_roster',array('rosterArrayMonth' => $rosterArrayMonth,'dutyShift' => $dutyShift,
                'numberofDay'=>$numberofDay
                ));

            });

        })->download("XLSX");
    }
    public function makeArrayMonthpdf($roster,$rosterArrayWeek,$employee)
    {
        $rosterArrayMonth = new \stdClass;
        $rosterArrayMonth->IIG = [];
        $rosterArrayMonth->IGW = [];

        foreach ($employee as $key => $value) {
            if ($value->priority_gateway=="IIG")
                $rosterArrayMonth->IIG [] = $this->createRosterWeekArray($roster->start_date,$roster->end_date,$value);
            else
                $rosterArrayMonth->IGW [] = $this->createRosterWeekArray($roster->start_date,$roster->end_date,$value);
        }
        foreach ($rosterArrayMonth->IIG as  $value) {
                $count =0;
                $index = $this->getElementByIndex($value->employee_id,$rosterArrayWeek->IIG);
                if($index!=-1){
                    foreach ($value->duty as $key => $valueDuty) {
                            $valueDuty->shift = $rosterArrayWeek->IIG[$index]->duty[$count]->shift;
                            $count++;
                            if($count==7)$count=0;
                    }
                }
        }
        foreach ($rosterArrayMonth->IGW as  $value) {
                $count =0;
                $index = $this->getElementByIndex($value->employee_id,$rosterArrayWeek->IGW);
                if($index!=-1){
                    foreach ($value->duty as $key => $valueDuty) {
                            $valueDuty->shift = $rosterArrayWeek->IGW[$index]->duty[$count]->shift;
                            $count++;
                            if($count==7)$count=0;
                    }
                }
        }
        return $rosterArrayMonth;
    }
    public function getElementByIndex($employeeID,$rosterWeek)
    {
        foreach ($rosterWeek as $key => $value) {
            if($value->employee_id==$employeeID)
            return $key;
        }
        return -1;
    }
    public function createDummyRosterArray($start, $end,$format = 'Y-m-d')
    {
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        $rosterMonthlyFull= new \stdClass;
        $rosterMonthlyFull->duty = [];

        foreach($period as $date) {
            $duty= new \stdClass;
            $duty->rosterDate = $date->format($format);;
            $duty->day = date("D", strtotime($date->format($format)));
            $rosterMonthlyFull->duty [] = $duty;

        }

        return $rosterMonthlyFull;
    }
    public function mapArrayRoster($roster,$employee,$rosterShift)
    {
        $weekLastDate = Carbon::parse($roster->start_date)->addDays(6);
        $rosterArray  = $this->createRosterWeekArray($roster->start_date,$weekLastDate->format('Y-m-d'),$employee);
            foreach ($rosterShift as $key => $value) {
                        //Array Map Roster
                        if($value->employee_id==$rosterArray->employee_id){
                            foreach ($rosterArray->duty as $valueDuty) {
                                if ($value->date==$valueDuty->rosterDate) {
                                     $valueDuty->shift= $value->shift;
                                }
                            }
                        }
            }

        return $rosterArray;
    }

    public function createRosterWeekArray($start, $end, $valueEMP,$format = 'Y-m-d')
    {
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        $rosterMonthlyFull= new \stdClass;
        $rosterMonthlyFull->employee_id = $valueEMP->employee_id;
        $rosterMonthlyFull->name = $valueEMP->name;
        $rosterMonthlyFull->gender = $valueEMP->gender;
        $rosterMonthlyFull->experience_number = $valueEMP->experience_number;
        $rosterMonthlyFull->duty = [];

        foreach($period as $date) {
            $duty= new \stdClass;
            $duty->rosterDate = $date->format($format);;
            $duty->shift = -1;
            $duty->day = date("l", strtotime($date->format($format)));
            $rosterMonthlyFull->duty [] = $duty;

        }

        return $rosterMonthlyFull;
    }
    public function get_all_employee()
    {
        return Employee::orderBy('experience_number','desc')->get();
    }
    public function get_all_employee_edit_roster($ID)
    {
        $employee = Employee::whereHas('roster', function($q) use ($ID){
            $q->where('roster_id',$ID);
        })->orderBy('experience_number','desc')->get();
        return $employee;
    }

}
