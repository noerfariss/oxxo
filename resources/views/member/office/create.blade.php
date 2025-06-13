@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <h5 class="card-header">Tambah Outlet/Cabang</h5>
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

                        <form action="{{ route('office.store') }}" method="POST" enctype="multipart/form-data" id="my-form">
                            @csrf

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">alamat</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="address">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">kota</label>
                                <div class="col-sm-9">
                                    <select name="city_id" id="city_id" class="form-control city-select"
                                        data-ajax--url="{{ route('drop-city') }}"></select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">koordinat</label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" name="latitude" class="form-control"
                                                placeholder="Latitude">
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" name="longitude" class="form-control"
                                                placeholder="Longitude">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">outlet cabang?</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input btn-check" type="checkbox" role="switch"
                                            id="flexSwitchCheckDefault" name="is_branch">
                                    </div>
                                </div>
                            </div>


                            <div class="row mt-5">
                                <div class="col-sm-9 offset-sm-3">
                                    <a href="{{ route('office.index') }}" class="btn btn-link btn-sm">Kembali</a>
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
    {!! JsValidator::formRequest('App\Http\Requests\Office\OfficeCreateRequest', '#my-form') !!}
@endpush
