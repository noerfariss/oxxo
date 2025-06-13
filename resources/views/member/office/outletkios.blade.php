@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-sm-12">
                <x-office.top-navigation :office="$office" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <form action="{{ route('office.outletkios.update', ['office' => $office]) }}" method="POST"
                        enctype="multipart/form-data" id="my-form">
                        @csrf
                        @method('PATCH')

                        <h5 class="card-header">Kios</h5>
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
                                <label class="col-sm-3 col-form-label">outlet</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" value="{{ $office->name }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">alamat</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" value="{{ $office->address }}"
                                        disabled>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Kios</label>
                                <div class="col-sm-9">
                                    <hr>

                                    <section id="box-modal-selected-modalkios"></section>

                                    <div class="mt-5">
                                        <button type="button" onclick="showModalKios()"
                                            class="btn btn-sm btn-outline-primary">Tambah kios</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-3 offset-sm-3">
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- /Account -->
                </div>

            </div>
        </div>
    </div>

    {{-- add kios --}}
    <x-modal-data-table
    id="modalkios"
    title="Daftar Kios"
    function="showModalKios"
    route="{{ route('office.outletkios.list') }}"
    :initialdata="$office->kios"
    :headers="['', 'kios']"
    :columns="['checkbox', 'namefull']"
     />

@endsection
