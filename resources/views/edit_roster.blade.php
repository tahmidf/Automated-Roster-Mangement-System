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
                <h5>Roster Start Date</h5>
            <div class="input-group date" style="z-index: 999">
                  <input type="text" id="startDate" class="form-control pull-right" value="{{\Carbon\Carbon::parse($roster->start_date)->format("Y-m-d")}}" disabled>
            </div>
            <br/>
        </div>
        <div class="col-sm-3">
                <h5>Roster End Date</h5>
              <input type="text" id="endDate" class="form-control pull-right" value="{{\Carbon\Carbon::parse($roster->end_date)->format("Y-m-d")}}" disabled>
         </div>
    </div>
    <br/>
      <div class="box box-danger" id="boxRoster">
           <div class="box-header">
               <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
             <h3 class="box-title">Roster Update</h3>
             <br />
             <span>Roster ID:  <input type="text" id="roster_id" value="{{$roster->id}}" disabled> </span>

           </div>
            <div class="box-body">
                <ul class="timeline">
                <li class="time-label">

                </li>
                <li>
                    {{-- employeeIGW --}}
                    <i class="fa fa-user bg-blue"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header"><a href="#">IGW </a> Engineers</h3>

                        <div class="timeline-body">

                                <br /><br />
                                <table  id="rostersTBL" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <td>SL.</td>
                                            <td>Engineer</td>
                                            @foreach ($dutyShift->duty as $key => $valuedutyShift)
                                                <td>{{$valuedutyShift->day}}
                                                    ({{\Carbon\Carbon::parse($valuedutyShift->rosterDate)->format("d/m")}})
                                                </td>
                                            @endforeach

                                        </tr>
                                    </thead>
                                    <tbody>
                                            @foreach ($rosterArray->IGW as $key => $value)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$value->name}}@if ($value->experience_number>=80)
                                                    (SR.)
                                                @endif</td>
                                                @foreach ($value->duty as $valueDuty)
                                                <td>
                                                    <select class="form-control select2" style="width: 100%;" id='{{$valueDuty->day}}{{$value->employee_id}}'>
                                                        <option selected="selected" value="{{$valueDuty->shift}}">
                                                            @if ($valueDuty->shift==1)MOR @elseif ($valueDuty->shift==2)EVE @elseif ($valueDuty->shift==3)NIGHT @else OFF @endif
                                                        </option>
                                                        <option value="1">MOR</option>
                                                        <option value="2">EVE</option>
                                                        <option value="3">NIGHT</option>
                                                        <option value="-1">OFF</option>
                                                    </select>
                                                </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                    </tbody>
                                </table>


                        </div>
                    </div>

                </li>
                <li>
                    {{-- employeeIIG --}}
                    <i class="fa fa-user bg-red"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header"><a href="#">IIG </a> Engineers</h3>
                        <div class="timeline-body">
                                <br />
                                <table  id="rostersTBL" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <td>SL.</td>
                                            <td>Engineer</td>
                                            @foreach ($dutyShift->duty as $key => $valuedutyShift)
                                                <td>{{$valuedutyShift->day}}
                                                    ({{\Carbon\Carbon::parse($valuedutyShift->rosterDate)->format("d/m")}})
                                                </td>
                                            @endforeach

                                        </tr>
                                    </thead>
                                    <tbody>
                                            @foreach ($rosterArray->IIG as $key => $value)
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>{{$value->name}}</td>
                                                    @foreach ($value->duty as $valueDuty)
                                                    <td>
                                                        <select class="form-control select2" style="width: 100%;" id='{{$valueDuty->day}}{{$value->employee_id}}'>
                                                          <option selected="selected" value="{{$valueDuty->shift}}">
                                                              @if ($valueDuty->shift==1)MOR @elseif ($valueDuty->shift==2)EVE @elseif ($valueDuty->shift==3)NIGHT @else OFF @endif
                                                          </option>
                                                          <option value="1">MOR</option>
                                                          <option value="2">EVE</option>
                                                          <option value="3">NIGHT</option>
                                                          <option value="-1">OFF</option>
                                                        </select>
                                                    </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                    </tbody>
                                </table>


                        </div>
                    </div>
                </li>

                </ul>
                <button type="button" class="btn btn-flat bg-olive" onclick="saveRoster()"> Update Roster</button>

            </div>

     </div>
    <script>
    var startDate;
    var endDate;
    var employee =[];
    $(document).ready(function(){
     $('.datepicker').datepicker({
          autoclose: true,
          format: 'yyyy-mm-dd',
      });
     $('.select2').select2();

     $('[data-toggle="popover"]').popover();
      colorChangeSelectValue();
      $('.select2').on('select2:select', function(e) {
        colorChangeSelectValue();
      });

    });
    function getEmployeeData() {
        var result=null;
        var roster_id = document.getElementById('roster_id').value;

        $.ajax({
                async: false,
                url: '/get-employee-roster/'+roster_id,
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

                    var sun = document.getElementById('Sunday'+element.employee_id).value;
                    var mon = document.getElementById('Monday'+element.employee_id).value;
                    var tue = document.getElementById('Tuesday'+element.employee_id).value;
                    var wed = document.getElementById('Wednesday'+element.employee_id).value;
                    var thu = document.getElementById('Thursday'+element.employee_id).value;
                    var fri = document.getElementById('Friday'+element.employee_id).value;
                    var sat = document.getElementById('Saturday'+element.employee_id).value;

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
        var roster_id = document.getElementById('roster_id').value;
        if(saveData.length>0){
            $.ajax({
                    data: {data:saveData},
                    url: '/roster/'+roster_id,
                    type: 'PUT',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        if(response==1)
                        window.location="/roster/"+roster_id;
                        else notifySnackBar("Problem in Saving Data");
                    }
                });
        }
        else alert("No Data to Save!");
    }

       function colorChangeSelectValue() {
           $('.select2-selection__rendered').each(function(i, obj) {
               let title = obj.title.replace(/\s/g, "");
               if(title=="NIGHT")
               $(obj).css("color", "red");
               if(title=="MOR")
               $(obj).css("color", "#ff7f00");
               if(title=="EVE")
               $(obj).css("color", "#00a7be");
               if(title=="OFF")
               $(obj).css("color", "green");
           });
       }
    </script>


@endsection
