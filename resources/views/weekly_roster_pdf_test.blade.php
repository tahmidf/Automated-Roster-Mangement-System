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

    <h3>WEEKLY DUTY REGISTER </h3>
    <div id="pdf">

      <table>
          <tr>
              <th>EXP</th>
              <th>Employee Name</th>
              <th>Gender</th>
              <th>1</th>
              <th>2</th>
              <th>3</th>
              <th>4</th>
              <th>5</th>
              <th>6</th>
              <th>7</th>

          </tr>
          <body>
              @foreach ($rosterDuty[0]->IGW as $key => $value)
                  <tr>
                    <td>@if ($value->experience_number>=80)
                        SR.
                      @else
                        JR.
                      @endif
                  </td>
                                    <td>{{$value->name}}</td>
                      <td>{{$value->gender}}</td>
                      @foreach ($value->duty as $key1 => $valueDuty)
                        @if ($valueDuty->shift==1)
                              <td>MOR</td>
                        @elseif ($valueDuty->shift==2)
                            <td>EVN</td>
                        @elseif ($valueDuty->shift==3)
                            <td>NIG</td>
                        @else
                            <td>-1</td>
                        @endif
                      @endforeach
                  </tr>
              @endforeach

          </body>
      </table>
    </div>
<div id="pdf">

  <table>
      <tr>
          <th>EXP</th>
          <th>Employee Name</th>
          <th>Gender</th>
          <th>1</th>
          <th>2</th>
          <th>3</th>
          <th>4</th>
          <th>5</th>
          <th>6</th>
          <th>7</th>

      </tr>
      <body>
          @foreach ($rosterDuty[0]->IIG as $key => $value)
              <tr>
                  <td>@if ($value->experience_number>=80)
                        SR.
                      @else
                        JR.
                      @endif
                  </td>
                  <td>{{$value->name}}</td>
                  <td>{{$value->gender}}</td>
                  @foreach ($value->duty as $key1 => $valueDuty)
                    @if ($valueDuty->shift==1)
                          <td>MOR</td>
                    @elseif ($valueDuty->shift==2)
                        <td>EVN</td>
                    @elseif ($valueDuty->shift==3)
                        <td>NIG</td>
                    @else
                        <td>-1</td>
                    @endif
                  @endforeach
              </tr>
          @endforeach

      </body>
  </table>
</div>


</body>

</html>
