@extends('layouts.app')
@section('content')
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <?php
    echo Session::put('message', '');
    ?>
    @if (session('info'))
        <div class="alert alert-danger">
            {{ session('info') }}
        </div>
    @endif
    <?php
    echo Session::put('info', '');
    ?>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
            <div class="col-sm-3">
                <h5>Select Start Date</h5>
            <div class="input-group date" style="z-index: 999">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" id="startDate" class="datepicker form-control pull-right">
            </div>
            <br/>
            <button type="button" class="btn btn-sm btn-primary"name="button" onclick="dateGet()">Procced</button>
        </div>
        <div class="col-sm-3">
                <h5>End Date</h5>
              <input type="text" id="endDate" class="form-control pull-right" disabled>
         </div>
    </div>
    <br/>
      <div class="box box-danger" id="boxRoster">
           <div class="box-header">
               <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
             <h3 class="box-title">Roster Entry</h3>

           </div>
            <div class="box-body">
                <ul class="timeline">
                <li class="time-label">

                </li>
                <li>
                    {{-- employeeIIG --}}
                    <i class="fa fa-user bg-blue"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header"><a href="#">IGW </a> Engineers</h3>

                        <div class="timeline-body">

                                <button type="button" class="btn btn-flat bg-maroon" onclick="getDataAlgorithm()" name="button">
                                    <i class="class fa fa-snowflake-o "> Generate Roster By Algorithm</i>
                                </button>
                                <button type="button" class="btn btn-primary btn-flat"name="button"
                                  data-toggle="modal" data-target="#copy_rosters" >
                                  <i class="fa fa-clipboard"> Copy Previous Rosters</i>
                                </button>
                                <br /><br />
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <td>SL.</td>
                                            <td>Engineer</td>
                                            <td id="day0">Sun</td>
                                            <td id="day1">Mon</td>
                                            <td id="day2">Tue</td>
                                            <td id="day3">Wed</td>
                                            <td id="day4">Thu</td>
                                            <td id="day5">Fri</td>
                                            <td id="day6">Sat</td>

                                        </tr>
                                    </thead>
                                    <tbody>
                                            @foreach ($employeeIGW as $key => $value)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$value->name}}@if ($value->experience_number>=80)
                                                    (SR.)
                                                @endif</td>
                                                <td>
                                                    <select class="form-control select2" style="width: 100%;" id='day0{{$value->employee_id}}'>
                                                      <option selected="selected">Select</option>
                                                      <option value="1">MOR</option>
                                                      <option value="3">NIGHT</option>
                                                      <option value="2">EVE</option>
                                                      <option value="-1">OFF</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" style="width: 100%;" id='day1{{$value->employee_id}}'>
                                                      <option selected="selected">Select</option>
                                                      <option value="1">MOR</option>
                                                      <option value="3">NIGHT</option>
                                                      <option value="2">EVE</option>
                                                      <option value="-1">OFF</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" style="width: 100%;" id='day2{{$value->employee_id}}'>
                                                      <option selected="selected">Select</option>
                                                      <option value="1">MOR</option>
                                                      <option value="3">NIGHT</option>
                                                      <option value="2">EVE</option>
                                                      <option value="-1">OFF</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" style="width: 100%;" id='day3{{$value->employee_id}}'>
                                                      <option selected="selected">Select</option>
                                                      <option value="1">MOR</option>
                                                      <option value="3">NIGHT</option>
                                                      <option value="2">EVE</option>
                                                      <option value="-1">OFF</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" style="width: 100%;" id='day4{{$value->employee_id}}'>
                                                      <option selected="selected">Select</option>
                                                      <option value="1">MOR</option>
                                                      <option value="3">NIGHT</option>
                                                      <option value="2">EVE</option>
                                                      <option value="-1">OFF</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" style="width: 100%;" id='day5{{$value->employee_id}}'>
                                                      <option selected="selected">Select</option>
                                                      <option value="1">MOR</option>
                                                      <option value="3">NIGHT</option>
                                                      <option value="2">EVE</option>
                                                      <option value="-1">OFF</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control select2" style="width: 100%;" id='day6{{$value->employee_id}}'>
                                                      <option selected="selected">Select</option>
                                                      <option value="1">MOR</option>
                                                      <option value="3">NIGHT</option>
                                                      <option value="2">EVE</option>
                                                      <option value="-1">OFF</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            @endforeach
                                    </tbody>
                                </table>


                        </div>
                    </div>

                </li>
                <li>
                    {{-- employeeIGW --}}
                    <i class="fa fa-user bg-red"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header"><a href="#">IIG </a> Engineers</h3>
                        <div class="timeline-body">
                                <br />
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <td>SL.</td>
                                            <td>Engineer</td>
                                            <td id="day7">Sun</td>
                                            <td id="day8">Mon</td>
                                            <td id="day9">Tue</td>
                                            <td id="day10">Wed</td>
                                            <td id="day11">Thu</td>
                                            <td id="day12">Fri</td>
                                            <td id="day13">Sat</td>

                                        </tr>
                                    </thead>
                                    <tbody>
                                            @foreach ($employeeIIG as $key => $value)
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>{{$value->name}}</td>
                                                    <td>
                                                        <select class="form-control select2" style="width: 100%;" id='day0{{$value->employee_id}}'>
                                                            <option selected="selected">Select</option>
                                                          <option value="1">MOR</option>
                                                          <option value="3">NIGHT</option>
                                                          <option value="2">EVE</option>
                                                          <option value="-1">OFF</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width: 100%;" id='day1{{$value->employee_id}}'>
                                                          <option selected="selected">Select</option>
                                                          <option value="1">MOR</option>
                                                          <option value="3">NIGHT</option>
                                                          <option value="2">EVE</option>
                                                          <option value="-1">OFF</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width: 100%;" id='day2{{$value->employee_id}}'>
                                                          <option selected="selected">Select</option>
                                                          <option value="1">MOR</option>
                                                          <option value="3">NIGHT</option>
                                                          <option value="2">EVE</option>
                                                          <option value="-1">OFF</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width: 100%;" id='day3{{$value->employee_id}}'>
                                                          <option selected="selected">Select</option>
                                                          <option value="1">MOR</option>
                                                          <option value="3">NIGHT</option>
                                                          <option value="2">EVE</option>
                                                          <option value="-1">OFF</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width: 100%;" id='day4{{$value->employee_id}}'>
                                                          <option selected="selected">Select</option>
                                                          <option value="1">MOR</option>
                                                          <option value="3">NIGHT</option>
                                                          <option value="2">EVE</option>
                                                          <option value="-1">OFF</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width: 100%;" id='day5{{$value->employee_id}}'>
                                                          <option selected="selected">Select</option>
                                                          <option value="1">MOR</option>
                                                          <option value="3">NIGHT</option>
                                                          <option value="2">EVE</option>
                                                          <option value="-1">OFF</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width: 100%;" id='day6{{$value->employee_id}}'>
                                                          <option selected="selected">Select</option>
                                                          <option value="1">MOR</option>
                                                          <option value="3">NIGHT</option>
                                                          <option value="2">EVE</option>
                                                          <option value="-1">OFF</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach
                                    </tbody>
                                </table>


                        </div>
                    </div>
                </li>

                </ul>
                <button type="button" class="btn btn-flat bg-olive" onclick="saveRoster()"> Save Roster</button>

            </div>
            <div class="overlay">
                <i class="fa fa-hourglass-3 fa-spin"></i>
            </div>

     </div>
     @component('components.modal')
        @slot('ID')
            copy_rosters
        @endslot
        @slot('title')
            Copy Roster Previous
        @endslot
        @slot('body')
        <table  id="rostersTBL" class="table table-hover">
            <thead>
                <tr>
                    <td>SL.</td>
                    <td>Start Date</td>
                    <td>End Date</td>
                    <td>Status</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>
                @php
                    $date = date("Y-m-d")
                @endphp
                @foreach ($rosters as $key => $value)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{\Carbon\Carbon::parse($value->start_date)->format("Y-m-d")}}</td>
                            <td>{{\Carbon\Carbon::parse($value->end_date)->format("Y-m-d")}}</td>
                            @if($date>=$value->end_date)
                                <td > <i style="color: green; font-size: 25px;"  data-toggle="popover" data-placement="left" data-trigger="hover" data-content="Roster Done" class="fa fa-check-circle-o"></i></td>
                            @else
                                <td> <i style="color: orange; font-size: 25px;"  data-toggle="popover" data-placement="left" data-trigger="hover" data-content="Running Roster" class="fa fa-refresh"></i></td>
                            @endif
                            <td>
                                    <button type="button" class="btn btn-flat bg-olive" data-dismiss="modal" onclick="getPreviousRoster({{$value->id}})" name="button">Copy</button>

                            </td>

                        </tr>
                @endforeach
            </tbody>
        </table>
        <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
           </div>
        @endslot


    @endcomponent
    <script>
    $('#rostersTBL').DataTable({
        'paging'      : true,
        'lengthChange': false,
        'searching'   : true,
        'ordering'    : false,
        'info'        : true,
        'autoWidth'   : true
    })
    var startDate;
    var endDate;
    var employee =[];
    function dateGet() {
        var momentDate = document.getElementById('startDate').value;
        if(momentDate!=""){
            startDate= moment(momentDate);
            endDate= startDate.clone().add(1,'months');
            document.getElementById('endDate').value= endDate.format("YYYY-MM-DD");
            $('.overlay').remove();
            let nextDay ;
            for (var i = 0; i < 7; i++) {
                nextDay= startDate.clone().add(i,'days');
                document.getElementById('day'+i).innerHTML= nextDay.format("ddd(DD)");
                document.getElementById('day'+(i+7)).innerHTML= nextDay.format("ddd(DD)");

            }

        }
        else {
            alert("Select a Date Please!")
        }
    }
    function getEmployeeData() {
        var result=null;
        $.ajax({
                async: false,
                url: '/get-employee',
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                     result = response;

                }
            });
            return result;
    }
    function saveRoster() {
        employee = getEmployeeData();
        var saveData = [];
        if (employee.length>0) {
            employee.forEach(function(element){
                    var sun = document.getElementById('day0'+element.employee_id).value;
                    var mon = document.getElementById('day1'+element.employee_id).value;
                    var tue = document.getElementById('day2'+element.employee_id).value;
                    var wed = document.getElementById('day3'+element.employee_id).value;
                    var thu = document.getElementById('day4'+element.employee_id).value;
                    var fri = document.getElementById('day5'+element.employee_id).value;
                    var sat = document.getElementById('day6'+element.employee_id).value;

                    saveData.push([
                        {
                            employee_id : element.employee_id,
                            sun:sun
                        },
                        {
                            employee_id : element.employee_id,
                            mon:mon
                        },
                        {
                            employee_id : element.employee_id,
                            tue:tue
                        },
                        {
                            employee_id : element.employee_id,
                            wed:wed
                        },
                        {
                            employee_id : element.employee_id,
                            thu:thu
                        },
                        {
                            employee_id : element.employee_id,
                            fri:fri
                        },
                        {
                            employee_id : element.employee_id,
                            sat:sat
                        },
                      ]
                    )
            });
        }
        if(saveData.length>0){
            saveData.push({
                 startDate : startDate.format("YYYY-MM-DD"),
                 endDate   : endDate.format("YYYY-MM-DD")
            })

            $.ajax({
                    data: {data:saveData},
                    url: '/roster',
                    type: 'POST',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        if(response==1)
                        window.location="/add-roster";
                        else notifySnackBar("Problem in Saving Data");
                    }
                });
        }
        else alert("No Data to Save!");
    }

    $(function () {
           $('.datepicker').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
            });
           $('.select2').select2({
            });
            $('.select2').on('select2:select', function(e) {
                colorChangeSelectValue();
              });
           $('[data-toggle="popover"]').popover();
       });
       function getDataAlgorithm() {
           var startDate = document.getElementById('startDate').value;

           $.ajax({
                   data:{data:startDate},
                   url: '/algorithm-roster',
                   type: 'POST',
                   beforeSend: function (request) {
                       return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                   },
                   success: function (response) {
                        if(response==-1){
                            hotsnackbar('hserror', 'Rule cant be excuted as senior is less than 5');
                        }
                        else{
                        response[0]['IIG'].forEach(function(element){
                            //console.log(element);

                            $('#day0'+element.employee_id).val(element.duty[0].shift).trigger("change");
                            $('#day1'+element.employee_id).val(element.duty[1].shift).trigger("change");
                            $('#day2'+element.employee_id).val(element.duty[2].shift).trigger("change");
                            $('#day3'+element.employee_id).val(element.duty[3].shift).trigger("change");
                            $('#day4'+element.employee_id).val(element.duty[4].shift).trigger("change");
                            $('#day5'+element.employee_id).val(element.duty[5].shift).trigger("change");
                            $('#day6'+element.employee_id).val(element.duty[6].shift).trigger("change");

                        });
                        response[0]['IGW'].forEach(function(element){
                            //console.log(element);
                            $('#day0'+element.employee_id).val(element.duty[0].shift).trigger("change");
                            $('#day1'+element.employee_id).val(element.duty[1].shift).trigger("change");
                            $('#day2'+element.employee_id).val(element.duty[2].shift).trigger("change");
                            $('#day3'+element.employee_id).val(element.duty[3].shift).trigger("change");
                            $('#day4'+element.employee_id).val(element.duty[4].shift).trigger("change");
                            $('#day5'+element.employee_id).val(element.duty[5].shift).trigger("change");
                            $('#day6'+element.employee_id).val(element.duty[6].shift).trigger("change");

                        });
                        colorChangeSelectValue();
                    }
                   }
               });

       }
       function getPreviousRoster(id){

        $.ajax({
            url: '/previous-roster/'+id,
            type: 'GET',
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {

                 response[0]['IIG'].forEach(function(element){
                     //console.log(element);

                     $('#day0'+element.employee_id).val(element.duty[0].shift).trigger("change");
                     $('#day1'+element.employee_id).val(element.duty[1].shift).trigger("change");
                     $('#day2'+element.employee_id).val(element.duty[2].shift).trigger("change");
                     $('#day3'+element.employee_id).val(element.duty[3].shift).trigger("change");
                     $('#day4'+element.employee_id).val(element.duty[4].shift).trigger("change");
                     $('#day5'+element.employee_id).val(element.duty[5].shift).trigger("change");
                     $('#day6'+element.employee_id).val(element.duty[6].shift).trigger("change");

                 });
                 response[0]['IGW'].forEach(function(element){
                     //console.log(element);
                     $('#day0'+element.employee_id).val(element.duty[0].shift).trigger("change");
                     $('#day1'+element.employee_id).val(element.duty[1].shift).trigger("change");
                     $('#day2'+element.employee_id).val(element.duty[2].shift).trigger("change");
                     $('#day3'+element.employee_id).val(element.duty[3].shift).trigger("change");
                     $('#day4'+element.employee_id).val(element.duty[4].shift).trigger("change");
                     $('#day5'+element.employee_id).val(element.duty[5].shift).trigger("change");
                     $('#day6'+element.employee_id).val(element.duty[6].shift).trigger("change");

                 });
                 colorChangeSelectValue();

            }
        });
       }
       function colorChangeSelectValue() {
           $('.select2-selection__rendered').each(function(i, obj) {
               if(obj.title=="NIGHT")
               $(obj).css("color", "red");
               if(obj.title=="MOR")
               $(obj).css("color", "#ff7f00");
               if(obj.title=="EVE")
               $(obj).css("color", "#00a7be");
               if(obj.title=="OFF")
               $(obj).css("color", "green");

           });
       }
    </script>


@endsection
