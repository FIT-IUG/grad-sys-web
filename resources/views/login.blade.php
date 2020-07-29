@includeIf('layouts.header')
<div class="login-box" style="width: 400px">
    <div class="login-logo">
<span class="login100-form-title p-b-40 form-row">
						أهلًا بك في نظام متابعة مشاريع التخرج
					</span>
        <span class="login100-form-avatar">
						<img src="{{asset('assets/loginAssets/images/collageIcon.png')}}" alt="AVATAR">
					</span>
    </div>
    <div class="card">
        <div class="card-body login-card-body">

            <form action="{{route('login.check')}}" method="post">
                @csrf
                <div class="input-group mb-3">{{-- style="direction: ltr;"--}}
                    <input type="text" class="form-control @error('email') is-invalid @enderror" name="email"
                           placeholder="البريد الإلكتروني" value="{{old('email')}}" required>
                    <div class="input-group-append">
                        <span class="fa fa-envelope input-group-text"></span>
                    </div>
                    @error('email')
                    <div class="invalid-feedback">
                        <strong>{{$message}}</strong>
                    </div>
                    @enderror
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                           placeholder="كلمة المرور" required>
                    <div class="input-group-append">
                        <span class="fa fa-lock input-group-text"></span>
                    </div>
                    @error('password')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-lg-5">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">تسجيل الدخول</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@includeIf('layouts.footer-meta')
@includeIf('layouts.notify')
