@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">

                @include('member.profil.headerprofil')

                <div class="card mb-4">
                    <h5 class="card-header">Password</h5>

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

                        <form action="{{ route('password.store') }}" method="POST" enctype="multipart/form-data"
                            id="my-form">
                            @csrf
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="password_lama">Password Lama</label>
                                <div class="col-sm-4">
                                    <input type="password" class="form-control" name="password_lama">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="password">Password Baru</label>
                                <div class="col-sm-4">
                                    <input type="password" class="form-control" name="password">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="password">Konfirmasi Password</label>
                                <div class="col-sm-4">
                                    <input type="password" class="form-control" name="password_confirmation">
                                </div>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-sm-0">
                                    <button type="submit" class="btn btn-sm btn-primary">Perbarui</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\User\PasswordUpdateRequest', '#my-form') !!}
@endpush
