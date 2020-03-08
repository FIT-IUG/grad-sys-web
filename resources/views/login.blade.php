@includeIf('layouts.header')
<div class="login-box" style="width: 400px">
    <div class="login-logo">
<span class="login100-form-title p-b-40 form-row">
						أهلا بك في نظام متابعة مشاريع التخرج
					</span>
        <span class="login100-form-avatar">
						<img src="{{asset('assets/loginAssets/images/collageIcon.png')}}" alt="AVATAR">
					</span>
    </div>
    <div class="card">
        <div class="card-body login-card-body">

            <form action="{{route('login.check')}}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                           placeholder="ایمیل" value="{{old('email')}}" required>
                    <div class="input-group-append">
                        <span class="fa fa-envelope input-group-text"></span>
                    </div>
                    @error('email')
                    <div class="invalid-feedback">
                        <strong>{{ ($message) }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                           placeholder="رمز عبور" required>
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
                {{--                    <div class="col-7">--}}
                {{--                        <div class="checkbox icheck">--}}
                {{--                            <label class="">--}}
                {{--                                <div class="icheckbox_square-blue" aria-checked="false" aria-disabled="false" style="position: relative;"><input type="checkbox" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div> یاد آوری من--}}
                {{--                            </label>--}}
                {{--                        </div>--}}
                {{--                    </div>--}}
                <!-- /.col -->
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
