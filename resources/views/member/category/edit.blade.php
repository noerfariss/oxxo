@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <h5 class="card-header">Edit Kategori</h5>
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

                        <form action="{{ route('category.update', ['category' => $category]) }}" method="POST"
                            enctype="multipart/form-data" id="my-form">
                            @csrf
                            @method('PATCH')

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" value="{{ $category->name }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">status</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input btn-check" type="checkbox" role="switch"
                                            id="flexSwitchCheckDefault" name="status"
                                            {{ $category->status ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-9 offset-sm-3">
                                    <a href="{{ route('category.index') }}" class="btn btn-link btn-sm">Kembali</a>
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
    {!! JsValidator::formRequest('App\Http\Requests\Category\CategoryUpdateRequest', '#my-form') !!}
@endpush
