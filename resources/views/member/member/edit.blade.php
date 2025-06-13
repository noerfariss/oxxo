@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <h5 class="card-header">Edit pegawai</h5>
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

                        <form action="{{ route('member.update', ['member' => $member]) }}" method="POST"
                            enctype="multipart/form-data" id="my-form">
                            @csrf
                            @method('PATCH')

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Kantor</label>
                                <div class="col-sm-9">
                                    <select name="office_id" id="office_id" class="form-control office-select"
                                        data-ajax--url="{{ route('drop-office') }}">
                                        @if ($member->office_id)
                                            <option value="{{ $member->office_id }}" selected>
                                                {{ $member->office->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">divisi</label>
                                <div class="col-sm-9">
                                    <select name="division_id" id="division_id" class="form-control division-select"
                                        data-ajax--url="{{ route('drop-division') }}">
                                        @if ($member->division_id)
                                            <option value="{{ $member->division_id }}" selected>
                                                {{ $member->division->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">jabatan</label>
                                <div class="col-sm-9">
                                    <select name="position_id" id="position_id" class="form-control position-select"
                                        data-ajax--url="{{ route('drop-position') }}">
                                        @if ($member->position_id)
                                            <option value="{{ $member->position_id }}" selected>
                                                {{ $member->position->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" value="{{ $member->name }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">nik</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nik" value="{{ $member->nik }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="email" value="{{ $member->email }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">phone</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="phone" value="{{ $member->phone }}">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="gender" class="col-form-label col-sm-3">jenis kelamin</label>
                                <div class="col-sm-9">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="pria"
                                            {{ !$member->gender ? 'checked' : '' }} value="0" />
                                        <label class="form-check-label" for="pria">
                                            Laki-laki
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="wanita"
                                            {{ $member->gender ? 'checked' : '' }} value="1" />
                                        <label class="form-check-label" for="wanita">
                                            Perempuan
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">status</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input btn-check" type="checkbox" role="switch"
                                            id="flexSwitchCheckDefault" name="status"
                                            {{ $member->status ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-9 offset-sm-3">
                                    <a href="{{ route('member.index') }}" class="btn btn-link btn-sm">Kembali</a>
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
    {!! JsValidator::formRequest('App\Http\Requests\Member\MemberUpdateRequest', '#my-form') !!}
@endpush
