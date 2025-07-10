@extends('customer.layouts.layouts')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y mx-auto">
        <h4 class="fw-bold py-1 mb-0 text-center">
            <span class="text-muted fw-semibold">Password</span>
        </h4>

        <div class="row">
            <div class="col-md-6 offset-sm-3">
                <div class="d-flex justify-content-center">
                    <x-customer.top-nav />
                </div>

                <div class="row">
                    <div class="col-md-12 col-12 mb-md-0 mb-4">
                        <div class="card">
                            <h5 class="card-header">Ganti Password</h5>
                            <div class="card-body">
                                @if (session()->has('message'))
                                    {!! session('message') !!}
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

                                <form action="{{ route('member.profile.password.post') }}" method="POST">
                                    @csrf
                                    <p>Lengkapi form di bawah ini untuk mengganti password</p>
                                    <!-- Connections -->
                                    <div class="mb-3">
                                        <label for="password_lama" class="form-label">Password lama</label>
                                        <input type="password" class="form-control" id="password_lama"
                                            name="password_lama" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password baru</label>
                                        <input type="password" class="form-control" id="password" name="password" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" />
                                    </div>

                                    <div class="mb-3 mt-5">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
