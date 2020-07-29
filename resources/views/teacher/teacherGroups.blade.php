@if($teacher_groups != null)
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                @if(isset($teacher))
                    بيانات المجموعات الخاصة بالمشرف {{isset($teacher['name']) ? $teacher['name'] : '-'}}
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
                    <div class="tab-pane fade @if($loop->first) active show @endif" id="group{{$loop->iteration}}"
                         role="tabpanel"
                         aria-labelledby="custom-content-below-home-tab">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>الرقم الجامعي</th>
                                <th>اسم الطالب</th>
                                <th>رقم الموبايل</th>
                                <th>القسم</th>
                                <th>البريد الإلكتروني</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{isset($group['group_leader_data']['user_id']) ? $group['group_leader_data']['user_id'] : '-'}}</td>
                                <td>{{isset($group['group_leader_data']['name']) ? $group['group_leader_data']['name'] : '-'}}</td>
                                <td>{{isset($group['group_leader_data']['mobile_number']) ? $group['group_leader_data']['mobile_number'] : '-'}}</td>
                                <td>{{isset($group['group_leader_data']['department']) ? $group['group_leader_data']['department'] : '-'}}</td>
                                <td>{{isset($group['group_leader_data']['email']) ? $group['group_leader_data']['email'] : '-'}}</td>
                                <td><span class="fa fa-star text-warning"></span></td>
                            </tr>
                            @foreach($group['group_members_data'] as $member)
                                <tr>
                                    <td>{{isset($member['user_id']) ? $member['user_id'] : '-'}}</td>
                                    <td>{{isset($member['name']) ? $member['name'] : '-'}}</td>
                                    <td>{{isset($member['mobile_number']) ? $member['mobile_number'] : '-'}}</td>
                                    <td>{{isset($member['department']) ? $member['department'] : '-'}}</td>
                                    <td>{{isset($member['email']) ? $member['email'] : '-'}}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <div class="row" style="padding-top: 15px;">
                            <div class="col-6">
                                <p class="lead">بيانات المشرف</p>

                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <th>الرقم الوظيفي:</th>
                                            <td>{{isset($group['teacher_data']['user_id']) ? $group['teacher_data']['user_id'] : '-'}}</td>
                                        </tr>
                                        <tr>
                                            <th>الاسم:</th>
                                            <td>{{isset($group['teacher_data']['name']) ? $group['teacher_data']['name'] : '-'}}</td>
                                        </tr>
                                        <tr>
                                            <th>رقم الهاتف:</th>
                                            <td>{{isset($group['teacher_data']['mobile_number']) ? $group['teacher_data']['mobile_number'] : '-'}}</td>
                                        </tr>
{{--                                        <tr>--}}
{{--                                            <th>القسم:</th>--}}
{{--                                            <td>{{isset($group['teacher_data']['department']) ? $group['teacher_data']['department'] : '-'}}</td>--}}
{{--                                        </tr>--}}
                                        <tr>
                                            <th>البريد الإلكتروني:</th>
                                            <td>{{isset($group['teacher_data']['email']) ? $group['teacher_data']['email'] : '-'}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-6">
                                <p class="lead">بيانات المشروع</p>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <th>العنوان المبدئي:</th>
                                            <td>{{isset($group['project_data']['initialProjectTitle']) ? $group['project_data']['initialProjectTitle'] : '-'}}</td>
                                        </tr>
                                        <tr>
                                            <th>هل المجموعة خريجة فصل أول؟</th>
                                            <td>{{(isset($group['project_data']['graduateInFirstSemester']) ? $group['project_data']['graduateInFirstSemester'] : '-') == 0 ? 'لا' : 'نعم'}}</td>
                                        </tr>
                                        <tr>
                                            <th>شكل المشروع:</th>
                                            <td>@if(isset($group['project_data']['tags']))
                                                    @foreach($group['project_data']['tags'] as $tag)
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
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    @if(isset($teacher) )
        <h1>لا يوجد مجموعات تخص المشرف: {{$teacher['name']}}.</h1>
    @elseif(getRole() == 'teacher' && $notifications == null)
        <h1>لا يوجد لديك مجموعات.</h1>
    @endif
@endif
