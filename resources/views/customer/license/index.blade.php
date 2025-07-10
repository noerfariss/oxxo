@extends('customer.layouts.layouts')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y mx-auto">
        @include('customer.license.licenseTitle')

        <div class="row">
            <div class="col-md-6 offset-sm-3">
                <div class="d-flex justify-content-center">
                    <x-customer.top-nav />
                </div>

                <div class="row">
                    <div class="col-md-12 col-12 mb-md-0 mb-4">
                        <div class="card">
                            <h5 class="card-header">Deposit / Saldo
                                <button type="button" class="btn btn-primary btn-sm float-end" onclick="addModal()">tambah
                                    saldo</button>
                            </h5>
                            <div class="card-body">
                                <h5>Saldo : Rp {{ $data }}</h5>
                            </div>
                        </div>
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
                                <input type="hidden" name="member_id" value="{{ auth()->guard('member')->id() }}">
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

@push('scripts')
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
