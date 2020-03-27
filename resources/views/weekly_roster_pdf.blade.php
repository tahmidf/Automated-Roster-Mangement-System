<!DOCTYPE html>
<html>
<style media="screen">
@include('style.pdf_style')
body {
position: relative;
width: 21cm;
height: 29.7cm;
margin: 0 auto;
color: #001028;
background: #FFFFFF;
font-family: Arial, sans-serif;
font-size: 12px;
font-family: Arial;
}

</style>

<body>
    <header>
        <div id= "details">
{{--
        <table>
            <tr>
                <td>Name:</td>
                <td>{{$rosterInfo[0]->employee->name}}</td>
            </tr>
            <tr>
                <td>Employee ID#</td>
                <td>{{$rosterInfo[0]->employee->employee_id}}</td>
            </tr>
            <tr>
                <td>Zone</td>
                <td>{{$rosterInfo[0]->employee->zone}}</td>
            </tr>
        </table> --}}
        </div>

        </table>

    </header>

    <h3>WEEKLY DUTY REGISTER ({{\Carbon\Carbon::parse($roster->start_date)->format("Y-m-d")}} - {{\Carbon\Carbon::parse($roster->end_date)->format("Y-m-d")}})</h3>
<div id="pdf">
    <h4>IIG Engineers</h4>
  <table>
      <tr>
          <td>SL.</td>
          <td>Engineer</td>
          @foreach ($dutyShift->duty as $key => $valuedutyShift)
              <td>{{$valuedutyShift->day}}
                  ({{\Carbon\Carbon::parse($valuedutyShift->rosterDate)->format("d/m")}})
              </td>
          @endforeach

      </tr>
      <body>
          @foreach ($rosterArray->IIG as $key => $value)
              <tr>
                  <td>{{$key+1}}</td>
                  <td>{{$value->name}}@if ($value->experience_number>=80)
                      (SR.)
                  @endif</td>
                  @foreach ($value->duty as $key1 => $valueDuty)
                    @if ($valueDuty->shift==1)
                          <td>MOR</td>
                    @elseif ($valueDuty->shift==2)
                        <td>EVN</td>
                    @elseif ($valueDuty->shift==3)
                        <td>NIGHT</td>
                    @else
                        <td>OFF</td>
                    @endif
                  @endforeach
              </tr>
          @endforeach

      </body>
  </table>
  <h4>IGW Engineers</h4>
<table>
    <tr>
        <td>SL.</td>
        <td>Engineer</td>
        @foreach ($dutyShift->duty as $key => $valuedutyShift)
            <td>{{$valuedutyShift->day}}
                ({{\Carbon\Carbon::parse($valuedutyShift->rosterDate)->format("d/m")}})
            </td>
        @endforeach

    </tr>
    <body>
        @foreach ($rosterArray->IGW as $key => $value)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$value->name}}@if ($value->experience_number>=80)
                    (SR.)
                @endif</td>
                @foreach ($value->duty as $key1 => $valueDuty)
                  @if ($valueDuty->shift==1)
                        <td>MOR</td>
                  @elseif ($valueDuty->shift==2)
                      <td>EVN</td>
                  @elseif ($valueDuty->shift==3)
                      <td>NIGHT</td>
                  @else
                      <td>OFF</td>
                  @endif
                @endforeach
            </tr>
        @endforeach

    </body>
</table>
</div>

</body>

</html>
