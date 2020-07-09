<!DOCTYPE html>
<html lang="ar">
<head>
    <title>@yield('title')</title>
    @includeIf('layouts.header')
</head>
<body class="hold-transition sidebar-mini" style="font-family: 'Amiri', serif">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
        @includeIf('layouts.navbar')
    </nav>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        @includeIf('layouts.sidebar')
    </aside>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                @yield('small_box')
                @yield('content')
            </div>
        </section>
    </div>
    @includeIf('layouts.footer')

    <aside class="control-sidebar control-sidebar-dark">
    </aside>
</div>
@includeIf('layouts.footer-meta')
@includeIf('layouts.notify')
@stack('script')
</body>
</html>
