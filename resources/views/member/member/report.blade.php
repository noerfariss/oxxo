@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-sm-12">
                <x-member.report-navigation />
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">Report Saldo
                {!! statusBtn() !!}
            </h5>

            <div class="card-body">
                @if (session()->has('pesan'))
                    {!! session('pesan') !!}
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-sm-3 mt-2">
                        <input type="text" id="cari" class="form-control" placeholder="Cari...">
                    </div>
                    <div class="col-sm-3 mt-2">

                    </div>
                </div>

            </div>

            <table class="table table-hover display nowrap noscroll mb-4" id="datatable">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>nama</th>
                        <th>tanggal lahir</th>
                        <th>alamat</th>
                        <th>status</th>
                        <th>saldo</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


    @include('member.layouts.modalDetailTable')
@endsection

@push('script')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

    <script>
        var datatables = $('#datatable').DataTable({
            scrollY: false,
            scrollX: true,
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: false,
            pageLength: 10,
            bDestroy: true,
            responsive: true,
            order: [
                [5, 'desc']
            ],
            ajax: {
                url: "{{ route('member.report.ajax') }}",
                type: "POST",
                data: function(d) {
                    d._token = $("input[name=_token]").val();
                    d.status = $('.btn-check:checked').val();
                    d.cari = $('#cari').val();
                },
            },
            columns: [{
                    data: 'numberid'
                },
                {
                    data: 'namestring',
                    name: 'name',
                    render: function(data, type, row) {
                        return `${row.namestring} <br> ${row.memberstring}`;
                    }
                },
                {
                    data: 'born',
                },
                {
                    data: 'address',
                },
                {
                    data: 'statusstring',
                    name: 'status'
                },
                {
                    data: 'lastest_saldo',
                    render: function(data) {
                        return 'Rp ' + data;
                    }
                },
            ]
        });

        $('#cari').keyup(function() {
            datatables.search($('#cari').val()).draw();
        });

        $('#datatable tbody').on('click', 'tr td:not(:last-child)', function() {
            const data = datatables.row(this).data();
            $('#modalDetailTable').modal('show');
            $('#modalDetailTableLabel').text('Customer');

            console.log(data);


            let dataTable = `
                <table class="table table-sm table-hover">
                    <tbody>
                        <tr>
                            <td class="col-form-label">cust. ID</td>
                            <td>:</td>
                            <td>${data.numberid}</td>
                        </tr>
                        <tr>
                            <td class="col-form-label">nama</td>
                            <td>:</td>
                            <td>${data.namestring}</td>
                        </tr>
                        <tr>
                            <td class="col-form-label">tanggal lahir</td>
                            <td>:</td>
                            <td>${data.born}</td>
                        </tr>
                        <tr>
                            <td class="col-form-label">alamat</td>
                            <td>:</td>
                            <td>${data.address}</td>
                        </tr>
                        <tr>
                            <td class="col-form-label">Member</td>
                            <td>:</td>
                            <td>${data.memberstring}</td>
                        </tr>
                         <tr>
                            <td class="col-form-label">status</td>
                            <td>:</td>
                            <td>${data.statusstring}</td>
                        </tr>
                        <tr>
                            <td class="col-form-label">register</td>
                            <td>:</td>
                            <td>${data.created_at}</td>
                        </tr>
                    </tbody>
                </table>
            `;

            dataTable += data.aksi;

            $('#modalDetailTableBody').html(dataTable);
        });
    </script>
@endpush
