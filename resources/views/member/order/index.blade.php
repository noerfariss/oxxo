@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <h5 class="card-header">data order</h5>

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

                <div class="row">
                    <div class="col-sm-3 mt-2">
                        <input type="text" id="cari" class="form-control" placeholder="Cari...">
                    </div>
                    <div class="col-sm-1 mt-2">

                    </div>
                </div>
            </div>

            <table class="table table-hover display nowrap noscroll mb-4" id="datatable">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>customer</th>
                        <th>kios</th>
                        <th>items</th>
                        <th>grand total</th>
                        <th>pembayaran</th>
                        <th>dibuat</th>

                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('member.layouts.modalDetailTable', [
        'size' => 'lg',
    ])
@endsection

@push('script')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

    <script>
        var datatables = $('#datatable').DataTable({
            scrollY: false,
            scrollX: true,
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: false,
            pageLength: 10,
            bDestroy: true,
            responsive: true,
            order: [
                [5, 'desc']
            ],
            ajax: {
                url: "{{ route('order.ajax') }}",
                type: "POST",
                data: function(d) {
                    d._token = $("input[name=_token]").val();
                    d.cari = $('#cari').val();
                },
            },
            columns: [{
                    data: 'numberid'
                },
                {
                    data: 'membertext',
                    render: function(data){
                        return data.name
                    }
                },
                {
                    data: 'kiostext',
                    render: function(data, type, row) {
                        return data.name
                    }
                },
                {
                    data: 'product_count',
                },
                {
                    data: 'grandtotal',
                },
                {
                    data: 'payment',
                },
                {
                    data: 'created_at'
                },
            ]
        });

        $('#cari').keyup(function() {
            datatables.search($('#cari').val()).draw();
        });

        $('#datatable tbody').on('click', 'tr td', function() {
            const data = datatables.row(this).data();
            $('#modalDetailTable').modal('show');
            $('#modalDetailTableLabel').text('Data Order');

            console.log(data);


            let dataTable = `
            <table class="table table-sm table-hover">
                <tbody>
                    <tr>
                        <td class="col-form-label">Bill ID</td>
                        <td>:</td>
                        <td>${data.numberid}</td>
                    </tr>
                     <tr>
                        <td class="col-form-label">customer</td>
                        <td>:</td>
                        <td>${data.membertext.name}</td>
                    </tr>
                    <tr>
                        <td class="col-form-label">kios</td>
                        <td>:</td>
                        <td>${data.kiostext.name}</td>
                    </tr>
                    <tr>
                        <td class="col-form-label">items</td>
                        <td>:</td>
                        <td>${data.product_id.length}</td>
                    </tr>
                    <tr>
                        <td class="col-form-label">pembayaran</td>
                        <td>:</td>
                        <td>${data.payment}</td>
                    </tr>
                    <tr>
                        <td class="col-form-label">dibuat</td>
                        <td>:</td>
                        <td>${data.created_at}</td>
                    </tr>
                </tbody>
            </table>

            <hr>
            <h6 class="mb-2">Detail Produk:</h6>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Attribute</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
        `;

            // Loop semua produk
            data.products.forEach((item) => {
                dataTable += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.category}</td>
                        <td>${item.attribute}</td>
                        <td class="text-end">Rp ${parseInt(item.price).toLocaleString('id-ID')}</td>
                        <td>${item.quantity}</td>
                        <td class="text-end">Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
                        <td>${item.noted ?? '-'}</td>
                    </tr>
                `;
            });

            // Tutup tabel
            dataTable += `
                    <tr>
                        <td colspan="3"></td>
                        <td>Subtotal</td>
                        <td>${data.qtytotal}</td>
                        <td class="text-end">${data.subtotal}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td>Diskon</td>
                        <td class="text-end">${data.discount ? data.discount+'%' : ''}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td>Grandtotal</td>
                        <td></td>
                        <td class="text-end fw-bold">${data.grandtotal}</td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            `;

            // dataTable += data.aksi;

            $('#modalDetailTableBody').html(dataTable);
        });
    </script>
@endpush
