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

        <div class="col-md-11">
            @component('components.widget')
                @slot('title')
                    Settings
                @endslot
                @slot('description')
                @endslot
                @slot('body')
                    <div class="col-md-8">
                        <table  id="rostersTBL" class="table table-hover">
                            <thead>
                                <th>SL.</th>
                                <th>Rule</th>
                                <th>Description</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @foreach ($settings as $key=>$value)
                                    
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$value->name}}</td>
                                    <td>
                                            @php
                                            $doc = new DOMDocument();
                                            $doc->loadHTML($value->description);
                                            echo $doc->saveHTML();
                                            @endphp</td>
                                    <td>
                                        @if ($value->value==true)
                                        <label style="margin-bottom:0px;"class="switch"><a href="settings/{{$value->id}}/1">
                                                <input id="switchMenu" type="checkbox" checked><span class="slider round"></span></a>
                                                </label>
                                        @else
                                        <label style="margin-bottom:0px;"class="switch"><a href="settings/{{$value->id}}/2">
                                                <input id="switchMenu" type="checkbox"><span class="slider round"></span></a>
                                                </label>
                                        @endif
                                        
                                    </td>
                                </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
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
           });
           getEmployeeAll();
       });
    </script>


@endsection
