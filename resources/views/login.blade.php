<!DOCTYPE html>
<html lang="ar">
<head>
    <title>Login V6</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{asset('assets/loginAssets/images/icons/favicon.ico')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/loginAssets/css/util.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/loginAssets/css/main.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/loginAssets/css/more.css')}}">
</head>
<body>

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100 p-t-85 p-b-20">
            <form class="login100-form validate-form" action="{{route('login.check')}}" method="POST">
                @csrf
                <span class="login100-form-title p-b-70 form-row">
						أهلا بك في نظام متابعة مشاريع التخرج
					</span>

                <span class="login100-form-avatar">
						<img src="{{asset('assets/loginAssets/images/collageIcon.png')}}" alt="AVATAR">
					</span>

                <div class="wrap-input100 validate-input m-t-85 m-b-35" data-validate="أدخل الإيميل">
                    <input class="input100" type="text" name="email" autocomplete="on" value="{{old('email')}}"
                           id="email">
                    <span class="focus-input100" data-placeholder="الإيميل"></span>
                    @error('email')
                    <div class="invalid-feedback">
                        <strong>{{ ($message) }}</strong>
                    </div>
                    @enderror
                </div>

                <div class="input-group wrap-input100 validate-input m-b-50" data-validate="أدخل كلمة المرور">
                    <input class="input100  @error('password') is-invalid @enderror" type="password"
                           name="password">
                    <span class="focus-input100  @error('password') is-invalid @enderror"
                          data-placeholder="كلمة السر">
                         @error('password')
                    <div class="invalid-feedback">
                        <strong>{{ ($message) }}</strong>
                    </div>
                    </span>

                    @enderror
                </div>
                <div class="container-login100-form-btn">
                    <button class="login100-form-btn">
                        تسجيل الدخول
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="dropDownSelect1"></div>
<!-- jQuery -->
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<script src="{{asset('assets/loginAssets/js/main.js')}}"></script>
<script src="{{asset('assets/dist/js/notify.min.js')}}"></script>

{{--@if(session()->has('error'))--}}
<script>
    $.notify(sessionStorage.getItem('error'), "success");
</script>
{{--@endif--}}
</body>
</html>
