{{--<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.css" rel="stylesheet"/>--}}
{{----}}
<div class="col-md-12">
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">إنشاء مجموعة</h3>
        </div>
        <div class="card-body">
            {{--            <i class="fa fa-warning" style="padding-bottom: 20px;"><span style="padding-right: 4px;">الاسم يجب انت يكون رباعي.</span></i>--}}
            <form action="{{route('group.members.store')}}" method="POST">
                @csrf
                {{--team leader data--}}
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>الرقم الجامعي لمنسق المجموعة</label>
                        <select class="students-std form-control select2 select2-hidden-accessible"
                                {{--                                    name="leaderStudentStd"--}}
                                name="leaderStudentStd"
                                style="width: 100%;text-align: right" tabindex="-1" aria-hidden="true">
                            <option></option>
                            @foreach($students as $student)
                                <option value="{{$student}}" id="option"
                                        @if(old('leaderStudentStd') == $student ) selected @endif>
                                    {{$student}}
                                </option>
                            @endforeach
                        </select>
                        @error('leaderStudentStd')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                {{--team member 1 data--}}
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="exampleInputEmail1">الرقم الجامعي للعضو الثاني</label>
                        <select class="students-std form-control select2 select2-hidden-accessible"
                                name="membersStd[]"
                                style="width: 100%;text-align: right" tabindex="-1" aria-hidden="true">
                            <option></option>
                            @foreach($students as $student)
                                <option @if(old('membersStd')[0] != null)
                                        @if(old('membersStd')[0] == $student) selected value="{{$student}}"
                                        id="option"@endif
                                    @endif>
                                    {{$student}}
                                </option>
                            @endforeach
                        </select>
                        @isset(old('membersStd')[0])
                            @if(old('membersStd')[0] == null)
                                <div class="alert alert-danger" style="margin-top: 10px">الرقم الجامعي مطلوب</div>
                            @endif
                        @endisset
                    </div>
                </div>
                {{--team member 2 data--}}
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="exampleInputEmail1">الرقم الجامعي للعضو الثالث</label>
                        <select class="students-std form-control select2 select2-hidden-accessible"
                                name="membersStd[]"
                                style="width: 100%;text-align: right" tabindex="-1" aria-hidden="true"
                                id="students_std">
                            <option></option>
                            @foreach($students as $student)
                                <option id="option"
                                        @if(old('membersStd')[0] != null)
                                        @if(old('membersStd')[1] == $student) selected value="{{$student}}" @endif
                                    @endif>
                                    {{$student}}
                                </option>
                            @endforeach
                        </select>
                        @isset(old('membersStd')[1])

                            @if(old('membersStd')[1] == null)
                                <div class="alert alert-danger" style="margin-top: 10px">الرقم الجامعي مطلوب</div>
                            @endif
                        @endisset
                    </div>
                </div>
                {{--team member 3 data--}}
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="exampleInputEmail1">الرقم الجامعي للعضو الرابع</label>
                        <select class="students-std form-control select2 select2-hidden-accessible"
                                name="membersStd[]"
                                style="width: 100%;text-align: right" tabindex="-1" aria-hidden="true"
                                id="students_std">
                            <option></option>
                            @foreach($students as $student)
                                <option id="option"
                                        @if(old('membersStd')[0] != null)
                                        @if(old('membersStd')[2] == $student ) selected value="{{$student}}" @endif
                                    @endif>
                                    {{$student}}
                                </option>
                            @endforeach
                        </select>
                        @isset(old('membersStd')[2])
                            @if(old('membersStd')[2] == null)
                                <div class="alert alert-danger" style="margin-top: 10px">الرقم الجامعي مطلوب</div>
                            @endif
                        @endisset
                    </div>
                </div>
                {{-- departments --}}
                <div class="row">
                    <div class="form-group col-md-12">
                        <label>التخصص </label>
                        <h6 style="font-size: small">اذا كان طلاب المجموعة منتسبين لأكثر من تخصص يتم اختيار اكثر من
                            قسم</h6>
                        <select class="form-control select2 select2-hidden-accessible" multiple=""
                                name="department[]"
                                style="width: 100%;text-align: right" tabindex="-1" aria-hidden="true">
                            <option></option>
                            @foreach($departments as $department)
                                <option value="{{$department}}"
                                        {{-- Becouse there is multiple select it should be like this
                                         to get old selectd options--}}
                                        @if(old('department')[0] != null)
                                        @foreach(old('department') as $oldDepartment)
                                        @if($oldDepartment == $department ) selected @endif
                                    @endforeach
                                    @endif
                                >{{$department}}
                                </option>
                            @endforeach
                        </select>
                        <h6></h6>
                        @error('department')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                {{-- Check if a group will be graduate in first semester. --}}
                <div class="form-group">
                    {{--1 for yes 0 for no--}}
                    <label for="">المجموعة خريجة الفصل الاول؟</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="1" name="graduateInFirstSemester"
                               @if(old('graduateInFirstSemester') == 1) checked @endif >
                        <label class="form-check-label">نعم</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="0" name="graduateInFirstSemester"
                               @if(old('graduateInFirstSemester') == 0) checked @endif>
                        <label class="form-check-label">لا</label>
                    </div>
                    @error('graduateInFirstSemester')
                    <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">تسجيل</button>
            </form>
        </div>
    </div>
</div>
@push('script')
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.js"></script>

    <script>
        $(function () {
            var $select2 = $(".students-std");

            // Copy the options to all selects based on the first one
            // $("select").html($select2.first().html());

            // Initialize Select2
            $select2.select2({
                allowClear: true,
                placeholder: "Select an option",
                tags: true
            });

            // Handle disabling already-selected options
            $select2.on("change", function () {
                $select2.find("option:disabled").prop("disabled", false).removeData("data");

                $select2.each(function () {
                    var val = $(this).val();
                    $select2.find("option:not(:selected)").filter(function () {
                        return this.value == val;
                    }).prop("disabled", true).removeData("data");
                });
            });
        })
    </script>

@endpush

