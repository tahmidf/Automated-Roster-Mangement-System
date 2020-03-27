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
    <div class="row">
        <div class="col-md-8">
            @component('components.widget')
                @slot('title')
                    Previous Rosters
                @endslot
                @slot('description')
                    You can edit and Modify Your Rosters Here!
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
                                            <a href="{{URL::to('/roster')}}{{'/'.$value->id}}" class="btn btn-flat bg-olive btn-sm"><i class="fa fa-pencil"></i></a>
                                            <a href="{{URL::to('/pdf-roster')}}{{'/'.$value->id}}"
                                                data-toggle="popover" title="Hints" data-trigger="hover" data-content="Weekly PDF download"
                                                class="btn btn-flat bg-maroon btn-sm"><i class="fa fa-print"></i>
                                            </a>
                                            <a href="{{URL::to('/month-xl-roster')}}{{'/'.$value->id}}"
                                                data-toggle="popover" title="Hints" data-trigger="hover" data-content="Monthly XLSX download"
                                                class="btn btn-flat bg-blue btn-sm">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>

                                    </tr>
                            @endforeach
                        </tbody>
                    </table>

                @endslot
            @endcomponent
         </div>
    </div>

    <script>
    $(function () {
           $('.select2').select2();

           $('[data-toggle="popover"]').popover();


           $('#rostersTBL').DataTable({
               'paging'      : true,
               'lengthChange': false,
               'searching'   : true,
               'ordering'    : false,
               'info'        : true,
               'autoWidth'   : true
           })

       });

    </script>


@endsection
