@extends('layout')
@section('title','الصفحة الرئيسية')
@section('small_box')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{isset($statistics['number_of_students']) ? $statistics['number_of_students'] : '-'}}</h3>
                    <p>عدد الطلبة</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{isset($statistics['number_of_groups']) ? $statistics['number_of_groups'] : '-'}}</h3>
                    <p>عدد المجموعات</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{isset($statistics['number_of_teamed_students']) ? $statistics['number_of_teamed_students'] : '-'}}</h3>
                    <p>عدد الطلاب المنضمين لِفِرَق </p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{isset($statistics['number_of_teachers']) ? $statistics['number_of_teachers'] : '-'}}</h3>
                    <p>عدد المشرفين</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    @includeIf('teacher.teacherNotifications')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa fa-bar-chart"></i>
                        إحصائيات الطلبة حسب التخصص
                    </h3>
                </div>
                <div class="card-body">
                    <div id="bar-chart2" style="height: 250px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa fa-bar-chart"></i>
                        إحصائيات شكل المشروع
                    </h3>
                </div>
                <div class="card-body">
                    <div id="bar-chart" style="height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>
    @includeIf('admin.create_user_form')
    @includeIf('teacher.teacherGroups')
    @includeIf('admin.exports_form')
@endsection
@push('script')
    <script>
        $(function () {
            var bar_data = {
                data: <?php echo $statistics['departments_data'];?>,
                bars: {show: true}
            }
            $.plot('#bar-chart', [bar_data], {
                grid: {
                    borderWidth: 1,
                    borderColor: '#f3f3f3',
                    tickColor: '#f3f3f3'
                },
                series: {
                    bars: {
                        show: true, barWidth: 0.5, align: 'center',
                    },
                },
                colors: ['#3c8dbc'],
                xaxis: {
                    ticks: <?php echo $statistics['departments'];?>
                }
            })

            /* Bar char for second statistic */
            var bar_data2 = {
                data: <?php echo $statistics['tags_data']; ?>,
                bars: {show: true}
            }
            $.plot('#bar-chart2', [bar_data2], {
                grid: {
                    borderWidth: 1,
                    borderColor: '#f3f3f3',
                    tickColor: '#f3f3f3'
                },
                series: {
                    bars: {
                        show: true, barWidth: 0.5, align: 'center',
                    },
                },
                colors: ['#3c8dbc'],
                xaxis: {
                    ticks: <?php echo $statistics['tags'];?>
                }
            })

        })

        /*
         * Custom Label formatter
         * ----------------------
         */
        function labelFormatter(label, series) {
            return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
                + label
                + '<br>'
                + Math.round(series.percent) + '%</div>'
        }
    </script>
@endpush
