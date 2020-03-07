<!-- Brand Logo -->
<a href="index3.html" class="brand-link">
{{--    <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"--}}
{{--         style="opacity: .8">--}}
    <span class="brand-text font-weight-light">كلية تكنولوجيا المعلومات</span>
</a>

<!-- Sidebar -->
<div class="sidebar" style="direction: ltr">
    <div style="direction: rtl">
        <!-- Sidebar user panel (optional) -->
{{--        <div class="user-panel mt-3 pb-3 mb-3 d-flex">--}}
{{--            <div class="image">--}}
{{--                <img src="https://secure.gravatar.com/avatar/5ffa2a1ffeb767c60ab7e1052e385d5c?s=52&d=mm&r=g" class="img-circle elevation-2" alt="User Image">--}}
{{--            </div>--}}
{{--            <div class="info">--}}
{{--                <a href="#" class="d-block">كلية تكنولوجيا المعلومات</a>--}}
{{--            </div>--}}
{{--        </div>--}}

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item menu-open">
                    <a href="{{route('dashboard')}}" class="nav-link active">
                        <i class="nav-icon fa fa-dashboard"></i>
                        <p>
                            الصفحة الرئيسية
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
</div>
<!-- /.sidebar -->
