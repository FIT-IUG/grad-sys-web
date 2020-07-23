@if($teacher_groups != null)
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                @if(isset($teacher))
                    بيانات المجموعات الخاصة بالمدرس {{$teacher['name']}}
                @else
                    بيانات المجموعات الخاصة بك
                @endif
            </h3>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                @for($i = 1; $i <= sizeof($teacher_groups) ;$i++)
                    <li class="nav-item">
                        <a class="nav-link @if($i == 1) active @endif" id="custom-content-below-home-tab"
                           data-toggle="pill"
                           href="#group{{$i}}" role="tab" aria-controls="custom-content-below-home"
                           aria-selected="false">المجموعة {{ $i }}</a>
                    </li>
                @endfor
            </ul>
            <div class="tab-content" id="custom-content-below-tabContent">
                @foreach($teacher_groups as $key => $group)
                    <div class="tab-pane fade @if($loop->first) active show @endif" id="group{{$key+1}}"
                         role="tabpanel"
                         aria-labelledby="custom-content-below-home-tab">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>الرقم الجامعي</th>
                                <th>اسم الطالب</th>
                                <th>رقم الجوال</th>
                                <th>القسم</th>
                                <th>الايميل</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($group['students_data'] as $member)
                                <tr>
                                    <td>{{isset($member['user_id']) ? $member['user_id'] : '-'}}</td>
                                    <td>{{isset($member['name']) ? $member['name'] : '-'}}
                                        @if($member['isLeader'])(قائد الفريق) @endif
                                    </td>
                                    <td>{{isset($member['mobile_number']) ? $member['mobile_number'] : '-'}}</td>
                                    <td>{{isset($member['department']) ? $member['department'] : '-'}}</td>
                                    <td>{{isset($member['email']) ? $member['email'] : '-'}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <div class="col-6">
                            <p class="lead">بيانات المشروع</p>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <th>العنوان المبدئي:</th>
                                        <td>
                                            {{isset($group['initialProjectTitle']) ? $group['initialProjectTitle'] : '-'}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>هل المجموعة خريجة فصل أول:</th>
                                        <td>
                                            {{(isset($group['graduateInFirstSemester']) ? $group['graduateInFirstSemester'] : '-') == 0 ? 'لا' : 'نعم'}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>شكل المشروع:</th>
                                        <td>@if(isset($group['tags']))
                                                @foreach($group['tags'] as $tag)
                                                    @if($loop->last) {{$tag}}.
                                                    @else {{$tag}},
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    @if(isset($teacher))
        <h1>لا يوجد مجموعات تخص المشرف: {{$teacher['name']}}.</h1>
    @elseif(getRole() == 'teacher')
        <h1>لا يوجد لديك أي مجموعات.</h1>
    @endif
@endif
