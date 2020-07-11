<div class="col-md-12">
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">إنشاء فريق مشروع التخرج</h3>
        </div>
        <div class="card-body">
            @if(Str::substr(getUserId(), 0, 1) == 1)
                <span style="padding-right: 4px;">الطالب المسجل يعتبر مشرف الفريق في حال أتم التسجيل.</span>
            @else
                <span style="padding-right: 4px;">الطالبة المسجلة تعتبر مشرفة الفريق في حال أتمت التسجيل.</span>
            @endif
            @error('membersStd')
            <div class="alert alert-danger" style="margin-top: 10px">{{$message}}</div>
            @enderror
            <form action="{{route('student.group.store')}}" method="POST">
                @csrf
                @for($i = 0; $i < $max_members_number; $i++)
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">الرقم الجامعي للعضو {{$i+2 }}</label>
                            <select class="students-std form-control select2 select2-hidden-accessible"
                                    name="membersStd[]"
                                    style="width: 100%;text-align: right" tabindex="-1" aria-hidden="true">
                                <option value=""></option>
                                @foreach($students as $student)
                                    <option @if(old('membersStd.'.$i) != null)
                                            @if(old('membersStd.'.$i) == $student) selected value="{{$student}}"
                                            id="option"@endif
                                        @endif>
                                        {{$student}}
                                    </option>
                                @endforeach
                            </select>
                            {{--                            @error('membersStd')--}}
                            {{--                            @if(old('membersStd')[$i] == null)--}}
                            {{--                                <div class="alert alert-danger" style="margin-top: 10px">الرقم الجامعي مطلوب</div>--}}
                            {{--                            @endif--}}
                            {{--                            @enderror--}}
                        </div>
                    </div>
                @endfor
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
            $select2.on("select2:open", function () {
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

