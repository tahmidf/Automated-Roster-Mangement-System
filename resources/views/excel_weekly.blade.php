<html>

<body>

 <div>
     <h3 style="background-color: #DF9721;">IGW Engineers</h3>
 </div>
<br>
<table >

  <thead>
  <tr>
      <th style="background-color:#169DCF;"> Name </th>
      @for ($i=0; $i <$numberofDay; $i++)
        <th class="data" style="background-color:#169DCF;">{{\Carbon\Carbon::parse($dutyShift->duty[$i]->rosterDate)->format("d/M")}}</th>
      @endfor
  </tr>
  </thead>
  <tbody>
      <tr>
          <td></td>
          @for ($i=0; $i <$numberofDay; $i++)
            <td class="data" style="background-color:#169DCF;">{{\Carbon\Carbon::parse($dutyShift->duty[$i]->rosterDate)->format("l")}}</td>
          @endfor
      </tr>
      @foreach ($rosterArrayMonth->IGW as $key => $value)
          <tr>
              <td>{{$value->name}}@if ($value->experience_number>=80)
                  (SR.)
              @endif</td>
              @foreach ($value->duty as $key1 => $valueDuty)
                  @if ($valueDuty->shift==1)
                      <td style="background-color:#DBCF17;">MOR</td>
                  @elseif ($valueDuty->shift==2)
                      <td style="background-color:#7F1E8C; color:#FFFFFF">EVN</td>
                  @elseif ($valueDuty->shift==3)
                      <td style="background-color:#000000; color:#FFFFFF">NIG</td>
                  @else
                      <td style="background-color:#159931;">OFF</td>
                  @endif
              @endforeach
          </tr>
      @endforeach

  </tbody>

</table>

<div>
    <h3 style="background-color:#DF9721;">IIG Engineers</h3>
</div>
<br>
<table >

 <thead>
 <tr>
     <th style="background-color:#169DCF;"> Name </th>
     @for ($i=0; $i <$numberofDay; $i++)
       <th class="data" style="background-color:#169DCF;">{{\Carbon\Carbon::parse($dutyShift->duty[$i]->rosterDate)->format("d/M")}}</th>
     @endfor
 </tr>
 </thead>
 <tbody>
     <tr>
         <td></td>
         @for ($i=0; $i <$numberofDay; $i++)
           <td class="data" style="background-color:#169DCF;">{{\Carbon\Carbon::parse($dutyShift->duty[$i]->rosterDate)->format("l")}}</td>
         @endfor
     </tr>
     @foreach ($rosterArrayMonth->IIG as $key => $value)

         <tr>
             <td>{{$value->name}}@if ($value->experience_number>=80)
                 (SR.)
             @endif</td>
             @foreach ($value->duty as $key1 => $valueDuty)
               @if ($valueDuty->shift==1)
                   <td style="background-color:#DBCF17;">MOR</td>
               @elseif ($valueDuty->shift==2)
                   <td style="background-color:#7F1E8C; color:#FFFFFF;">EVN</td>
               @elseif ($valueDuty->shift==3)
                   <td style="background-color:#000000; color:#FFFFFF">NIG</td>
               @else
                   <td style="background-color:#159931;">OFF</td>
               @endif
             @endforeach
         </tr>
     @endforeach

 </tbody>

</table>

</body>

</html>
