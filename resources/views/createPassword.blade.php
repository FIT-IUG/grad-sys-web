<head>
    <title>إنشاء كلمة مرور</title>
    @includeIf('layouts.header')
</head>
<body>
<div class="login-box" style="width: 400px">
    <div class="login-logo"><span
            class="login100-form-title p-b-40 form-row">أهلا بك في نظام متابعة مشاريع التخرج</span>
    </div>
    <div class="card">
        <div class="card-body login-card-body">

            <form action="{{route('store.user.password',['token'=>request()->segment(4)])}}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                           placeholder="كلمة السر" required>
                    <div class="input-group-append">
                        <span class="fa fa-lock input-group-text"></span>
                    </div>
                    @error('password')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                           name="password_confirmation"
                           placeholder="تأكيد كلمة السر" required>
                    <div class="input-group-append">
                        <span class="fa fa-lock input-group-text"></span>
                    </div>
                    @error('password_confirmation')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-lg-5">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">حفظ كلمة السر</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@includeIf('layouts.footer-meta')
@includeIf('layouts.notify')
</body>
