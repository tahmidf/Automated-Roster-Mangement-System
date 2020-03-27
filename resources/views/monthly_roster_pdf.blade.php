<!DOCTYPE html>
<html>
<style media="screen">
@include('style.pdf_style_monthly_client')
body {
    color: #001028;
    background: #FFFFFF;
    font-size: 11px;
}

</style>

<body>
    <hr />
    <header>
        <div class="center">
            <div class="detailsShift">
                <h4>
                    <img class="" src="logo.jpg">
                </h4>
            </div>
        </div>
    </div>

    </header>

    <h3>MONTHLY DUTY REGISTER ({{\Carbon\Carbon::parse($roster->start_date)->format("Y-m-d")}} - {{\Carbon\Carbon::parse($roster->end_date)->format("Y-m-d")}})</h3>
<div id="pdf">

  <table>
      <tr>
          <th class="thDuty">Name</th>
          @for ($i=0; $i <$numberofDay; $i++)
            <th class="data">{{\Carbon\Carbon::parse($dutyShift->duty[$i]->rosterDate)->format("d")}}</th>
          @endfor

      </tr>
      <body>
                @foreach ($rosterArrayMonth->IIG as $key => $value)
                    <tr>
                        <td>{{$value->name}}@if ($value->experience_number>=80)
                            (SR.)
                        @endif</td>
                        @foreach ($value->duty as $key1 => $valueDuty)
                          @if ($valueDuty->shift==1)
                                <td>MOR</td>
                          @elseif ($valueDuty->shift==2)
                              <td>EVN</td>
                          @elseif ($valueDuty->shift==3)
                              <td>NIG</td>
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
