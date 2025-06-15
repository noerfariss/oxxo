@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card mb-4">
            <h5 class="card-header">Kasir</h5>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3 mt-2">
                        <input type="text" id="cari" class="form-control" placeholder="Cari kios...">
                    </div>
                    <div class="col-sm-9 mt-2">
                    </div>
                </div>

                @if (session()->has('pesan'))
                    {!! session('pesan') !!}
                @endif
            </div>
        </div>

        <div class="row">
            @forelse ($data as $cashier)
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h4 class="fw-semibold fs-5">{{ $cashier->name }}</h4>
                            <h5 class="badge bg-primary">{{ $cashier->office->name }}</h5>
                        </div>
                        <div class="card-body py-0">
                            {{ $cashier->address }}
                            {{ $cashier->city->name }}
                        </div>
                        <div class="card-footer">
                            <a href="{{route('cashier.kios',['kios' => $cashier])}}" class="btn d-block btn-outline-primary">
                                <i class="bx bx-cable-car"></i> Kasir
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="alert alert-danger">Kios belum tersedia</p>
            @endforelse
        </div>
    </div>
@endsection
