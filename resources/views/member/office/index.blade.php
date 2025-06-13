@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card mb-4">
            <h5 class="card-header">Outlet
                {!! statusBtn() !!}
            </h5>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3 mt-2">
                        <input type="text" id="cari" class="form-control" placeholder="Cari...">
                    </div>
                    <div class="col-sm-9 mt-2">
                        @haspermission('OFFICE_CREATE')
                            <a href="{{ route('office.create') }}" class="btn btn-sm btn-outline-primary float-end">
                                <i class='bx bx-plus'></i> tambah
                            </a>
                        @endhaspermission

                    </div>
                </div>

                @if (session()->has('pesan'))
                    {!! session('pesan') !!}
                @endif
            </div>

            <table class="table table-hover noscroll mb-4" id="datatable">
                <thead>
                    <tr>
                        <th>nama</th>
                        <th>cabang</th>
                        <th>kios</th>
                        <th>status</th>
                        <th>dibuat</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
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
            info: false,
            responsive: true,
            ajax: {
                url: "{{ route('office.ajax') }}",
                type: "POST",
                data: function(d) {
                    d._token = $("input[name=_token]").val();
                    d.status = $('.btn-check:checked').val();
                    d.cari = $('#cari').val();
                },
            },
            columnDefs: [{
                    width: "40%",
                    targets: 0
                },
                {
                    width: "10%",
                    targets: 1
                },
                {
                    width: "10%",
                    targets: 2
                },
                {
                    width: "10%",
                    targets: 3
                },
                {
                    width: "10%",
                    targets: 4
                },
                {
                    width: "10%",
                    targets: 5
                },
            ],
            columns: [{
                    data: 'name',
                    render: function(data, type, row) {
                        const address = row.address;
                        const city = row.city_id ? row.city.name : '';
                        const coordinate = row.latitude ? `${row.latitude}, ${row.longitude}` : '';

                        return `<strong>${data.toUpperCase()}</strong> <br>
                                ${address} <br>
                                ${city} <br><br>
                                ${coordinate}
                                `;
                    }
                },
                {
                    data: 'branchstring',
                    name: 'is_branch'
                },
                {
                    data: 'kios_count'
                },
                {
                    data: 'statusstring'
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'aksi'
                },
            ]
        });

        $('#cari').keyup(function() {
            datatables.search($('#cari').val()).draw();
        });
    </script>
@endpush
