@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-md-7">
                <div class="card mb-4">
                    <h5 class="card-header">Pengaturan</h5>
                    <div class="card-body">
                        @if (session()->has('pesan'))
                            {!! session('pesan') !!}
                        @endif

                        <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data"
                            id="my-form">
                            @csrf
                            @method('PATCH')

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label" for="nama">Nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" value="{{ $setting->name }}">
                                </div>

                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label" for="nama">Alamat</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="address"
                                        value="{{ $setting->address }}">
                                </div>
                            </div>


                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label" for="nama">Provinsi</label>
                                <div class="col-sm-9">
                                    <select name="state" id="" class="form-control state-select"
                                        data-ajax--url="{{ route('drop-state') }}">
                                        @if ($setting->city_id)
                                            <option value="{{ $setting->city?->state_id }}" selected>
                                                {{ $setting->city?->state->name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label" for="nama">Kota</label>
                                <div class="col-sm-9">
                                    <select name="city_id" id="" class="form-control city-select"
                                        data-ajax--url="{{ route('drop-city') }}">
                                        @if ($setting->city_id)
                                            <option value="{{ $setting->city_id }}" selected>
                                                {{ $setting->city?->name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label" for="nama">phone</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="phone" value="{{ $setting->phone }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label" for="nama">Email</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="email"
                                        value="{{ $setting->email }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label" for="nama">Timezone</label>
                                <div class="col-sm-9">
                                    <select name="timezone" id="timezone" class="form-control select2">
                                        <option value="Asia/Jakarta"
                                            {{ $setting->timezone === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta
                                        </option>
                                        <option value="Asia/Makassar"
                                            {{ $setting->timezone === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar
                                        </option>
                                        <option value="Asia/Jayapura"
                                            {{ $setting->timezone === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label" for="nama">Durasi pengambilan</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="setting"
                                        value="{{ $setting->setting }}">
                                </div>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <div class="col-md-5">
                <div class="card mb-4">
                    <h5 class="card-header">Ganti Logo</h5>
                    <div class="card-body">
                        <form action="{{ route('setting.logo', ['setting' => $setting]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            @include('member.layouts.uploadfoto', [
                                'fileName' => 'logo',
                                'foto' => $setting->logo,
                                'path' => 'setting',
                                'title' => 'Logo',
                            ])

                            <div class="row justify-content-end">
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <h5 class="card-header">Ganti Favicon</h5>
                    <div class="card-body">
                        <form action="{{ route('setting.logo', ['setting' => $setting]) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            @include('member.layouts.uploadfoto', [
                                'fileName' => 'favicon',
                                'foto' => $setting->favicon,
                                'path' => 'setting',
                                'title' => 'Favicon',
                            ])


                            <div class="row justify-content-end">
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
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
    {!! JsValidator::formRequest('App\Http\Requests\Setting\SettingUpdateRequest', '#my-form') !!}
@endpush
