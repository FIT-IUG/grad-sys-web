<!-- Left navbar links -->
<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        {{--        <a href="index3.html" class="nav-link">خانه</a>--}}
    </li>
    <li class="nav-item d-none d-sm-inline-block">
        {{--        <a href="#" class="nav-link">تماس</a>--}}
    </li>
</ul>

<!-- Right navbar links -->
<ul class="navbar-nav mr-auto">
    <!-- Notifications Dropdown Menu -->
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fa fa-bell-o"></i>
            @if(isset($notifications) and $notifications != null)
                <span
                    class="badge badge-warning navbar-badge">{{sizeof($notifications)}}</span>
            @endif
        </a>

        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-left">
            @if(isset($notifications) and $notifications != null)
                @foreach($notifications as $key=>$notification)
                    <button class="dropdown-item">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    {{isset($notification['from_name']) ? $notification['from_name'] : '-'}}
                                </h3>
                                <p class="text-sm text-muted">{{isset($notification['message']) ? $notification['message'] : '-'}}</p>
                            </div>
                        </div>
                    </button>
                @endforeach
            @else
                {{--                <a href="#" class="dropdown-item">--}}
                <div class="media">
                    <div class="media-body">
                        <h3 class="dropdown-item-title">
                            لا يوجد اي اشعار
                        </h3>
                    </div>
                </div>
                {{--                </a>--}}
            @endif
        </div>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fa fa-sign-out"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-sm-right dropdown-menu-left">
            <a href="{{route('logout')}}" class="dropdown-item">تسجيل الخروج</a>
        </div>
    </li>
</ul>
