@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <form action="{{ route('role.update', ['role' => $role->id]) }}" method="POST" enctype="multipart/form-data"
            id="my-form">
            @csrf
            @method('PATCH')
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <h5 class="card-header">Edit role</h5>
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

                            <input type="hidden" name="id" value="{{ $role->id }}">

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">role</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" value="{{ $role->name }}">
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-9 offset-sm-2">
                                    <a href="{{ route('role.index') }}" class="btn btn-link btn-sm">Kembali</a>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </div>
                            </div>
                        </div>
                        <!-- /Account -->
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <h5 class="card-header">Permission</h5>
                        <div class="card-body">
                            <table class="table table-sm table-hover" id="datatable">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Permission</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /Account -->
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\Role\RoleEditRequest', '#my-form') !!}

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

    <style>
        #datatable_filter {
            display: block !important;
        }
    </style>

    <script>
        var datatables = $('#datatable').DataTable({
            scrollY: '300px',
            scrollX: false,
            processing: true,
            serverSide: false,
            // searching: true,
            lengthChange: false,
            paging: false,
            bDestroy: true,
            info: false,
            ajax: {
                url: "{{ route('permission.ajax', ['edit' => $role->id]) }}",
                type: "POST",
                data: function(d) {
                    d._token = $("input[name=_token]").val();
                    d.cari = $('#cari').val();
                },
            },
            columns: [{
                    data: 'id',
                    render: function(data, type, row) {
                        const role = row.role_id ? 'checked' : '';
                        return `<input type="checkbox" value="${data}" name="permission[]" ${role} id="${data}" class="checkboxs">`;
                    }
                },
                {
                    data: 'name'
                },
            ],
        });

        $('#datatable tbody').on('click', 'tr td', function() {
            const data = datatables.row(this).data();
            const uuid = data.id;
            const inputChecked = $(`#${uuid}`);

            if ($(inputChecked).is(':checked')) {
                $(inputChecked).prop('checked', false);
            } else {
                $(inputChecked).prop('checked', true);
            }
        });

        $('#datatable tbody').on('click', '.checkboxs', function() {
            const uuid = $(this).attr('id');
            const inputChecked = $(`#${uuid}`);

            if ($(inputChecked).is(':checked')) {
                $(inputChecked).prop('checked', false);
            } else {
                $(inputChecked).prop('checked', true);
            }
        });

        $('#cari').keyup(function() {
            datatables.search($('#cari').val()).draw();
        });
    </script>
@endpush
