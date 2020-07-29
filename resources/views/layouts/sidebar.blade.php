<!-- Brand Logo -->
<a href="{{route(getRole().'.index')}}" class="brand-link">
    {{--    <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"--}}
    {{--         style="opacity: .8">--}}
    <span class="brand-text font-weight-light">كلية تكنولوجيا المعلومات</span>
</a>

<!-- Sidebar -->
<div class="sidebar" style="direction: ltr">
    <div style="direction: rtl">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item menu-open">
                    <a href="{{route(getRole().'.index')}}" class="nav-link active">
                        <i class="nav-icon fa fa-home"></i>
                        <p>
                            الصفحة الرئيسية
                        </p>
                    </a>
                </li>

                @if(hasRole('admin'))
                    <li class="nav-item menu-open">
                        <a href="{{route('admin.settings')}}" class="nav-link active">
                            <i class="nav-icon fa fa-cogs"></i>
                            <p>
                                إعدادات النظام
                            </p>
                        </a>
                    </li>
                    <li class="nav-item menu-open">
                        <a href="{{route('admin.student.index')}}" class="nav-link active">
                            <i class="nav-icon fa fa-user"></i>
                            <p>
                                بيانات الطلبة
                            </p>
                        </a>
                    </li>
                    <li class="nav-item menu-open">
                        <a href="{{route('admin.teacher.index')}}" class="nav-link active">
                            <i class="nav-icon fa fa-user-circle"></i>
                            <p>
                                بيانات المشرفين
                            </p>
                        </a>
                    </li>
                    <li class="nav-item menu-open">
                        <a href="{{route('admin.group.index')}}" class="nav-link active">
                            <i class="nav-icon fa fa-users"></i>
                            <p>
                                بيانات المجموعات
                            </p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
</div>
<!-- /.sidebar -->
