<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bangla Trac. Communications</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        @include('layouts.importassets')
    <style>
        @include('layouts.extrastyle')
                html, body {

                    font-size: 17px;
                    height: 200vh;
                    margin: 0;
                }
    </style>
</head>
<body class="hold-transition skin-black sidebar-mini">

<div class="wrapper">
    <!-- Left side column. contains the logo and sidebar -->
    @include('layouts.leftsidebar')
    @include('layouts.header')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <section class="content-header">

            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">{{Request::path()}}</li>

            </ol>
        </section>

        <!-- Main content -->
        {{-- loadder --}}
        <section class="content" >
            <div class="spinner">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
            </div>
            <div id="snackbar"></div>
            <br>

            @yield('content')

        </section>
    </div>
        @include('layouts.footer')

    <div class="control-sidebar-bg"></div>
</div>



<script>
    $(function () {
        dinamicMenu();

        $(".spinner").css("display", "none");

    });

    $.widget.bridge('uibutton', $.ui.button);
    $(document).ready(function(){
        $(document).ajaxStart(function(){
            $(".spinner").css("display", "block");
        });
        $(document).ajaxComplete(function(){
            $(".spinner").css("display", "none");
        });

    });
    function notifySnackBar(message) {
        document.getElementById("snackbar").innerHTML = message;
        var x = document.getElementById("snackbar");
        x.className = "show";
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }
    function dinamicMenu() {
        var url = window.location;

        $('ul.sidebar-menu a').filter(function() {
            return this.href == url;
        }).parent().addClass('active');

        // Will only work if string in href matches with location
        $('.treeview-menu li a[href="' + url + '"]').parent().addClass('active');
        // Will also work for relative and absolute hrefs
        $('.treeview-menu li a').filter(function() {
            return this.href == url;
        }).parent().parent().parent().addClass('active');
    };
</script>
</body>
</html>
