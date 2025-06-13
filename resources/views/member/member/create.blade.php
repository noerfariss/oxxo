@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <form action="{{ route('member.store') }}" method="POST" enctype="multipart/form-data" id="my-form">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <h5 class="card-header">Tambah Pegawai</h5>
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

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Kantor</label>
                                <div class="col-sm-9">
                                    <select name="office_id" id="office_id" class="form-control office-select"
                                        data-ajax--url="{{ route('drop-office') }}"></select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">divisi</label>
                                <div class="col-sm-9">
                                    <select name="division_id" id="division_id" class="form-control division-select"
                                        data-ajax--url="{{ route('drop-division') }}"></select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">jabatan</label>
                                <div class="col-sm-9">
                                    <select name="position_id" id="position_id" class="form-control position-select"
                                        data-ajax--url="{{ route('drop-position') }}"></select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">nik</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nik">
                                    <small>Jika kosong sistem akan membuatkan NIK secara otomatis</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="email">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">phone</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="phone">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="gender" class="col-form-label col-sm-3">jenis kelamin</label>
                                <div class="col-sm-9">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="pria"
                                            value="0" />
                                        <label class="form-check-label" for="pria">
                                            Laki-laki
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="wanita"
                                            value="1" />
                                        <label class="form-check-label" for="wanita">
                                            Perempuan
                                        </label>
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
