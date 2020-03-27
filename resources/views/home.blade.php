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


    @component('components.box')
                @slot('title')
                <i
                class="fa fa-ravelry"></i>  Dashboard
                @endslot
                @slot('tool')
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                @endslot

                @slot('body')

                    <h2 class="text text-info"><b>Greetings, enjoy and have a beautiful day.</b></h2>
                    <br />

                           <div class="col-md-3">
                                <div class="text text-info">
                                    <h4>Quick Menu</h4>
                                    <p class="center-block download-buttons">
                                        <a href="{{URL::to('/add-roster')}}"class="btn btn-flat bg-olive">
                                            <i class="fa fa-plus-circle"></i> Add Roster</a>
                                            <a href="{{URL::to('/roster')}}"class="btn btn-flat bg-maroon">
                                                <i class="fa fa-backward"></i> Previous Roster</a>
                                    </p>
                                </div>

                            </div>






                @endslot
                @slot('footer')

                @endslot
    @endcomponent


@endsection
