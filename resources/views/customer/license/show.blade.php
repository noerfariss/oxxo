@extends('member.layouts.layouts')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y mx-auto">
        @include('member.license.licenseTitle')

        <div class="row">
            <div class="col-md-8 offset-sm-2">
                <div class="d-flex justify-content-center">
                    <x-member.top-nav />
                </div>

                <div class="row">
                    <div class="col-md-7 col-12 mb-md-0 mb-4">
                        <div class="card">
                            <h5 class="card-header">Detail Lisensi
                                <a href="{{ route('member.license.index') }}"
                                    class="fs-6 btn btn-link btn-sm float-end">Kembali</a>
                            </h5>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label for="whatsapp" class="form-label">jenis lisensi</label>
                                    <h6 class="fw-semibold fs-5 text-primary">{{ $license->is_demo }}</h6>
                                </div>
                                <div class="mb-4">
                                    <label for="whatsapp" class="form-label">kode lisensi</label>
                                    <h6 class="mb-3 fw-bold fs-5 mb-0 me-2" id="licenseCode">{{ $license->license }}</h6>
                                    <button class="btn btn-sm btn-outline-primary" onclick="copyLicenseCode()">Copy</button>
                                </div>
                                <div class="mb-2">
                                    <label for="whatsapp" class="form-label">harga</label>
                                    <h6>Rp {{ number_format($license->total, 0) }}</h6>
                                </div>
                                <div class="mb-2">
                                    <label for="whatsapp" class="form-label">status</label>
                                    <h6>
                                        @if ($license->status)
                                            <span class="badge bg-success text-dark">aktif</span>
                                        @else
                                            <span class="badge bg-danger">nonaktif</span>
                                        @endif
                                    </h6>
                                </div>
                                <div class="mb-5">
                                    <label for="whatsapp" class="form-label">dibuat</label>
                                    <h6>{{ $license->created_at }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($license->payment)
                        <div class="col-md-5 col-12 mb-md-0 mb-4">
                            <div class="card">
                                <h5 class="card-header">Pembayaran
                                    <span class="float-end">
                                        <h5>
                                            @if ($license->payment->payment_status == 'UNPAID')
                                                <span class="badge bg-danger">UNPAID</badge>
                                                @elseif ($license->payment->payment_status === 'EXPIRED')
                                                    <div class="">
                                                        <span class="badge bg-danger mt-4 mb-1">KEDALUWARSA</span>
                                                        <h5>{{ $license->payment->payment_date }}</h5>
                                                    </div>
                                                @elseif ($license->payment->payment_status === 'FAILED')
                                                    <div class="">
                                                        <span class="badge bg-danger mt-4 mb-1">PEMBAYARAN GAGAL</span>
                                                        <h5>{{ $license->payment->payment_date }}</h5>
                                                    </div>
                                                @else
                                                    <span class="badge bg-success text-dark">PAID</badge>
                                            @endif
                                        </h5>
                                    </span>
                                </h5>
                                <div class="card-body">
                                    <div class="text-center">
                                        <h6 class="mt-0 fw-semibold fs-4">Total Rp {{ number_format($license->total) }}</h6>

                                        <h5 class="fw-semibold fs-5 p-0 mb-1">{{ $license->payment->payment_name }}</h5>
                                        @if ($license->payment->payment_status === 'UNPAID')
                                            @if ($license->payment->payment_method === 'QRIS')
                                                <div class="mt-3">
                                                    {!! QrCode::size(200)->generate($license->payment->qr_string) !!}
                                                </div>
                                            @elseif ($license->payment->payment_method === 'SHOPEEPAY')
                                                <a href="{{ $license->payment->pay_url }}" target="_blank"
                                                    class="btn btn-sm btn-primary mt-2">Lanjutkan Pembayaran</a>
                                            @else
                                                <h5 class="fw-bold fs-2" id="paymentCode">{{ $license->payment->pay_code }}
                                                </h5>
                                                <button class="btn btn-sm btn-outline-primary mb-5"
                                                    onclick="copyPaymentCode()">Copy</button>
                                            @endif
                                        @endif

                                        @if ($license->payment->payment_status === 'UNPAID')
                                            <div class="mt-3">
                                                Tanggal Kedaluwarsa
                                                <h5 class="text-danger">{{ $license->payment->expired_time }}</h5>
                                            </div>
                                        @elseif ($license->payment->payment_status === 'EXPIRED')
                                            <div class="">
                                                <span class="badge bg-danger mt-4 mb-1">KEDALUWARSA</span>
                                                <h5>{{ $license->payment->payment_date }}</h5>
                                            </div>
                                        @elseif ($license->payment->payment_status === 'FAILED')
                                            <div class="">
                                                <span class="badge bg-danger mt-4 mb-1">PEMBAYARAN GAGAL</span>
                                                <h5>{{ $license->payment->payment_date }}</h5>
                                            </div>
                                        @else
                                            <div class="">
                                                <span class="badge bg-success text-dark mt-4 mb-1">SUDAH DIBAYAR</span>
                                                <h5>{{ $license->payment->payment_date }}</h5>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <i> Refresh halaman jika sudah melakukan pembayaran</i>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($license->payment)
                        <div class="col-sm-12 mt-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mt-0 pt-0">Instruksi Pembayaran</h5>

                                    @if (count($license->payment->instructions) > 0)
                                        <div class="accordion" id="accordionExample">
                                            @foreach ($license->payment->instructions as $key => $instruction)
                                                @php
                                                    $collapseId = 'collapse' . $key;
                                                    $headingId = 'heading' . $key;
                                                @endphp
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header border" id="{{ $headingId }}">
                                                        <button
                                                            class="accordion-button {{ $key !== 0 ? 'collapsed' : '' }}"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#{{ $collapseId }}"
                                                            aria-expanded="{{ $key === 0 ? 'true' : 'false' }}"
                                                            aria-controls="{{ $collapseId }}">
                                                            {{ $instruction->title }}
                                                        </button>
                                                    </h2>
                                                    <div id="{{ $collapseId }}"
                                                        class="accordion-collapse collapse border {{ $key === 0 ? 'show' : '' }}"
                                                        aria-labelledby="{{ $headingId }}"
                                                        data-bs-parent="#accordionExample">
                                                        <div class="accordion-body pt-3">
                                                            <ul>
                                                                @foreach ($instruction->steps as $step)
                                                                    <li>{!! $step !!}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function copyLicenseCode() {
            const code = document.getElementById("licenseCode").innerText;
            navigator.clipboard.writeText(code).then(() => {
                Swal.fire({
                    title: 'Sukses',
                    text: 'Kode lisensi berhasil disalin!',
                    timer: 3000,
                    icon: 'success',
                    showCancelButton: false,
                    showConfirmButton: false,
                });

            }).catch(err => {
                console.error("Gagal menyalin teks: ", err);
            });
        }

        function copyPaymentCode() {
            const code = document.getElementById("paymentCode").innerText;
            navigator.clipboard.writeText(code).then(() => {
                Swal.fire({
                    title: 'Sukses',
                    text: 'Kode pembayaran berhasil disalin!',
                    timer: 3000,
                    icon: 'success',
                    showCancelButton: false,
                    showConfirmButton: false,
                });
            }).catch(err => {
                console.error("Gagal menyalin teks: ", err);
            });
        }
    </script>
@endpush
