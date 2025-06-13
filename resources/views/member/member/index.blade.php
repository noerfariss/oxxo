@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-sm-12">
                <x-member.top-navigation />
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">member
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
                    <div class="col-sm-1 mt-2">
                        @include('member.layouts.globalFilter', [
                            'division' => true,
                            'office' => true,
                            'position' => true,
                        ])
                    </div>
                    <div class="col-sm-8 mt-2">
                        @haspermission('MEMBER_CREATE')
                            <div class="d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="modalImport()">
                                    <i class='bx bx-plus'></i> import
                                </button>

                                <a href="{{ route('member.create') }}" class="btn btn-sm btn-outline-primary ">
                                    <i class='bx bx-plus'></i> tambah
                                </a>
                            </div>
                        @endhaspermission
                    </div>
                </div>
            </div>

            <table class="table table-hover display nowrap noscroll mb-4" id="datatable">
                <thead>
                    <tr>
                        <th>nik</th>
                        <th>phone</th>
                        <th>nama</th>
                        <th>jabatan</th>
                        <th>gender</th>
                        <th>status</th>
                        <th>dibuat</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- Modal import --}}
    <div class="modal fade" id="modalImport" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form action="{{ route('member.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalImportLabel">Import Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalImportBody">
                        <input type="file" class="form-control" id="file" name="file">
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <a href="{{ route('member.template') }}" class="btn btn-outline-danger btn-sm">
                            Download template
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">Import</button>
                    </div>
                </div>
            </form>
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
                [6, 'desc']
            ],
            ajax: {
                url: "{{ route('member.ajax') }}",
                type: "POST",
                data: function(d) {
                    d._token = $("input[name=_token]").val();
                    d.status = $('.btn-check:checked').val();
                    d.cari = $('#cari').val();
                    d.office = $('#office_filter').val();
                    d.division = $('#division_filter').val();
                    d.position = $('#position_filter').val();
                },
            },
            columns: [{
                    data: 'nik'
                },
                {
                    data: 'phone'
                },
                {
                    data: 'name',
                    render: function(data, type, row) {
                        return `<b>${data}</b>
                                <div>${row.office_id ? row.office.name : ''} | ${row.division_id ? row.division.name : ''}</div>`
                    }
                },
                {
                    data: 'position_id',
                    render: function(data, type, row) {
                        return row.position.name;
                    }
                },
                {
                    data: 'genderstring',
                    name: 'gender',
                },
                {
                    data: 'statusstring',
                    name: 'status'
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

        $('#datatable tbody').on('click', 'tr td:not(:last-child)', function() {
            const data = datatables.row(this).data();
            $('#modalDetailTable').modal('show');
            $('#modalDetailTableLabel').text('Member');

            const gender = getGender(data.gender);

            const dataTable = `
                <table class="table table-sm table-hover">
                    <tbody>
                        <tr>
                            <td class="col-form-label">nik</td>
                            <td>:</td>
                            <td>${data.nik}</td>
                        </tr>
                        <tr>
                            <td class="col-form-label">email</td>
                            <td>:</td>
                            <td>${data.email}</td>
                        </tr>
                        <tr>
                            <td class="col-form-label">phone</td>
                            <td>:</td>
                            <td>${data.phone}</td>
                        </tr>
                        <tr>
                            <td class="col-form-label">nama</td>
                            <td>:</td>
                            <td>${data.name}</td>
                        </tr>
                        <tr>
                            <td class="col-form-label">gender</td>
                            <td>:</td>
                            <td>${gender}</td>
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

            $('#modalDetailTableBody').html(dataTable);
        });

        function modalImport() {
            $('#modalImport').modal('show');
        }
    </script>
@endpush
