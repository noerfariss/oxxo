@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-sm-12">
                <x-member.top-navigation :member="$member" />
            </div>
        </div>

        <div class="card mb-4">
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
                    <div class="col-sm-4">
                        <h5 class="">Deposit</h5>
                        <div class="row mb-0">
                            <label class="col-sm-3 col-form-label">Cust. ID</label>
                            <div class="col-sm-9">
                                <label class="col-form-label fw-normal">{{ $member->numberid }}</label>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <label class="col-sm-3 col-form-label">nama</label>
                            <div class="col-sm-9">
                                <label class="col-form-label fw-normal">{{ $member->namestring }}</label>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <label class="col-sm-3 col-form-label">tgl lahir</label>
                            <div class="col-sm-9">
                                <label class="col-form-label fw-normal">{{ $member->born }}</label>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <label class="col-sm-3 col-form-label">alamat</label>
                            <div class="col-sm-9">
                                <label class="col-form-label fw-normal">{{ $member->address }}</label>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <label class="col-sm-3 col-form-label">member</label>
                            <div class="col-sm-9">
                                <label class="col-form-label fw-normal">{!! $member->memberstring !!}</label>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <label class="col-sm-3 col-form-label">Saldo</label>
                            <div class="col-sm-9">
                                <label class="col-form-label fs-6 text-primary fw-semibold">Rp {{ $saldo }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-8">
                        <div class="row">
                            <h5 class="d-flex justify-content-between">
                                <span>Riwayat Transaksi</span>
                                @haspermission('MEMBER_CREATE')
                                    <div class="d-flex justify-content-end gap-3">
                                        <button class="btn btn-sm btn-outline-primary" onclick="addModal()">
                                            <i class='bx bx-plus'></i> Deposit
                                        </button>
                                    </div>
                                @endhaspermission
                            </h5>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="dates">
                            </div>
                        </div>
                        <table class="table table-hover display nowrap noscroll mb-4" id="datatable">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>tanggal</th>
                                    <th>label</th>
                                    <th>total</th>
                                    <th>catatan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form action="{{ route('deposit.store') }}" method="POST" enctype="multipart/form-data" id="formAdd">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAddLabel">Deposit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalAddBody">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Tanggal</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="dates" id="date">
                                <div class="invalid-feedback" id="error-dates"></div>
                                <input type="hidden" name="member_id" value="{{ $member->id }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Jumlah</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="amount">
                                <div class="invalid-feedback" id="error-amount"></div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Catatan</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="note">
                                <div class="invalid-feedback" id="error-note"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $('#date').flatpickr({
            // mode: 'range',
            defaultDate: "{{ date('Y-m-d') }}",
            maxDate: '{{ date('Y-m-d') }}',
            onClose: function() {
                $('#date').blur();
            }
        });


        $('#dates').flatpickr({
            mode: 'range',
            defaultDate: ["{{ date('Y-m-01') }}", "{{ date('Y-m-d') }}"],
            maxDate: '{{ date('Y-m-d') }}',
            onClose: function() {
                $('#dates').blur();
                datatables.ajax.reload();
            }
        });

        let token = $('input[name="_token"]').val();

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
                [0, 'desc']
            ],
            ajax: {
                url: "{{ route('deposit.ajax') }}",
                type: "POST",
                data: function(d) {
                    d._token = $("input[name=_token]").val();
                    d.dates = $('#dates').val();
                    d.member = '{{ $member->id }}';
                },
            },
            columns: [{
                    data: 'id',
                },
                {
                    data: 'dates'
                },
                {
                    data: 'typestring',
                },
                {
                    data: 'amountstring',
                    name: 'amount',
                    render: function(data) {
                        return 'Rp ' + data;
                    }
                },
                {
                    data: 'note',
                },
            ]
        });

        $('#formAdd').submit(function(e) {
            e.preventDefault();

            const data = $(this).serialize();
            const url = $(this).attr('action');

            $.ajax({
                    type: 'POST',
                    url: url,
                    data: data
                })
                .done(function(res) {
                    $('#formAdd')[0].reset();
                    Swal.fire({
                        title: 'Berhasil',
                        html: 'Deposit berhasil ditambahkan',
                        icon: 'success',
                        showCancelButton: false,
                        showConfirmButton: false,
                        timer: 3000,
                    })

                })
                .fail(function(xhr) {
                    if (xhr.status === 422) {
                        // Validasi dari Laravel
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            const field = $(`[name="${key}"]`);
                            field.addClass('is-invalid');
                            $(`#error-${key}`).text(value[0]);
                        });
                    } else {
                        console.error('Unexpected Error:', xhr);
                        Swal.fire({
                            title: 'Terjadi Kesalahan',
                            html: 'Cobalah beberapa saat lagi',
                            icon: 'error',
                            showCancelButton: false,
                            showConfirmButton: false,
                            timer: 3000,
                        })
                    }

                })

        })

        function addModal() {
            $('#modalAdd').modal('show');
        }
    </script>
@endpush
