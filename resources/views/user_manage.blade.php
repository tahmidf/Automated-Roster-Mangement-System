@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <script>
        function getInfo(r) {
            $.ajax({
                url: '/view-user/'+r,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    document.getElementById("name").value = response.name;
                    document.getElementById("ID").value = response.id;
                }
            });

        }
        function passwordGetter(object) {
            if(object.value==1)
            document.getElementById('passwordInput').innerHTML = `<input type="text" name="newPassword"
            value="" > <br>`;
            else
            document.getElementById('passwordInput').innerHTML ="";
        }

    </script>

    <hr class="alert-info">
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <?php
    echo Session::put('message','');
    ?>
    @if (session('info'))
        <div class="alert alert-danger">
            {{ session('info') }}
        </div>
    @endif
    <?php
    echo Session::put('info','');
    ?>
    <div class="row">
        @component('components.widget')
            @slot('title')
                User Login controller
            @endslot
            @slot('description')
                All users information
                @endslot
                @slot('body')
                    <button class="btn btn-danger" data-toggle="modal" data-target="#addagent"><i class="fa fa-user-plus"></i> Add New User</button>

                    <table  id="sTBL" class="table table-bordered table-hover">
                        <thead>
                        <tr>

                            <th><i class="fa fa-sort"></i> Username </th>
                            <th><i class="fa fa-sort"></i> Name</th>
                            <th><i class="fa fa-sort"></i> Category</th>
                            <th><i class=""></i> Action</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->username}}</td>
                                <td>{{ $user->name}}</td>
                                <td>{{ $user->category}}</td>
                                <td>

                                    <button class="btn btn-sm btn-primary" onclick="getInfo('{{$user->id}}');" data-toggle="modal" data-target="#editEmp"><i class="fa fa-pencil"></i> Edit</button>

                                </td>
                            </tr>
                        @endforeach

                        </tbody>

                    </table>
                @endslot
        @endcomponent

                </div>


    <div class="modal fade" id="editEmp">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Users</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="/update-user">
                        {{ csrf_field() }}

                    <table  class="table">


                            <tbody>

                            <tr>
                                <td></td>
                                <td>Name</td>
                                <td><input id="name" name="name" type="text"  required/></td>
                                <input id="ID" name="ID" type="hidden"  required/>
                            </tr>

                            <tr>
                                <td></td>
                                <td>Password</td>
                                    <td><input type="radio" name="password" value="-1" checked onclick="passwordGetter(this)"> Don't change password<br>
                                    <input type="radio" name="password" value="1" onclick="passwordGetter(this)"> Change password<br>
                                    <span id="passwordInput"></span><br>

                                </td>
                                </tr>

                            <tr>
                                <td></td>

                                <td>
                                    <button type="submit" class="btn btn-success btn-sm">SAVE</button>
                                </td>
                                <td></td>

                            </tr>



                        </tbody>



                    </table>
                </form>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    <div class="modal fade" id="addagent">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add new User</h4>
                </div>
                <div class="modal-body">
                    <strong>You can only add Sub-Admin/Manager from here.</strong> <br />
                    <table  class="table">
                        <form method="POST" action="/save-user">

                        {{ csrf_field() }}


                        <tbody>

                        <tr>
                            <td></td>
                            <td>Name</td>
                            <td><input type="text" name="name" required/></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Password</td>
                            <td><input id="password" name="password" type="password"  required/></td>
                        </tr>

                        <tr>
                            <td></td>

                            <td>
                                <button type="submit" class="btn btn-success btn-sm">SAVE</button>
                            </td>
                            <td></td>

                        </tr>

                        </form>



                        </tbody>



                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>



    <!-- DataTables -->
    <script src="{{ asset('/asset/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('/asset/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(function () {

            $('#sTBL').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : false,
                'info'        : true,
                'autoWidth'   : true
            });


        })
    </script>


@endsection
