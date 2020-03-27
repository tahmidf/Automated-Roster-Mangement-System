<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Auth;
use App\Model\RuleSetting;
use App\Model\Employee;
use App\Model\RuleRoster;
use DateInterval;
use DateTime;
use DatePeriod;
use Cache;
use DB;
use Hoa;
use Carbon\Carbon;
use Log;
use Session;
class RmsAlgorithmController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function ruleEngine($ruleString,$value)
    {
            $ruler = new Hoa\Ruler\Ruler();
            // 1. Write a rule.
            $rule  = $ruleString;

            // 2. Create a context.
            $context           = new Hoa\Ruler\Context();
            $context['value'] = $value;
        return $ruler->assert($rule, $context);
    }
    public function getDataAlgorithm(Request $request)
    {
        $employee = Employee::orderBy('experience_number','desc')->get();
        $rosterDuty = array();
        $roster = new \stdClass;
        $roster->IIG = [];
        $roster->IGW = [];
        $startDate =$request['data'];
        $endDate = Carbon::parse($startDate)->addDays(6);
        $countSenior =0;

        foreach ($employee as $key => $value) {
            if($this->isSenior($value->experience_number,80) && $value->priority_gateway=="IGW"){
                $countSenior++;
            }
        }

        if($countSenior<5)return -1;

        foreach ($employee as $key => $value) {
            if ($value->priority_gateway=="IIG") {
                $roster->IIG[] = $this->createRosterMonthArray($startDate,$endDate->format('Y-m-d'),$value);
            }
            else  $roster->IGW[] = $this->createRosterMonthArray($startDate,$endDate->format('Y-m-d'),$value);
        }
        $rosterDuty[] =$roster;

        $settings = RuleSetting::orderBy('id','asc')->get();

        $this->excuteRule($rosterDuty[0]->IIG,$settings);
        $this->excuteRule($rosterDuty[0]->IGW,$settings);
        $SeniorRule =false;
        foreach ($settings as $key => $value) {

            if($value->rule_name=="igw-rule-each-shift"){
                if($value->value==true)
                $this->shiftEnsureIGW($rosterDuty[0]->IGW);
            }
            elseif($value->rule_name=="female-night"){
                if($value->value==true)
                $this->excuteRuleGender($roster);
            }
            else if($value->rule_name=="senior-rule"){
                if($value->value==true)
                $SeniorRule=true;
            }
        }
        if($SeniorRule==true){
            $infiniteCounter =0;
            $this->seniorRule($rosterDuty[0]->IIG,$infiniteCounter);
        }
        return $rosterDuty;
    }
    public function index()
    {
        $employee = Employee::orderBy('experience_number','desc')->get();
        $rosterDuty = array();
        $roster = new \stdClass;
        $roster->IIG = [];
        $roster->IGW = [];

        foreach ($employee as $key => $value) {
            if ($value->priority_gateway=="IIG") {
                $roster->IIG[] = $this->createRosterMonthArray('2018-05-01','2018-05-07',$value);
            }
            else  $roster->IGW[] = $this->createRosterMonthArray('2018-05-01','2018-05-07',$value);
        }
        $rosterDuty[] =$roster;

        $settings = RuleSetting::orderBy('id','asc')->get();

        $this->excuteRule($rosterDuty[0]->IIG,$settings);
        $this->excuteRule($rosterDuty[0]->IGW,$settings);

        $SeniorRule =false;
        foreach ($settings as $key => $value) {

            if($value->rule_name=="igw-rule-each-shift"){
                if($value->value==true)
                $this->shiftEnsureIGW($rosterDuty[0]->IGW);
            }
            elseif($value->rule_name=="female-night"){
                if($value->value==true)
                $this->excuteRuleGender($roster);
            }
            else if($value->rule_name=="senior-rule"){
                if($value->value==true)
                $SeniorRule=true;
            }
        }
        if($SeniorRule==true){
            $infiniteCounter =0;
            $this->seniorRule($rosterDuty[0]->IIG,$infiniteCounter);
        }
        return view("weekly_roster_pdf_test",compact('rosterDuty'));
    }

    public function excuteRuleGender($rosterDutyArray)
    {
        $collectionOffDay = collect();
        $collectionOffDay = $this->countErrorGenderFemaleOffDay($rosterDutyArray,-1);

        $collectionOffDay->each(function ($item, $key)use ($collectionOffDay) {
               $collectionOffDay->each(function ($itemMap, $keyMap)
                    use ($key,$item,$collectionOffDay) {
                        //while day same for two Female
                        if($key!=$keyMap)
                          if($item['day']==$itemMap['day']){
                             $collectionOffDay->forget($keyMap);

                        }
                });
        });

        $this->genderRuleFemaleChangingShiftByOffDay($collectionOffDay,$rosterDutyArray);
        $day =0;
        $count =0;
        $lastKey =-1;
        while ($day < 7) {
            $count =0;
            foreach ($rosterDutyArray->IIG as $key => $value) {
                if ($value->duty[$day]->shift==-1) {
                    $count++;
                    if($value->gender!="Female")
                      $lastKey=$key;
                }

            }
            if($count>=3 && $lastKey!=-1){
                $startIndex =$day +2;
                if($startIndex>=7) $startIndex=1;
                $shiftedDuty =  $this->switchShiftByIndex($rosterDutyArray->IIG[$lastKey]->duty,$startIndex,sizeof($rosterDutyArray->IGW[$lastKey]->duty));
                $rosterDutyArray->IIG[$lastKey]->duty =$shiftedDuty;
                // Log::info($lastKey);

            }
            $day++;
        }
        //
        // $collection = collect();
        // $collection = $this->countErrorGenderFemale($rosterDutyArray,3);
        // $collection->each(function ($item, $key) use ($collection) {
        //    $collection->each(function ($itemMap, $keyMap)
        //         use ($key,$item,$collection) {
        //             //while day same for two Female
        //             if($key!=$keyMap)
        //               if($item['day']==$itemMap['day']){
        //                  $collection->forget($keyMap);
        //             }
        //     });
        // });
        //
        // // $collection = $collection->values();
        // $this->genderRuleFemale($rosterDutyArray,$collection);
        // // Log::info("==========================================");
        // //
        // $collection->each(function ($item, $key){
        //     Log::info($item);
        // });


    }
    ///other rule
    public function excuteRule($rosterDuty,$settings)
    {
        $shiftPermutationArray=[];
        $shiftPermutationArray = $this->shiftPermutationArrayCreate($shiftPermutationArray);
        $infiniteCounter =0;

        foreach ($settings as $key => $value) {
            if($value->rule_name=="permutation_shift"){
                if($value->value==true)
                $this->purifyShiftArray($rosterDuty,$shiftPermutationArray);
            }
            else if($value->rule_name=="off-day"){
                if($value->value==true)
                $this->offDayCaluclate($rosterDuty);
            }
            else if($value->rule_name=="initial-shift-combination-generate"){
                if($value->value==true)
                $this->ruleRosterInitial($rosterDuty);
            }
            else if($value->rule_name=="senior-rule"){
                if($value->value==true)
                $this->seniorRule($rosterDuty,$infiniteCounter);
            }
        }
    }
    /////gender Rule///////////////////////
    public function genderRuleFemaleChangingShiftByOffDay($collection,$rosterDuty)
    {

        $collection->each(function ($item, $key) use ($rosterDuty,$collection){
            $itemShift=-1;
            if($item['type']=='IGW'){
                if(!$this->isSenior($rosterDuty->IGW[$item['row']]->experience_number,80)){
                    foreach ($collection as $keyValue => $value) {
                        if($keyValue!=$key) {
                            $itemShift = $keyValue;
                            break;
                        }
                        else
                        $itemShift=-1;
                    }
                    if($itemShift!=-1){
                        //where one night female
                        $constantKey   =  $collection[$itemShift]['day'];
                        $changeAbleKey = $item['day'];
                        $tempKey = $constantKey - $changeAbleKey;
                        if($tempKey>0)
                        $startIndex = 7- $tempKey;
                        else
                        $startIndex = abs($tempKey);

                        if($item['type']=="IIG")  {
                          $shiftedDuty = $this->switchShiftByIndex($rosterDuty->IIG[$item['row']]->duty,$startIndex,sizeof($rosterDuty->IIG[$item['row']]->duty));
                          $rosterDuty->IIG[$item['row']]->duty =$shiftedDuty;
                        }
                        else {
                          $shiftedDuty =  $this->switchShiftByIndex($rosterDuty->IGW[$item['row']]->duty,$startIndex,sizeof($rosterDuty->IGW[$item['row']]->duty));
                          $rosterDuty->IGW[$item['row']]->duty =$shiftedDuty;
                        }
                        $collection->forget($itemShift);
                        $collection->forget($key);

                    }
                }
            }
            else{
            $itemShift=-1;
            if($item['type']=='IIG'){
                foreach ($collection as $keyValue => $value) {
                    if($keyValue!=$key) {
                        $itemShift = $keyValue;
                        break;
                    }
                }
                if($itemShift!=-1){

                  $constantKey   =   $collection[$itemShift]['day'];
                  $changeAbleKey =   $item['day'];
                  $tempKey = $constantKey - $changeAbleKey;
                  if($tempKey>0)
                  $startIndex = 7- $tempKey;
                  else
                  $startIndex = abs($tempKey);

                  if($item['type']=="IIG")  {
                     // Log::info( $rosterDuty->IIG[$item['row']]->duty);

                    $shiftedDuty = $this->switchShiftByIndex($rosterDuty->IIG[$item['row']]->duty,$startIndex,sizeof($rosterDuty->IIG[$item['row']]->duty));
                    $rosterDuty->IIG[$item['row']]->duty =$shiftedDuty;
                    // Log::info( $shiftedDuty);
                    // Log::info( $rosterDuty->IIG[$item['row']]->name);


                  }
                 else {

                    $shiftedDuty =  $this->switchShiftByIndex($rosterDuty->IGW[$item['row']]->duty,$startIndex,sizeof($rosterDuty->IGW[$item['row']]->duty));
                    $rosterDuty->IGW[$item['row']]->duty =$shiftedDuty;
                 }
                $collection->forget($itemShift);
                $collection->forget($key);

              }

            }
        }
        });

    }
    public function genderRuleFemale($rosterDuty,$collection)
    {

        ///Changing shift For Night Shift
        $this->genderRuleFemaleChangingShift($collection,$rosterDuty);
       // Log::info("==========================================");

    }
    public function genderRuleFemaleChangingShift($collection,$rosterDuty)
    {

        $collection->each(function ($item, $key) use ($rosterDuty,$collection){
            $itemShift=-1;
            if($item['type']=='IGW'){
                if(!$this->isSenior($rosterDuty->IGW[$item['row']]->experience_number,80)){
                    foreach ($collection as $keyValue => $value) {
                        if($keyValue!=$key) {
                            $itemShift = $keyValue;
                            break;
                        }
                        else
                        $itemShift=-1;
                    }
                    if($itemShift!=-1){
                        //where one night female
                        $constantKey   =  $collection[$itemShift]['day'];
                        $changeAbleKey = $item['day'];
                        $tempKey = $constantKey - $changeAbleKey;
                        if($tempKey>0)
                        $startIndex = 7- $tempKey;
                        else
                        $startIndex = abs($tempKey);

                        if($item['type']=="IIG")  {
                          $shiftedDuty = $this->switchShiftByIndex($rosterDuty->IIG[$item['row']]->duty,$startIndex,sizeof($rosterDuty->IIG[$item['row']]->duty));
                          $rosterDuty->IIG[$item['row']]->duty =$shiftedDuty;
                        }
                        else {
                          $shiftedDuty =  $this->switchShiftByIndex($rosterDuty->IGW[$item['row']]->duty,$startIndex,sizeof($rosterDuty->IGW[$item['row']]->duty));
                          $rosterDuty->IGW[$item['row']]->duty =$shiftedDuty;
                        }
                        $collection->pull($itemShift);
                        $collection->pull($key);
                    }
                    else{
                         $nextKey = $item['day']+1;
                       if($nextKey==7)$nextKey=0;
                       if($this->countGenderFemale($rosterDuty,$item['day'])==-1){
                           if($rosterDuty->IGW[$item['row']]->duty[$nextKey]->shift!=-1){
                                 $rosterDuty->IGW[$item['row']]->duty[$item['day']]->shift =2;
                                 $collection->pull($key);
                           }
                        }
                    }

                }
            }
            else{
            if($item['type']=='IIG'){
                foreach ($collection as $keyValue => $value) {
                    if($keyValue!=$key) {
                        $itemShift = $keyValue;
                        break;
                    }
                }
                if($itemShift!=-1){

                  $constantKey   =   $collection[$itemShift]['day'];
                  $changeAbleKey = $item['day'];
                  $tempKey = $constantKey - $changeAbleKey;
                  if($tempKey>0)
                  $startIndex = 7- $tempKey;
                  else
                  $startIndex = abs($tempKey);

                  if($item['type']=="IIG")  {

                    $shiftedDuty = $this->switchShiftByIndex($rosterDuty->IIG[$item['row']]->duty,$startIndex,sizeof($rosterDuty->IIG[$item['row']]->duty));
                    $rosterDuty->IIG[$item['row']]->duty =$shiftedDuty;


                  }
                 else {

                    $shiftedDuty =  $this->switchShiftByIndex($rosterDuty->IGW[$item['row']]->duty,$startIndex,sizeof($rosterDuty->IGW[$item['row']]->duty));
                    $rosterDuty->IGW[$item['row']]->duty =$shiftedDuty;
                 }
                $collection->pull($itemShift);
                $collection->pull($key);
              }
              else {
                   $nextKey = $item['day']+1;
                     if($nextKey==7)$nextKey=0;
                     if($this->countGenderFemale($rosterDuty,$item['day'])==-1){
                         if($rosterDuty->IIG[$item['row']]->duty[$nextKey]->shift!=-1){
                               $rosterDuty->IIG[$item['row']]->duty[$item['day']]->shift =2;
                               $collection->pull($key);
                              // Log::info($rosterDuty->IIG[$item['row']]->name);

                         }
                      }
              }

            }
        }
        });



    }
    public function countGenderFemale($array,$day)
    {
            $count=0;
            foreach ($array->IIG as $row => $value) {
                if ($value->gender=="Female") {
                    if($value->duty[$day]->shift==3)
                       $count++;
                }
            }
            foreach ($array->IGW as $row => $value) {
                if ($value->gender=="Female") {
                    if($value->duty[$day]->shift==3)
                       $count++;
                }
            }
            if($count<=1)
            {
                return -1;
            }
        return $day;

    }
    public function countErrorGenderFemaleOffDay($array,$countValue){
        $count =0;
        $collection = collect();

        foreach ($array->IGW as $row => $value) {
            if ($value->gender=="Female") {
                foreach ($value->duty as $key => $valueDuty) {
                    $nextKey = $key+1;
                    if($nextKey==7) $nextKey =0;
                    if($valueDuty->shift==$countValue &&
                    $value->duty[$nextKey]->shift==$countValue){
                        $collection->push(
                           [
                                'type'=>'IGW',
                                'row'=>$row,
                                'day'=>$key
                           ]
                        );
                    }
                }
            }
        }
        foreach ($array->IIG as $row => $value) {
            if ($value->gender=="Female") {
                foreach ($value->duty as $key => $valueDuty) {
                    $nextKey = $key+1;
                    if($nextKey==7) $nextKey =0;
                    if($valueDuty->shift==$countValue &&
                       $value->duty[$nextKey]->shift==$countValue){
                        $collection->push(
                           [
                               'type'=>'IIG',
                                'row'=>$row,
                                'day'=>$key
                           ]
                        );
                    }
                }
            }
        }
        return $collection;
    }
    public function countErrorGenderFemale($array,$countValue){
        $count =0;
        $collection = collect();
        foreach ($array->IIG as $row => $value) {
            if ($value->gender=="Female") {
                foreach ($value->duty as $key => $valueDuty) {
                    if($valueDuty->shift==$countValue){
                        $collection->push(
                           [
                               'type'=>'IIG',
                                'row'=>$row,
                                'day'=>$key
                           ]
                        );
                    }
                }
            }
        }
        foreach ($array->IGW as $row => $value) {
            if ($value->gender=="Female") {
                foreach ($value->duty as $key => $valueDuty) {
                    if($valueDuty->shift==$countValue){
                        $collection->push(
                           [
                                'type'=>'IGW',
                                'row'=>$row,
                                'day'=>$key
                           ]
                        );
                    }
                }
            }
        }
        return $collection;
    }
    ////////////seniorRule//////////////////////////
    public function seniorRule($array,$infiniteCounter)
    {

        $count=0;
        $infiniteCounter++;

        while ($count <7) {
              $shifCounterArray = $this->shiftCounter();
              foreach ($array as $key => $value) {
                if($this->isSenior($value->experience_number,80)){
                  if($value->duty[$count]->shift!=-1){
                      if ($value->duty[$count]->shift==1) {
                            $shifCounterArray[0]->count++;
                            $shifCounterArray[0]->index[] = $key;
                      }
                      elseif ($value->duty[$count]->shift==2) {
                            $shifCounterArray[1]->count++;
                            $shifCounterArray[1]->index []= $key;
                      }
                      elseif ($value->duty[$count]->shift==3) {
                            $shifCounterArray[2]->count++;
                            $shifCounterArray[2]->index []= $key;
                      }

                  }
              }
           }

           $this->changeShiftSeniorRule($shifCounterArray,$array,$count);
           $count++;

        }
        if($infiniteCounter>=5) {
            //IF Error Found All Rule are calling one by one again
            $this->offDayCancel($array);

            $settings = RuleSetting::orderBy('id','asc')->get();
            $this->excuteRule($array,$settings);
        }
        else
        $this->errorPossibiltyCounter($array,$infiniteCounter);
    }
    public function errorPossibiltyCounter($array,$infiniteCounter)
    {
                $count=0;


                    while ($count <7) {
                        $mornError =0;
                        $nightError =0;
                        $eveError =0;
                      foreach ($array as $key => $value) {
                        if($this->isSenior($value->experience_number,80)){
                          if($value->duty[$count]->shift!=-1){
                              if ($value->duty[$count]->shift==1) {
                                    $mornError++;
                              }
                              elseif ($value->duty[$count]->shift==2) {
                                    $eveError++;
                              }
                              elseif ($value->duty[$count]->shift==3) {
                                    $nightError++;
                              }

                          }
                       }
                    }
                    if($nightError==0||$mornError==0||$eveError==0){
                        $this->seniorRule($array,$infiniteCounter);
                    }
                   $count++;
                }
    }
    public function changeShiftSeniorRule($shiftCountArray,$rosterDuty,$row)
    {
        $checkIndex = 0;
        foreach ($shiftCountArray as $key => $value) {
            if ($value->count>1) {
                foreach ($value->index as $valueErrorIndex) {

                        $forwardIndex=0;
                        if($row==6) $forwardIndex=0;
                        else $forwardIndex=$row+1;

                        if($row==0) $checkIndex=6;
                        else $checkIndex=$row-1;
                            if($rosterDuty[$valueErrorIndex]->duty[$checkIndex]->shift!=-1 &&
                               $rosterDuty[$valueErrorIndex]->duty[$forwardIndex]->shift!=-1
                            ){

                                    if($shiftCountArray[0]->count==0){
                                        if($rosterDuty[$valueErrorIndex]->duty[$checkIndex]->shift==1){
                                            $shiftCountArray[0]->count++;
                                            $rosterDuty[$valueErrorIndex]->duty[$row]->shift =1;
                                        }
                                    }
                                    elseif($shiftCountArray[1]->count==0){
                                        if($rosterDuty[$valueErrorIndex]->duty[$checkIndex]->shift==1 ||
                                            $rosterDuty[$valueErrorIndex]->duty[$checkIndex]->shift==2){
                                            $shiftCountArray[1]->count++;
                                            $rosterDuty[$valueErrorIndex]->duty[$row]->shift =2;
                                        }
                                        if($rosterDuty[$valueErrorIndex]->duty[$forwardIndex]->shift==1){
                                            $rosterDuty[$valueErrorIndex]->duty[$forwardIndex]->shift =2;
                                        }
                                    }
                                    elseif($shiftCountArray[2]->count==0){

                                      if($rosterDuty[$valueErrorIndex]->duty[$forwardIndex]->shift==3
                                        ){
                                            $forwardForwardIndex=0;
                                            if($row==6) $forwardForwardIndex=1;
                                            elseif($row==5) $forwardForwardIndex=0;
                                            else $forwardForwardIndex=$row+2;

                                            if($rosterDuty[$valueErrorIndex]->duty[$forwardForwardIndex]->shift!=3){
                                            $shiftCountArray[2]->count++;
                                            $rosterDuty[$valueErrorIndex]->duty[$row]->shift =3;
                                          }
                                      }
                                    }
                            }
                }
            }
        }

    }
    public function changeShiftIGWRule($shiftCountArray,$rosterDuty,$row)
    {
        $checkIndex = 0;
        foreach ($shiftCountArray as $key => $value) {
            if ($value->count>2) {
                foreach ($value->index as $valueErrorIndex) {
                    if(!$this->isSenior($rosterDuty[$valueErrorIndex]->experience_number,80)){
                       // Log::info($rosterDuty[$valueErrorIndex]->name);
                        $forwardIndex=0;
                        if($row==6) $forwardIndex=0;
                        else $forwardIndex=$row+1;

                        if($row==0) $checkIndex=6;
                        else $checkIndex=$row-1;
                            if($rosterDuty[$valueErrorIndex]->duty[$checkIndex]->shift!=-1 &&
                               $rosterDuty[$valueErrorIndex]->duty[$forwardIndex]->shift!=-1
                            ){

                                    if($shiftCountArray[0]->count==1){
                                       // Log::info("s");
                                        if($rosterDuty[$valueErrorIndex]->duty[$checkIndex]->shift==1){
                                            $shiftCountArray[0]->count++;
                                            $rosterDuty[$valueErrorIndex]->duty[$row]->shift =1;
                                        }
                                    }
                                    elseif($shiftCountArray[1]->count==1){
                                        if($rosterDuty[$valueErrorIndex]->duty[$checkIndex]->shift==1 ||
                                            $rosterDuty[$valueErrorIndex]->duty[$checkIndex]->shift==2){
                                            $shiftCountArray[1]->count++;
                                            $rosterDuty[$valueErrorIndex]->duty[$row]->shift =2;
                                        }
                                        if($rosterDuty[$valueErrorIndex]->duty[$forwardIndex]->shift==1){
                                            $rosterDuty[$valueErrorIndex]->duty[$forwardIndex]->shift =2;
                                        }
                                    }
                                    elseif($shiftCountArray[2]->count==1){

                                      if($rosterDuty[$valueErrorIndex]->duty[$forwardIndex]->shift==3
                                        ){
                                            $forwardForwardIndex=0;
                                            if($row==6) $forwardForwardIndex=1;
                                            elseif($row==5) $forwardForwardIndex=0;
                                            else $forwardForwardIndex=$row+2;

                                            if($rosterDuty[$valueErrorIndex]->duty[$forwardForwardIndex]->shift!=3){
                                            $shiftCountArray[2]->count++;
                                            $rosterDuty[$valueErrorIndex]->duty[$row]->shift =3;
                                          }
                                      }
                                    }
                            }
                   }
                }
            }
        }

    }
    ///Two IGW on eache shift RULE
    public function getTwoShiftIndex($array,$option){
        $count=0;
        $errorIndex =0;
        while ($count <7) {
            $errorIndex =0;
            foreach ($array as $key => $value) {
                if($value->duty[$count]->shift==-1)
                    $errorIndex++;
            }
            if($errorIndex<=2){
                $errorIndex =0;
                if($option==1){
                    $firstShift = $count;
                    $nextshift =  $count+1;
                    if($nextshift==7)
                    $nextshift =0;
                }
                else{
                    $firstShift = $count;
                    $nextshift =  $count-1;
                    if($nextshift==-1)
                    $nextshift =6;
                }
                foreach ($array as $key => $value) {
                    if($value->duty[$nextshift]->shift==-1)
                        $errorIndex++;
                }
                if($errorIndex<=2){
                    return $count;
                }
            }
            $count++;
        }
        return -1;
    }
    public function shiftEnsureIGW($array){
        $this->shiftEnsureIGWInit($array);
        $count=0;
        while ($count <7) {
              $shifCounterArray = $this->shiftCounter();
              foreach ($array as $key => $value) {
                  if($value->duty[$count]->shift!=-1){
                      if ($value->duty[$count]->shift==1) {
                            $shifCounterArray[0]->count++;
                            $shifCounterArray[0]->index[] = $key;
                      }
                      elseif ($value->duty[$count]->shift==2) {
                            $shifCounterArray[1]->count++;
                            $shifCounterArray[1]->index []= $key;
                      }
                      elseif ($value->duty[$count]->shift==3) {
                            $shifCounterArray[2]->count++;
                            $shifCounterArray[2]->index []= $key;
                      }

              }
           }
         //  Log::info($shifCounterArray);
           $this->changeShiftIGWRule($shifCounterArray,$array,$count);
           $count++;

        }
    }
    public function shiftEnsureIGWInit($array){
        $count=0;
        $errorIndex =0;
        $changableIndex =0;
        while ($count <7) {
            $errorIndex =0;
            foreach ($array as $key => $value) {
                if($value->duty[$count]->shift==-1){
                    $errorIndex++;
                    $changableIndex =$key;
                    if($errorIndex>=4){
                        $errorIndex=0;
                      if(!$this->isSenior($array[$changableIndex]->experience_number,80)){
                            $firstShift = $count;
                            $nextshift =  $count+1;
                            if($nextshift==7)
                            $nextshift =0;
                        if($array[$changableIndex]->duty[$nextshift]->shift==-1)
                            $startIndex = $this->getTwoShiftIndex($array,1);
                        else
                            $startIndex = $this->getTwoShiftIndex($array,2);

                        if ($startIndex!=-1) {
                            // Log::info($count.' '.$startIndex.' '.$changableIndex);

                            //where one night female
                            $constantKey   =  $startIndex;
                            $changeAbleKey = $count;
                            $tempKey = $constantKey - $changeAbleKey;
                            if($tempKey>0)
                            $startIndex = 7- $tempKey;
                            else
                            $startIndex = abs($tempKey);
                            $shiftedDuty = $this->switchShiftByIndex($array[$changableIndex]->duty,$startIndex,sizeof($array[$changableIndex]->duty));
                            $array[$changableIndex]->duty= $shiftedDuty;
                            // Log::info($count.' '.$startIndex.' '.$changableIndex);
                            return;
                        }
                    }
                  }
                }

            }
            $count++;
        }
    }

    public  function  switchShiftByIndex($array,$startIndex,$size){
            $shifttedNewArray = [];
            for ($i=$startIndex; $i < $size+$startIndex; $i++) {
                $shifttedNewArray [] = $array[$i%$size];
            }
            //$array= $shifttedNewArray;
           return $shifttedNewArray;
    }
    public function offDayGenderFemale($rosterDuty){
        $count =0;
        $offDay =-1;
        $offDay1 =-1;

        foreach ($rosterDuty as $value) {
            if($value->gender=="Female"){
                foreach($value->duty as $key=>$valueDuty){
                    if($valueDuty->shift==-1){
                        $count++;
                        if($count==1)
                        $offDay = $key;
                        if($count==2)
                        $offDay1 = $key;
                    }
                }
            }
        }
    }
    public function offDayCaluclate($rosterDuty)
    {
        $off_day_senior=0;
        $offDaySenior=[];
        $offDaySenior = $this->OffDayCombination();
        $tempoffDaySenior=$offDaySenior;

        $offDayJunior=[];
        $offDayJunior = $this->OffDayCombination();
        $tempoffDayJunior=$offDayJunior;
        foreach ($rosterDuty as $value) {
            if(!$this->isSenior($value->experience_number,80)){
                //off day calculation when not senior
                $offDayJunior = $this->getRandomOffDaySeniorJuniorIndex($tempoffDayJunior);
                if(!is_int($offDayJunior)){

                    $value->duty[$offDayJunior[0][0]]->shift = -1;
                    $value->duty[$offDayJunior[0][1]]->shift = -1;

                    if($offDayJunior[0][0]-1==-1)
                    $offDayJunior[0][0]=6;

                    if($offDayJunior[0][1]==6){
                        $value->duty[$offDayJunior[0][0]]->shift = 1;
                        $value->duty[$offDayJunior[0][0]-1]->shift = 3;
                    }
                    else {
                        $value->duty[$offDayJunior[0][1]+1]->shift = 1;
                        $value->duty[$offDayJunior[0][0]-1]->shift = 3;
                    }

                }
                else {
                    $off_day= $this->randomOffDayInex();
                    $off_day1=$off_day+1;
                    if($off_day==6)
                    $off_day1=0;

                    $value->duty[$off_day]->shift = -1;
                    $value->duty[$off_day1]->shift = -1;

                    $first_shift=$off_day;
                    $last_shift=$off_day1;
                    if($first_shift-1==-1)
                    $first_shift=6;

                    if($last_shift==6){
                        $value->duty[0]->shift = 1;
                        $value->duty[$first_shift-1]->shift = 3;
                    }
                    else {
                        $value->duty[$last_shift+1]->shift = 1;
                        $value->duty[$first_shift-1]->shift = 3;
                    }
                }
            }
            else {
                // code...
                //off day calculation when not Junior

                $offDaySenior = $this->getRandomOffDaySeniorJuniorIndex($tempoffDaySenior);
                if(!is_int($offDaySenior)){

                    $value->duty[$offDaySenior[0][0]]->shift = -1;
                    $value->duty[$offDaySenior[0][1]]->shift = -1;

                    if($offDaySenior[0][0]-1==-1)
                    $offDaySenior[0][0]=6;

                    if($offDaySenior[0][1]==6){
                        $value->duty[$offDaySenior[0][0]]->shift = 1;
                        $value->duty[$offDaySenior[0][0]-1]->shift = 3;
                    }
                    else {
                        $value->duty[$offDaySenior[0][1]+1]->shift = 1;
                        $value->duty[$offDaySenior[0][0]-1]->shift = 3;
                    }

                }
                else {
                    $off_day= $this->randomOffDayInex();
                    $off_day1=$off_day+1;



                    if($off_day==6)
                    $off_day1=0;

                    $value->duty[$off_day]->shift = -1;
                    $value->duty[$off_day1]->shift = -1;

                    $first_shift=$off_day;
                    $last_shift=$off_day1;
                    if($first_shift-1==-1)
                    $first_shift=6;

                    if($last_shift==6){
                        $value->duty[0]->shift = 1;
                        $value->duty[$first_shift-1]->shift = 3;
                    }
                    else {
                        $value->duty[$last_shift+1]->shift = 1;
                        $value->duty[$first_shift-1]->shift = 3;
                    }

                    $startIndex = $this->countSeniorOffDaysError($rosterDuty,$off_day,$off_day1);
                    if ($startIndex!=-1) {

                        $shiftedDuty = $this->switchShiftByIndex($value->duty,$startIndex+2,sizeof($value->duty));
                        $value->duty = $shiftedDuty;

                    }
                }
            }
        }
    }
    //seniorRule
    public function OffDayCombination()
    {
        // code...
        $offDay = array();

        $days = new \stdClass;
        $days->value =  array();

        $days->value [] =[0,1];
        $days->status= false;
        $offDay [] =$days;


        $days = new \stdClass;
        $days->value =  array();
        $days->value []=  [2,3];
        $days->status= false;
        $offDay [] =$days;


        $days = new \stdClass;
        $days->value =  array();
        $days->value []=  [4,5];
        $days->status= false;
        $offDay [] =$days;


        $days = new \stdClass;
        $days->value =  array();
        $days->value []=  [6,0];
        $days->status= false;
        $offDay [] =$days;
        return $offDay;

    }
    //seniorRule
    public function countSeniorOffDaysError($array,$offDay,$offDay1)
    {
            $count=0;
            foreach ($array as $key => $value) {
                if($value->duty[$offDay]->shift==-1)
                $count++;
            }
            if($count==3)return $offDay;

            $count=0;
            foreach ($array as $key => $value) {
                if($value->duty[$offDay1]->shift==-1)
                $count++;
            }
            if($count==3)return $offDay1;

            return -1;

    }
    //seniorRule
    public function getRandomOffDaySeniorJuniorIndex($array)
    {
            $count=0;
            $randNumber = $this->getRandomShift(0,3);
            if($array[$randNumber]->status==false){
                $array[$randNumber]->status =true;
                return $array[$randNumber]->value;
            }
            else {

                foreach ($array as $key => $value) {
                    if($value->status==true)
                    $count++;
                }
                if($count==4)
                return $count;

                return $this->getRandomOffDaySeniorJuniorIndex($array);
            }

    }
    public function getEditableIndexRoster($rosterDuty)
    {
        $found_index = [];
            foreach ($rosterDuty as $key => $valueDuty) {
                        if($valueDuty->shift==-1){
                            if($key==5)
                            array_push($found_index,1,2,3);
                            elseif($key==0 && $rosterDuty[$key+1]->shift==-1)
                            array_push($found_index,3,4,5);
                            elseif($key==0)
                            array_push($found_index,2,3,4);
                            elseif($key==2)
                            array_push($found_index,$key+3,$key+4,$key-2);
                            elseif($key==3)
                            array_push($found_index,6,0,1);
                            elseif($key==1)
                            array_push($found_index,4,5,6);
                            elseif($key==4)
                            array_push($found_index,0,1,2);
                            break;

                        }
            }
            return $found_index;
    }
        //Recursion of randomIndexNumber
    //don't get excited more comming up
    public function randomIndexNumber($array)
    {   $found =-1;
        if(sizeof($array)>=7){
            return $array;
        }
        else {
            $randNumber=rand(0,6);
            foreach ($array as $key => $value) {
                if($value==$randNumber){
                    $found=1;
                }
            }
            if($found==-1 && sizeof($array)!=7){
                $array []=$randNumber;
            }
          return $this->randomIndexNumber($array);
        }
        return null;

    }
    public function offDayCancel($array)
    {
        // code...
        foreach ($array as $value) {
            foreach ($value->duty as $key => $valueDuty) {
                    if($valueDuty->shift==-1){
                        $valueDuty->shift=$this->getRandomShift(1,3);
                    }
            }
        }
    }
    public function shiftCounter()
    {
        //morning
        $shifCounter = new \stdClass;
        $shifCounter->shift = 1;
        $shifCounter->count = 0;
        $shifCounter->index=  array();
        $shifCounterArray [] =$shifCounter;

        ///evening
        $shifCounter = new \stdClass;
        $shifCounter->shift = 2;
        $shifCounter->count = 0;
        $shifCounter->index=  array();
        $shifCounterArray [] =$shifCounter;

        ///night
        $shifCounter = new \stdClass;
        $shifCounter->shift = 3;
        $shifCounter->count = 0;
        $shifCounter->index=  array();
        $shifCounterArray [] =$shifCounter;

        return $shifCounterArray;
    }
    public function ruleRosterInitial($rosterDuty)
    {
        $count=1;
        foreach ($rosterDuty as $value) {
            $count=1;
            $foundEditable=[];
            $foundEditable = $this->getEditableIndexRoster($value->duty);
            foreach ($foundEditable as $key => $valueIndex) {

                        if($valueIndex==0) $previousShift=6;
                        else $previousShift=$valueIndex-1;

                        if($count==1){
                            if($value->duty[$previousShift]->shift==1){
                                $value->duty [$valueIndex]->shift= $this->getRandomShift(1,2);
                            }
                            $count=2;

                        }
                        elseif ($count==2) {
                            if($value->duty[$previousShift]->shift==1){
                                $value->duty [$valueIndex]->shift= $this->getRandomShift(1,2);
                            }
                            elseif($value->duty[$previousShift]->shift==2){
                                $value->duty [$valueIndex]->shift= 2;
                            }
                            $count=3;
                        }
                        else{
                            if($value->duty[$previousShift]->shift==1){
                                $value->duty [$valueIndex]->shift= $this->getRandomShift(1,3);
                            }
                            elseif($value->duty[$previousShift]->shift==2){
                                $value->duty [$valueIndex]->shift=$this->getRandomShift(2,3);
                            }
                            $count=1;

                        }
                    }
                }
    }
    public function purifyShiftArray($rosterDuty,$shiftPermutationArray)
    {
        $previousShift =-1;
        foreach ($rosterDuty as $value) {
            foreach ($value->duty as $key => $valueDuty) {

                if($key==0) $previousShift=6;
                else $previousShift=$key-1;
                //current shift check
                foreach ($shiftPermutationArray[$valueDuty->shift-1]->false_value as $valueShift) {

                        //previous shift
                        if($value->duty[$previousShift]->shift!=$valueShift){

                            if($value->duty[$previousShift]->shift==2)
                            $valueDuty->shift= $this->getRandomShift(2,3);
                            elseif ($value->duty[$previousShift]->shift==3) {
                                $valueDuty->shift= 3;
                            }
                        }
                }
            }
        }
    }
    public function shiftPermutationArrayCreate($array)
    {
        //morning
        $shiftPermutation = new \stdClass;
        $shiftPermutation->shift_value = 1;
        $shiftPermutation->false_value=  array();
        array_push($shiftPermutation->false_value,[1]);
        $array [] =$shiftPermutation;

        ///evening
        $shiftPermutation = new \stdClass;
        $shiftPermutation->shift_value = 2;
        $shiftPermutation->false_value=  array();
        array_push($shiftPermutation->false_value,[1,2]);
        $array [] =$shiftPermutation;

        ///night
        $shiftPermutation = new \stdClass;
        $shiftPermutation->shift_value = 3;
        $shiftPermutation->false_value=  array();
        array_push($shiftPermutation->false_value,[1,2,3]);
        $array [] =$shiftPermutation;

        return $array;

    }

    public function  shiftCalculate($array,$shiftValue){
        $count=0;
        foreach ($array as $item) {
            if($item->shift==$shiftValue)
                $count++;
        }

        return $count;
    }
    public function randomOffDayInex()
    {
        $randNumber=rand(0,6);
        return $randNumber;
    }
    public function getRandomShift($first,$last)
    {
        return $randNumber=rand($first,$last);
    }
    public function  excuteRuleShiftMostNumber($numberShift){
        return $this->ruleEngine('value < 2',$numberShift);
    }
    public function excuteNumberOfShiftRule($numberShift)
    {
        return$this->ruleEngine('value < 5',$numberShift);
    }

    public function createRosterMonthArray($start, $end, $valueEMP,$format = 'Y-m-d')
    {
        $array = array();
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
            $duty->shift = $this->getRandomShift(1,3);
            $duty->day = date("l", strtotime($date->format($format)));

            $rosterMonthlyFull->duty [] = $duty;

        }

        return $rosterMonthlyFull;
    }
    /////////////Difining RULE Engine RULER

    public function isSenior($empEXP,$valueEXP)
    {
        if($empEXP>=$valueEXP)
        return true;
    }
    public function genderCount($array, $valueGender)
    {
        $count =0;
        while ($count < 7) {

            foreach ($array as $value) {
                    if($value->gender==$valueGender)
                    $count++;
            }
        }
        return $count;
    }


}
