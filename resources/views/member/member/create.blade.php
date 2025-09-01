@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <form action="{{ route('member.store') }}" method="POST" enctype="multipart/form-data" id="my-form">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <h5 class="card-header">Tambah Customer</h5>
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

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Outlet</label>
                                <div class="col-sm-9">
                                    <select name="office_id" id="office_id" class="form-control office-select"
                                        data-ajax--url="{{ route('drop-office') }}"></select>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Kios</label>
                                <div class="col-sm-9">
                                    <select name="kios_id" id="kios_id" class="form-control kios-select" disabled
                                        data-ajax--url="{{ route('drop-kios') }}"></select>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name">
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">whatsapp</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="phone">
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">tanggal lahir</label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" name="born">
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Alamat</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="address">
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">kota</label>
                                <div class="col-sm-9">
                                    <select name="city_id" id="city_id" class="form-control city-select"
                                        data-ajax--url="{{ route('drop-city') }}"></select>
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label for="gender" class="col-form-label col-sm-3">panggilan</label>
                                <div class="col-sm-9">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="pria"
                                            value="0" />
                                        <label class="form-check-label" for="pria">
                                            Bapak
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="wanita"
                                            value="1" />
                                        <label class="form-check-label" for="wanita">
                                            Ibu
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">member</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input btn-check" type="checkbox" role="switch"
                                            id="flexSwitchCheckDefault" name="is_member" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-9 offset-sm-3">
                                    <a href="{{ route('member.index') }}" class="btn btn-link btn-sm">Kembali</a>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\Member\MemberCreateRequest', '#my-form') !!}
@endpush
