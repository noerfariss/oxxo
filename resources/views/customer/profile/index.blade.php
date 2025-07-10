@extends('customer.layouts.layouts')

@section('content')
    <form action="{{ route('member.profile.process') }}" method="POST">
        @csrf
        <div class="container-xxl flex-grow-1 container-p-y mx-auto">
            <h4 class="fw-bold py-1 mb-0 text-center">
                <span class="text-muted fw-semibold">Perbarui Profil</span>
            </h4>

            <div class="row">
                <div class="col-md-8 offset-sm-2">
                    <div class="d-flex justify-content-center">
                        <x-customer.top-nav />
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-12 mb-md-0 mb-4">
                            <div class="card">
                                <h5 class="card-header">Perbarui Profil</h5>
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


                                    <p>Lengkapi form di bawah ini untuk memperbarui profil</p>
                                    <!-- Connections -->
                                    <div class="mb-3">
                                        <label for="nama" class="form-label">nama</label>
                                        <input type="text" class="form-control" id="nama" name="name"
                                            value="{{ Auth::guard('member')->user()->name }}" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="whatsapp" class="form-label">jenis kelamin</label>
                                        <select name="gender" id="gender" class="form-control">
                                            <option value="">-- Jenis Kelamin</option>
                                            <option value="0"
                                                {{ Auth::guard('member')->user()->gender == 0 ? 'selected' : '' }}>Laki-laki
                                            </option>
                                            <option value="1"
                                                {{ Auth::guard('member')->user()->gender == 1 ? 'selected' : '' }}>Perempuan
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="whatsapp" class="form-label">whatsapp</label>
                                        <input type="text" class="form-control" id="whatsapp" name="whatsapp"
                                            value="{{ Auth::guard('member')->user()->whatsapp }}" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-12 mb-md-0 mb-4">
                            <div class="card">
                                <h5 class="card-header">Alamat</h5>
                                <div class="card-body">

                                    <!-- Connections -->
                                    <div class="mb-3">
                                        <label for="address" class="form-label">alamat</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            value="{{ Auth::guard('member')->user()->address }}" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="state" class="form-label">provinsi</label>
                                        <select name="state" id="state" class="form-control state-select"
                                            data-ajax--url={{ route('drop-state') }}>
                                            @if (Auth::guard('member')->user()->city_id)
                                                <option
                                                    value="{{ Auth::guard('member')->user()->city?->state_id }}"
                                                    selected>
                                                    {{ Auth::guard('member')->user()->city?->state->name }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="city" class="form-label">kota</label>
                                        <select name="city" id="city" class="form-control city-select"
                                            data-ajax--url={{ route('drop-city') }}>
                                            @if (Auth::guard('member')->user()->city_id)
                                                <option value="{{ Auth::guard('member')->user()->city_id }}"
                                                    selected>
                                                    {{ Auth::guard('member')->user()->city->name }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>



                                    <div class="mb-3 mt-5">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
