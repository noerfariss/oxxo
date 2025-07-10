<!DOCTYPE html>

<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ env('APP_NAME') }} | Verifikasi</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/css/core.css') }}"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet"
        href="{{ asset('sneat-1.0.0/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('sneat-1.0.0/assets/vendor/css/pages/page-auth.css') }}" />
    <!-- Helpers -->
    <script src="{{ asset('sneat-1.0.0/assets/vendor/js/helpers.js') }}"></script>
</head>

<body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">

                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="" class="d-flex justify-content-center align-items-center">
                                <img src="{{ asset('images/logo.png') }}" style="max-width: 80%;" class="img-fluid">
                            </a>
                        </div>

                        <!-- /Logo -->
                        <h4 class="mb-2">Kode Verifikasi</h4>
                        <p class="mb-4">Masukkan kode verifikasi yang sudah Anda dapatkan melalui Whatsapp</p>

                        <div id="error-message"></div>

                        <form id="my-form" class="mb-3"
                            action="{{ route('member.register.verification.process', ['uuid' => $member->uuid]) }}"
                            method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="otp" class="form-label">Kode OTP</label>
                                <input type="number" class="form-control" id="otp" name="otp" autofocus />
                            </div>

                            <button class="btn btn-primary d-grid w-100 btn-submit">Verifikasi</button>
                        </form>
                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('sneat-1.0.0/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat-1.0.0/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat-1.0.0/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat-1.0.0/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#my-form').submit(function(e) {
                e.preventDefault();
                const url = $(this).attr('action');
                const data = $(this).serialize();
                const method = $(this).attr('method');

                $.ajax({
                        type: method,
                        url: url,
                        data: data,
                        beforeSend: function() {
                            $('.btn-submit').text('Loading...').attr('disabled', 'disabled');
                        }
                    })
                    .done(function(res) {
                        window.location.href = res.url;
                        $('.btn-submit').text('Verifikasi');
                    })
                    .fail(function(xhr) {
                        const status = xhr.status;

                        if (status == 422) {
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
                        } else {
                            $('#error-message').html(
                                    `<div class="alert alert-danger">${xhr.responseJSON.message}</div>`)
                                .show();
                        }

                        $('.btn-submit').text('Verifikasi').removeAttr('disabled');
                    })

            });

            $('#error-message').on('click', '#btn-resend', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');

                $.ajax({
                        type: 'GET',
                        url: url,
                        beforeSend: function() {
                            $('.btn-submit').text('Loading...').attr('disabled', 'disabled');
                        }
                    })
                    .done(function(res) {
                        $('#error-message').html( `<div class="alert alert-success">Kode OTP berhasil dikirim</div>`);
                        $('.btn-submit').text('Verifikasi').removeAttr('disabled');
                    })
                    .fail(function(error) {
                        const status = xhr.status;

                        if (status == 422) {
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
                        } else {
                            $('#error-message').html(
                                    `<div class="alert alert-danger">${xhr.responseJSON.message}</div>`)
                                .show();
                        }

                        $('.btn-submit').text('Verifikasi').removeAttr('disabled');
                    })
            })
        });
    </script>

</body>

</html>
