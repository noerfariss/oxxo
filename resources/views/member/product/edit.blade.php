@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <h5 class="card-header">Edit Produk</h5>
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

                        <form action="{{ route('product.update', ['product' => $product]) }}" method="POST"
                            enctype="multipart/form-data" id="my-form">
                            @csrf
                            @method('PATCH')

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">kategori</label>
                                <div class="col-sm-9">
                                    <select name="category_id" id="category_id" class="form-control category-select"
                                        data-ajax--url="{{ route('drop-category') }}">
                                        @if ($product->category_id)
                                            <option value="{{ $product->category_id }}" selected>
                                                {{ $product->category->name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" value="{{ $product->name }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">unit satuan</label>
                                <div class="col-sm-9">
                                    @foreach ($units as $key => $unit)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="unit"
                                                {{ $product->unit == $unit ? 'checked' : '' }} value="{{ $unit }}"
                                                id="{{ $key }}">
                                            <label class="form-check-label" for="{{ $key }}">
                                                {{ $key }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">harga</label>
                                <div class="col-sm-9">
                                    @forelse ($attribute as $data)
                                        <div class="card border mb-2">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-end align-items-center gap-3">
                                                    <div class="fw-bold">
                                                        {{ $data->name }}
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="number" class="form-control"
                                                            name="price[{{ $data->id }}]" value="{{ $data->price }}">
                                                        <small>Berikan angka 0 jika tidak ingin digunakan</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <span>Belum ada attribute untuk harga</span>
                                    @endforelse
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">status</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input btn-check" type="checkbox" role="switch"
                                            id="flexSwitchCheckDefault" name="status"
                                            {{ $product->status ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-9 offset-sm-3">
                                    <a href="{{ route('product.index') }}" class="btn btn-link btn-sm">Kembali</a>
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
    {!! JsValidator::formRequest('App\Http\Requests\Product\ProductUpdateRequest', '#my-form') !!}
@endpush
