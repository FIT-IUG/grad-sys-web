{{-- user manually registration --}}
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">تسجيل مستخدم يدويًا</h3>
    </div>
    <div class="card-body">
        <form action="{{route('user.store')}}" method="POST">
            @csrf
            <div class="row">
                <div class="form-group col-md-4">
                    <label>الاسم</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           name="name" value="{{old('name')}}">
                    @error('name')
                    <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label>البريد الإلكتروني</label>
                    <input type="email" class="form-control @error('email') is-invalid  @enderror"
                           name="email" value="{{old('email')}}">
                    @error('email')
                    <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label>الرقم الجامعي</label>
                    <input type="text" class="form-control @error('user_id') is-invalid  @enderror"
                           name="user_id" value="{{old('user_id')}}">
                    @error('user_id')
                    <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label>الدور</label>
                    <select class="form-control @error('role') is-invalid  @enderror" name="role">
                        <option value="student" selected>طالب</option>
                        <option value="teacher">مدرس</option>
                        <option value="admin">آدمن</option>
                    </select>
                    @error('role')
                    <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label>رقم الموبايل</label>
                    <input type="text" class="form-control @error('mobile_number') is-invalid  @enderror"
                           name="mobile_number" value="{{old('mobile_number')}}">
                    @error('mobile_number')
                    <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label>التخصص</label>
                    <select class="form-control @error('department') is-invalid  @enderror" name="department">
                        <option value=""></option>
                        @foreach($departments as $department)
                            <option value="{{$department}}"
                                    @if(old('department') == $department) selected @endif>{{$department}}</option>
                        @endforeach
                    </select>
                    @error('department')
                    <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary">تسجيل</button>
        </form>
    </div>
</div>
