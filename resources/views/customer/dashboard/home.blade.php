@extends('customer.layouts.layouts')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y mx-auto">
        <h4 class="fw-bold py-1 mb-0 text-center">
            <span class="text-muted fw-semibold">Dashboard</span>
        </h4>

        <div class="row">
            <div class="col-md-8 offset-sm-2">
                <div class="d-flex justify-content-center">
                    <x-customer.top-nav />
                </div>

                <div class="row">
                    <div class="col-md-7 col-12 mb-md-0 mb-4">
                        <div class="card">
                            <h5 class="card-header">Informasi</h5>
                            <div class="card-body">
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1 row">
                                        <div class="col-5 mb-sm-0 mb-2">
                                            <h6 class="mb-0">IP</h6>
                                        </div>
                                        <div class="col-7 text-end">
                                            <span class="text-muted">{{ request()->ip() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1 row">
                                        <div class="col-5 mb-sm-0 mb-2">
                                            <h6 class="mb-0">Cust. ID</h6>
                                        </div>
                                        <div class="col-7 text-end">
                                            <span class="text-muted">{{ Auth::guard('member')->user()->numberid }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1 row">
                                        <div class="col-5 mb-sm-0 mb-2">
                                            <h6 class="mb-0">Whatsapp</h6>
                                        </div>
                                        <div class="col-7 text-end">
                                            <span class="text-muted">{{ Auth::guard('member')->user()->whatsapp }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1 row">
                                        <div class="col-5 mb-sm-0 mb-2">
                                            <h6 class="mb-0">Nama</h6>
                                        </div>
                                        <div class="col-7 text-end">
                                            <span class="text-muted">{{ Auth::guard('member')->user()->name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1 row">
                                        <div class="col-5 mb-sm-0 mb-2">
                                            <h6 class="mb-0">Alamat</h6>
                                        </div>
                                        <div class="col-7 text-end">
                                            <span class="text-muted">{{ Auth::guard('member')->user()->address }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1 row">
                                        <div class="col-5 mb-sm-0 mb-2">
                                            <h6 class="mb-0">Kecamatan</h6>
                                        </div>
                                        <div class="col-7 text-end">
                                            <span
                                                class="text-muted">{{ Auth::guard('member')->user()->city?->name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1 row">
                                        <div class="col-5 mb-sm-0 mb-2">
                                            <h6 class="mb-0">Kota</h6>
                                        </div>
                                        <div class="col-7 text-end">
                                            <span
                                                class="text-muted">{{ Auth::guard('member')->user()->city?->name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="flex-grow-1 row">
                                        <div class="col-5 mb-sm-0 mb-2">
                                            <h6 class="mb-0">Provinsi</h6>
                                        </div>
                                        <div class="col-7 text-end">
                                            <span
                                                class="text-muted">{{ Auth::guard('member')->user()->city?->state?->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5 col-12 mb-md-0 mb-4">
                        <div class="card">
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
