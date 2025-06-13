@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <h5 class="card-header">Edit Kios</h5>
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

                        <form action="{{ route('kios.update', ['kios' => $kios]) }}" method="POST"
                            enctype="multipart/form-data" id="my-form">
                            @csrf
                            @method('PATCH')

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" value="{{ $kios->name }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">alamat</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="address" value="{{ $kios->address }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">kota</label>
                                <div class="col-sm-9">
                                    <select name="city_id" id="city_id" class="form-control city-select"
                                        data-ajax--url="{{ route('drop-city') }}">
                                        @if ($kios->city_id)
                                            <option value="{{ $kios->city_id }}">{{ $kios->city->name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">koordinat</label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" name="latitude" class="form-control"
                                                placeholder="Latitude" value="{{ $kios->latitude }}">
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" name="longitude" class="form-control"
                                                placeholder="Longitude" value="{{ $kios->longitude }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Outlet/Cabang</label>
                                <div class="col-sm-9">
                                    <section id="box-modal-selected-modaloutlet" class="mb-2 mt-1"></section>

                                    <button type="button" onclick="showModalOutlet()"
                                        class="btn btn-sm btn-outline-primary">Pilih Outlet</button>

                                    {{-- add kios --}}
                                    <x-modal-data-table id="modaloutlet" title="Daftar Outlet/Cabang" function="showModalOutlet"
                                        route="{{ route('kios.outletlist') }}" :initialdata="$kios->office" :headers="['', 'kios']"
                                        :columns="['checkbox', 'namefull']" isMultiple="false" />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">status</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input btn-check" type="checkbox" role="switch"
                                            id="flexSwitchCheckDefault" name="status"
                                            {{ $kios->status ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-9 offset-sm-3">
                                    <a href="{{ route('kios.index') }}" class="btn btn-link btn-sm">Kembali</a>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /Account -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\Kios\KiosUpdateRequest', '#my-form') !!}
@endpush
