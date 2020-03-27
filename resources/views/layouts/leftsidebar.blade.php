<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{  URL::to('employee_images/photo_default_emplyee1.png') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{Auth::user()->name}}</p>
                <small>{{Auth::user()->category}}</</small>

            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree" style="">
            <li>
                @if (Auth::user()->is_admin)
                    <a href="{{URL::to('/home')}}">
                @else
                    <a href="{{URL::to('/home')}}">
                @endif
                    <i class="fa fa-ravelry"></i> <span>Dashboard</span>

                    </a>
            </li>

            @if(Auth::user()->is_admin)
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-eercast"></i>
                    <span>RMS</span>
                    <span class="pull-right-container">
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{URL::to('/add-roster')}}"><i class="fa fa-plus"></i>Add New Roster</a></li>
                    <li>
                        <a href="{{URL::to('/roster')}}">
                        <i class="fa fa-adjust"></i>
                            Previous Rosters
                        </a>
                    </li>
                </ul>
                <li>
                        <a href="{{URL::to('/engineers')}}">
                            <i class="fa fa-user"></i> <span>Engineers</span>
                        </a>
                </li>
                <li>
                        <a href="{{URL::to('/settings')}}">
                            <i class="fa fa-cog"></i> <span>Settings</span>
                        </a>
                </li>
                <li>
                    <a href="{{URL::to('/user-manage')}}">
                        <i class="fa fa-user"></i>
                        <span>User Manager</span>
                        <span class="pull-right-container">
                        </span>
                    </a>

                </li>
            </li>
            @endif


        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
