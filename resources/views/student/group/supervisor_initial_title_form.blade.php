<div class="col-md-12">
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">إستكمال إجراءات انشاء مجموعة</h3>
        </div>
        <div class="card-body">
{{--            <i class="fa fa-warning" style="padding-bottom: 20px;"><span style="padding-right: 4px;">الاسم يجب انت يكون رباعي.</span></i>--}}
            <form action="{{route('group.teacher.store')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label style="padding-bottom: 23px;">مشرف المجموعة</label>
                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;"
                            tabindex="-1" aria-hidden="true" dir="rtl" name="supervisor">
                        <option value=""></option>
                        @foreach($teachers as $teacher)
                            <option value="{{$teacher}}"
                                    @if(old('teacher') == $teacher) selected @endif>{{$teacher}}
                            </option>
                        @endforeach
                    </select>
                    @error('teacher')
                    <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="form-group">
                        <label for="">العنوان المبدئي للمشروع</label>
                        <input type="text" class="form-control @error('initialProjectTitle') is-invalid @enderror"
                               id="exampleInputEmail1" name="initialProjectTitle"
                               value="{{old('initialProjectTitle')}}">
                        @error('initialProjectTitle')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">تسجيل</button>
            </form>
        </div>
    </div>
</div>
