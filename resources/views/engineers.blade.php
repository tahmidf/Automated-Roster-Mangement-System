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

        <div class="col-md-9">
            @component('components.widget')
                @slot('title')
                    Engineers
                @endslot
                @slot('description')
                    You can add and Modify IGW and IIG Engineers here!
                    <br /><br />
                    <button type="button" class="btn btn-primary btn-flat"name="button"
                      data-toggle="modal" data-target="#add_new_engineers" >
                        <i class="fa fa-user-plus">Add new Engineers</i>
                    </button>
                @endslot
                @slot('body')
                    <table  id="rostersTBL" class="table table-hover">
                        <thead>
                            <tr>
                                <td>SL.</td>
                                <td>Name</td>
                                <td>Gender</td>
                                <td>Experience Number</td>
                                <td>Division</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                @endslot
            @endcomponent
         </div>
    </div>
    @component('components.modal')
        @slot('ID')
            add_new_engineers
        @endslot
        @slot('title')
            Add New Engineers
        @endslot
        @slot('body')
            <table  class="table table-hover">
                <form method="POST" action="/update-employee-info">
                    {{ csrf_field() }}
                    <tbody>
                    <tr>
                        <td></td>
                        <td>Name</td>
                        <td><input id="name" name="name" type="text"  required/></td>

                    </tr>
                    <tr>
                        <td></td>
                        <td>Gender</td>
                        <td>
                            <input type="radio" name="gender" id="genderM"  value="Male"/> Male
                            <input type="radio" name="gender" id="genderF"  value="Female"/> Female
                        </td>

                    </tr>
                    <tr>
                        <td></td>
                        <td>Experience Number</td>
                        <td><input type="number" id="exp" required/></td>

                    </tr>
                    <tr>
                        <td></td>
                        <td>Employee ID</td>
                        <td><input type="number"  id="employee_id" required/></td>

                    </tr>
                    <tr>
                        <td></td>
                        <td>Division</td>
                        <td>
                            <input type="radio" name="div" id="divIIG"  value="IIG"/> IIG
                            <input type="radio" name="div" id="divIGW"  value="IGW"/> IGW
                        </td>

                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button type="submit" class="btn btn-success btn-sm" onclick="saveEmployee()" data-dismiss="modal">SAVE PROFILE</button>
                        </td>
                        <td></td>
                    </tr>
                </form>
                </tbody>
            </table>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        @endslot
    @endcomponent

    @component('components.modal')
        @slot('ID')
            edit_new_engineers
        @endslot
        @slot('title')
            Edit Engineers
        @endslot
        @slot('body')
            <table  class="table table-hover">
                    <tbody>
                    <tr>
                        <td></td>
                        <td>Name</td>
                        <td><input id="nameEdit" name="name" type="text"  required/></td>

                    </tr>
                    <tr>
                        <td></td>
                        <td>Gender</td>
                        <td>
                            <input type="radio" name="gender" id="genderEditM"  value="Male"/> Male
                            <input type="radio" name="gender" id="genderEditF"  value="Female"/> Female
                        </td>

                    </tr>
                    <tr>
                        <td></td>
                        <td>Experience Number</td>
                        <td><input type="number" name="pass" id="expEdit" required/></td>

                    </tr>

                    <tr>
                        <td></td>
                        <td>Division</td>
                        <td>
                            <input type="radio" name="div" id="divEditIIG"  value="IIG"/> IIG
                            <input type="radio" name="div" id="divEditIGW"  value="IGW"/> IGW
                        </td>

                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button type="submit" class="btn btn-success btn-sm" onclick="updateEmployee()" data-dismiss="modal">UPDATE PROFILE</button>
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        @endslot
    @endcomponent
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
       var employee_id=-1;
       function getEmployee(Id) {
           employee_id = Id;
           $.ajax({
                   url: '/engineers/'+Id,
                   type: 'GET',
                   beforeSend: function (request) {
                       return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                   },
                   success: function (response) {
                       document.getElementById('nameEdit').value = response.name;
                       if (response.gender=="Male"){
                           document.getElementById('genderEditM').checked = true;
                           document.getElementById('genderEditF').checked = false;
                        }
                       else{
                           document.getElementById('genderEditF').checked = true;
                           document.getElementById('genderEditM').checked = false;

                        }
                       if (response.priority_gateway=="IIG"){
                           document.getElementById('divEditIIG').checked = true;
                           document.getElementById('divEditIGW').checked = false;

                        }
                       else{
                           document.getElementById('divEditIGW').checked = true;
                           document.getElementById('divEditIIG').checked = false;
                        }
                       document.getElementById('expEdit').value = response.experience_number;


                   }
               });
       }
       function getEmployeeAll() {
           $.ajax({
                   url: '/get-employee',
                   type: 'GET',
                   beforeSend: function (request) {
                       return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                   },
                   success: function (response) {

                       var t = $('#rostersTBL').DataTable();
                       t.clear().draw();
                       $.each(response, function (i, data) {
                           var editBtn =`<a href="#" class="btn btn-flat bg-olive btn-sm"
                               data-toggle="modal" data-target="#edit_new_engineers"
                               onclick="getEmployee(${data.id})">
                            <i class="fa fa-pencil"></i> </a>`;

                           t.row.add([
                               data.id,
                               data.name,
                               data.gender,
                               data.experience_number,
                               data.priority_gateway,
                               editBtn
                           ]).draw(true);
                       });

                   }
               });
       }
       function updateEmployee() {
           var gender;
           if(document.getElementById('genderEditM').checked)
           gender = document.getElementById('genderEditM').value;
           else
           gender = document.getElementById('genderEditF').value;

           var div;
           if(document.getElementById('divEditIIG').checked)
           div = document.getElementById('divEditIIG').value;
           else
           div = document.getElementById('divEditIGW').value;

           var saveInfo={
               name: document.getElementById('nameEdit').value,
               gender: gender,
               div: div,
               experience_number: document.getElementById('expEdit').value
           }
           $.ajax({
                   data: {data:saveInfo},
                   url: '/engineers/'+employee_id,
                   type: 'PUT',
                   beforeSend: function (request) {
                       return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                   },
                   success: function (response) {
                      if(response==-1)
                      notifySnackBar("Problem in Inputed Data!");
                      else
                      notifySnackBar("Data saved successfully!");
                      getEmployeeAll();

                   }
               });
       }
       function saveEmployee() {
           var gender="";
           if(document.getElementById('genderM').checked)
           gender = document.getElementById('genderM').value;
           else
           gender = document.getElementById('genderF').value;

           var div="";
           if(document.getElementById('divIIG').checked)
           div = document.getElementById('divIIG').value;
           else
           div = document.getElementById('divIGW').value;

           var saveInfo={
               name: document.getElementById('name').value,
               gender: gender,
               div: div,
               experience_number: document.getElementById('exp').value,
               employee_id: document.getElementById('employee_id').value

           }

           if (saveInfo.name==""||saveInfo.gender==""||saveInfo.div==""||saveInfo.experience_number==""||saveInfo.employee_id=="") {
               alert("Fill up all the Data");
               return ;
           }
           $.ajax({
                   data: {data:saveInfo},
                   url: '/engineers',
                   type: 'POST',
                   beforeSend: function (request) {
                       return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                   },
                   success: function (response) {
                       document.getElementById('name').value ="";
                       document.getElementById('exp').value=0;
                      if(response==-1)
                      notifySnackBar("Problem in Inputed Data!");
                      else
                      notifySnackBar("Data saved successfully!");
                      getEmployeeAll();

                   }
               });
       }
    </script>


@endsection
