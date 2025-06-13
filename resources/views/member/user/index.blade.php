@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card mb-4">
            <h5 class="card-header">user
                {!! statusBtn() !!}
            </h5>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3 mt-2">
                        <input type="text" id="cari" class="form-control" placeholder="Cari...">
                    </div>
                    <div class="col-sm-9 mt-2">
                        @haspermission('USERWEB_CREATE')
                            <a href="{{ route('user.create') }}" class="btn btn-sm btn-outline-primary float-end">
                                <i class='bx bx-plus'></i> tambah
                            </a>
                        @endhaspermission

                    </div>
                </div>

                @if (session()->has('pesan'))
                    {!! session('pesan') !!}
                @endif
            </div>

            <table class="table table-hover display nowrap noscroll mb-4" id="datatable">
                <thead>
                    <tr>
                        <th>foto</th>
                        <th>nama</th>
                        <th>email</th>
                        <th>whatsapp</th>
                        <th>roles</th>
                        <th>status</th>
                        <th>time input</th>
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
            scrollX: false,
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: false,
            pageLength: 10,
            bDestroy: true,
            info: false,
            order: [
                [6, 'desc']
            ],
            ajax: {
                url: "{{ route('user.ajax') }}",
                type: "POST",
                data: function(d) {
                    d._token = $("input[name=_token]").val();
                    d.status = $('.btn-check:checked').val();
                    d.cari = $('#cari').val();
                },
            },
            columns: [{
                    name: 'photo',
                    data: 'photourl',
                    render: function(data) {
                        return `<img src="${data}" class="rounded"/>`;
                    }
                },
                {
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'whatsapp'
                },
                {
                    data: 'rolestring'
                },
                {
                    data: 'statusstring'
                },
                {
                    name: 'created_at',
                    data: 'timeinput'
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
