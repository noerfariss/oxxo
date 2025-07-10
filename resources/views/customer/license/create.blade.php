@extends('member.layouts.layouts')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y mx-auto">
        @include('member.license.licenseTitle')

        <div class="row">
            <div class="col-sm-6 offset-sm-3">
                <div class="d-flex justify-content-center">
                    <x-member.top-nav />
                </div>

                <div class="card">
                    <h5 class="card-header">Tambah Lisensi
                        <a href="{{ route('member.license.index') }}" class="fs-6 btn btn-link btn-sm float-end">Kembali</a>
                    </h5>
                    <div class="card-body">
                        <form action="{{ route('member.license.store') }}" method="POST" id="my-form">
                            @csrf
                            <div class="row">
                                <div class="col-sm-5 col-12">
                                    <div class="mb-2">
                                        <label for="whatsapp" class="form-label">jenis lisensi</label>
                                        <select name="is_demo" id="is_demo" class="form-control">
                                            <option value="">-- Jenis Lisensi --</option>
                                            <option value="0">DEMO</option>
                                            {{-- <option value="7">DEMO PAYMENT</option> --}}
                                            <option value="1">PREMIUM</option>
                                            <option value="2">PRAKTIS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-7 col-12">
                                    <div class="mb-2">
                                        <label for="whatsapp" class="form-label">Total</label>
                                        <h4 id="total" class="fw-semibold">Rp 0</h4>
                                        <input type="hidden" name="total" value="0">
                                    </div>
                                </div>
                            </div>

                            {{-- <hr style="border: 1px solid #e0e0e0"> --}}

                            <h5 id="headingPembayaran" class="mt-3">Pembayaran</h5>
                            <div id="error-message"></div>
                            <section id="boxPembayaran"></section>

                            <div class="mb-3 mt-3">
                                <button type="submit" class="btn btn-primary" id="btnSubmit">Proses</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        #boxPembayaran {
            height: 250px;
            overflow-y: scroll
        }

        .form-check-input:checked+img+div+div,
        .form-check-input:checked+img+div {
            font-weight: bold;
        }

        .form-check-input:checked~* {
            border-color: #0d6efd !important;
            /* Bootstrap Primary */
        }

        .form-check-label {
            border: 2px solid transparent;
            border-radius: 0.5rem;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s;
        }

        .form-check-input:checked+* {
            /* Tidak efektif langsung karena + hanya bekerja ke satu elemen, maka kita gunakan JS di bawah juga */
        }

        /* Tambahan kelas saat dipilih */
        .card.selected {
            border: 2px solid #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>

    <script>
        $(document).on('change', 'input[name="payment_channel"]', function() {
            $('.form-check.card').removeClass('selected');
            $(this).closest('.form-check.card').addClass('selected');
        });

        $(document).ready(function() {
            // Reset select ke default kosong saat halaman direfresh
            $('#is_demo').val('');
            $('#btnSubmit').attr('disabled', 'disabled');
            $('#headingPembayaran, #boxPembayaran').hide();

            // Set harga awal
            $('#total').text('Rp 0');
            $('input[name="total"]').val(0);

            $('#is_demo').on('change', function() {
                var selected = $(this).val();
                var total = 0;
                var formatted = "Rp 0";

                if (selected == "0") {
                    $('#btnSubmit').removeAttr('disabled');
                    $('#headingPembayaran, #boxPembayaran').hide();

                } else if (selected == "7") {
                    $('#btnSubmit').removeAttr('disabled');
                    $('#headingPembayaran, #boxPembayaran').show();

                    total = 1000;
                    formatted = "Rp 1000";

                    getPaymentChannelList();

                } else if (selected == "1") {
                    $('#btnSubmit').removeAttr('disabled');
                    $('#headingPembayaran, #boxPembayaran').show();

                    total = 400000;
                    formatted = "Rp 400.000";

                    getPaymentChannelList();

                } else if (selected == "2") {
                    $('#btnSubmit').removeAttr('disabled');
                    $('#headingPembayaran, #boxPembayaran').show();

                    total = 650000;
                    formatted = "Rp 650.000";

                    getPaymentChannelList();
                } else {
                    $('#btnSubmit').attr('disabled', 'disabled');
                    $('#headingPembayaran, #boxPembayaran').hide();
                }

                $('#total').text(formatted);
                $('input[name="total"]').val(total);
            });


            // simpan proses pembayaran
            $('#my-form').submit(function(e) {
                e.preventDefault();
                const url = $(this).attr('action');
                const data = $(this).serialize();

                $.ajax({
                        type: 'POST',
                        url: url,
                        data: data,
                        beforeSend: function() {
                            $('#btnSubmit').text('Loading...').attr('disabled', 'disabled');
                        }
                    })
                    .done(function(res) {
                        Swal.fire({
                            title: 'Sukses',
                            text: res.message,
                            icon: 'success',
                            showCancelButton: false,
                            showConfirmButton: false,
                        });

                        setTimeout(() => {
                            window.location.href = res.redirect;
                            $('#btnSubmit').text('Proses');

                        }, 3000);

                    })
                    .fail(function(xhr) {
                        if (xhr.status === 401) {
                            $('#error-message').html(
                                    `<div class="alert alert-danger">${xhr.responseJSON.message}</div>`)
                                .show();

                        } else {
                            let errors = xhr.responseJSON.errors;
                            let errorList = '<ul style="padding-left:8px;">';

                            for (let field in errors) {
                                if (errors.hasOwnProperty(field)) {
                                    errors[field].forEach(function(error) {
                                        errorList += '<li>' + error + '</li>';
                                    });
                                }
                            }

                            errorList += '</ul>';

                            // Tampilkan ke pengguna
                            $('#error-message').html(
                                `<div class="alert alert-danger">${errorList}</div>`).show();
                        }

                        $('#btnSubmit').text('Proses').removeAttr('disabled');
                    })

            });
        });

        function getPaymentChannelList() {
            $.ajax({
                    type: 'GET',
                    url: '{{ route('payment.channel.list') }}',
                    beforeSend: function() {
                        $('#boxPembayaran').html('Loading...');
                    }
                })
                .done(function(res) {
                    let payments = '';

                    res.data.forEach((item, index) => {
                        payments += `
                                <div class="form-check card mb-3 shadow-sm p-0 border overflow-hidden">
                                    <label class="form-check-label d-flex align-items-center py-1 px-2 w-100" for="payment-${index}" style="cursor: pointer;">
                                        <input class="me-2" type="radio" name="payment_channel" id="payment-${index}" value="${item.code}">
                                        <img src="${item.icon_url}" alt="${item.name}" class="me-3" style="width: 50px; height: 50px; object-fit: contain;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">${item.name}</h6>
                                            <small class="text-muted">${item.group}</small>
                                        </div>

                                    </label>
                                </div>
                            `;
                    });

                    $('#boxPembayaran').html(payments);
                })
                .fail(function(error) {
                    console.error(error);
                    $('#boxPembayaran').html('<div class="alert alert-danger">Gagal memuat metode pembayaran.</div>');
                });
        }
    </script>
@endpush
