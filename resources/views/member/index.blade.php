@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
            <h5 class="card-title text-primary mb-3">Halooo, Selamat datang kembali <b>{{ Auth::user()->nama }}</b> 🎉</h5>
            <div class="row">
                <div class="col-sm-12">
                    <input type="text" class="form-control" id="tanggal">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-2 mb-4">
                <div class="card mb-3">
                    <div class="px-3 py-1">
                        <h6 class="col-form-label">pegawai</h6>
                        <h2 class="fw-bold" id="ts-members">0</h2>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="px-3 py-1">
                        <h6 class="col-form-label">kantor</h6>
                        <h2 class="fw-bold" id="ts-offices">0</h2>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="px-3 py-1">
                        <h6 class="col-form-label">divisi</h6>
                        <h2 class="fw-bold" id="ts-divisions">0</h2>
                    </div>
                </div>


            </div>
            <div class="col-sm-10 mb-4">
                <div class="card">
                    <h5 class="card-header col-form-label"></h5>
                    <section id="boxDailyAttendance" class="text-center"></section>
                </div>
            </div>
        </div>

        <div class="row">



        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/0.5.7/chartjs-plugin-annotation.js">
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        let token = $('input[name="_token"]').val();
        const url = '{{ url('/') }}';

        $('#tanggal').flatpickr({
            mode: 'range',
            defaultDate: ["{{ date('Y-m-01') }}", "{{ date('Y-m-d') }}"],
            maxDate: '{{ date('Y-m-d') }}',
            onClose: function() {
                $('#tanggal').blur();
                getGrafik();
            }
        });

        getGrafik();

        function getGrafik() {
            // --- init
            // memberCount();
            // memberOffice();
            // memberDivision();
            // dailyAttendance()
        }

        function memberCount() {
            const tsmember = '#ts-members';
            $.ajax({
                    type: 'GET',
                    url: '{{ route('chart.member') }}',
                    beforeSend: function() {
                        $(tsmember).text(0);
                    }
                })
                .done(function(msg) {
                    const data = msg.data;
                    $(tsmember).text(data);
                })
                .fail(function(err) {
                    console.log(err);
                });
        }

        function memberOffice() {
            const tsoffices = '#ts-offices';
            $.ajax({
                    type: 'GET',
                    url: '{{ route('chart.office') }}',
                    beforeSend: function() {
                        $(tsoffices).text(0);
                    }
                })
                .done(function(msg) {
                    const data = msg.data;
                    $(tsoffices).text(data);
                })
                .fail(function(err) {
                    console.log(err);
                });
        }

        function memberDivision() {
            const divisions = '#ts-divisions';
            $.ajax({
                    type: 'GET',
                    url: '{{ route('chart.office') }}',
                    beforeSend: function() {
                        $(divisions).text(0);
                    }
                })
                .done(function(msg) {
                    const data = msg.data;
                    $(divisions).text(data);
                })
                .fail(function(err) {
                    console.log(err);
                });
        }

        function dailyAttendance() {
            const boxDailyAttendance = '#boxDailyAttendance';
            const canvasDailyAttendance = 'canvasDailyAttendance';

            $.ajax({
                    type: 'POST',
                    url: '{{ route('chart.daily.attendance') }}',
                    data: {
                        _token: token,
                        dates: $('#tanggal').val()
                    },
                    beforeSend: function() {
                        $(boxDailyAttendance).html('Loading...');
                    }
                })
                .done(function(msg) {

                    $(boxDailyAttendance).html(`<canvas id="${canvasDailyAttendance}"></canvas>`);

                    const data = msg.data;

                    let date = [];
                    let work = [];
                    let sick = [];
                    let permit = [];
                    let cuti = [];

                    data.map(function(val, i) {
                        date.push(val.date);
                        work.push(val.work);
                        sick.push(val.sick);
                        permit.push(val.permit);
                        cuti.push(val.cuti);
                    });

                    const datas = [{
                            label: 'Kerja',
                            daily: work,
                            borderColor: '#2E7D32',
                        },
                        {
                            label: 'Sakit',
                            daily: sick,
                            borderColor: '#C62828',
                        },
                        {
                            label: 'Izin',
                            daily: permit,
                            borderColor: '#FF8F00',
                        },
                        {
                            label: 'Cuti',
                            daily: cuti,
                            borderColor: '#2196F3',
                        },
                    ]

                    const dataValue = [date, datas];
                    chartLINE('absensi', canvasDailyAttendance, 120, dataValue, true);

                })
                .fail(function(err) {
                    console.log(err);
                });
        }

        function chartLINE(label, boxDiv, chartHeight, dataValue, setLegend) {
            var ctx = document.getElementById(boxDiv);
            ctx.height = chartHeight;

            let datasets = [];
            dataValue[1].map(function(val, i) {
                datasets.push({
                    label: val.label,
                    data: val.daily,
                    borderColor: val.borderColor,
                    pointBorderColor: val.borderColor,
                    tension: 0,
                }, )
            })

            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dataValue[0], // sumbu ---- x  ---
                    datasets: datasets,
                },
                options: {
                    plugins: {
                        datalabels: {
                            display: true,
                            color: '#000000',
                            anchor: 'end',
                            align: 'end',
                            offset: 3,
                        },
                    },
                    legend: {
                        display: setLegend
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            stacked: true,
                            ticks: {
                                autoSkip: false,
                            },
                        }],
                        yAxes: [{
                            display: true,
                            stacked: true,
                            ticks: {
                                beginAtZero: true,
                                fontSize: 12,
                            },
                            afterDataLimits(scale) {
                                scale.max += 2;
                            }
                        }],
                    },
                }
            });
        }

        function chartBar(label, boxDiv, chartHeight, dataValue, setLegend) {
            var ctx = document.getElementById(boxDiv);
            ctx.height = chartHeight;

            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label,
                    datasets: dataValue,
                },
                options: {
                    plugins: {
                        datalabels: {
                            display: true,
                            color: '#000000',
                            anchor: 'end',
                            align: 'top',
                            // formatter: function(value, context) {
                            //     return value + '%';
                            // }
                        },
                    },
                    legend: {
                        display: setLegend
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            stacked: true,
                            ticks: {
                                autoSkip: false,
                            },
                        }],
                        yAxes: [{
                            display: true,
                            stacked: true,
                            ticks: {
                                beginAtZero: true,
                                fontSize: 11,
                            },
                            afterDataLimits(scale) {
                                scale.max += 1;
                            }
                        }],
                    },
                }
            });
        }
    </script>
@endpush
