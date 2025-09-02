@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-sm-12">
                <x-order.top-navigation />
            </div>
        </div>
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
                        <input type="text" class="form-control" id="tanggal">
                    </div>
                    <div class="col-sm-3 mt-2">
                        <input type="text" id="cari" class="form-control" placeholder="Cari...">
                    </div>
                    <div class="col-sm-2 mt-2">
                        <button id="btnReport" class="btn btn-success btn-sm">Report</button>
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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $('#tanggal').flatpickr({
            mode: 'range',
            defaultDate: ["{{ date('Y-m-01') }}", "{{ date('Y-m-d') }}"],
            maxDate: '{{ date('Y-m-d') }}',
            onClose: function() {
                $('#tanggal').blur();
                datatables.ajax.reload();
            }
        });
    </script>

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
                    d.type = 'new';
                    d.dates = $('#tanggal').val();
                },
            },
            columns: [{
                    data: 'numberid'
                },
                {
                    data: 'memberstring',
                    // render: function(data) {
                    //     return data.name
                    // }
                },
                {
                    data: 'kiostext',
                    render: function(data, type, row) {
                        return data.name
                    }
                },
                {
                    data: 'qtytotal',
                },
                {
                    data: 'grandtotal',
                },
                {
                    data: 'payment_method',
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
                        <td class="col-form-label">phone</td>
                        <td>:</td>
                        <td>${data.membertext.phone}</td>
                    </tr>
                    <tr>
                        <td class="col-form-label">alamat</td>
                        <td>:</td>
                        <td>${data.membertext.address}</td>
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
                        <td>${data.payment_method.toUpperCase()}</td>
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
                        <td class="text-end">${data.subtotaltext}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td>Diskon ${data.discount_if_persen}</td>
                        <td class="text-end"></td>
                        <td class="text-end"> - ${data.discount_nominal}</td>
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

                 <div class="text-end mt-3 d-flex justify-content-end gap-2">
                    <form id="prosesBarangDone" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-sm btn-primary">Done</button>
                    </form>

                    <a id="btnPrintInvoice" href="#" target="_blank" class="btn btn-sm btn-secondary">Print Invoice</a>
                </div>
            `;


            $('#modalDetailTableBody').html(dataTable);

            $('#btnPrintInvoice').attr('href', `/auth/order/${data.id}/print`);

            $('#prosesBarangDone').attr('action', `{{ url('auth/order') }}/${data.id}/done`);
            $(document).on('submit', '#prosesBarangDone', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: "Ingin memproses data ini?",
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: "Ya Lanjutkan!",
                    denyButtonText: `Batal`
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $(this).attr('action'),
                            type: 'POST',
                            data: $(this).serialize(),
                            success: function(res) {
                                Swal.fire({
                                    title: "Data berhasil diproses",
                                    icon: "success",
                                    draggable: true
                                });

                                $('#modalDetailTable').modal('hide');
                                datatables.ajax
                            .reload(); // reload tabel agar status terbaru muncul
                            },
                            error: function(err) {
                                alert('Gagal memproses order!');
                                console.error(err);
                            }
                        });
                    }
                });
            });

        });
    </script>

    <script>
        $('#btnReport').click(function() {
            let tanggal = $('#tanggal').val();
            if (!tanggal) {
                alert('Silakan pilih tanggal terlebih dahulu.');
                return;
            }
            // Redirect ke route report dengan parameter tanggal (GET)
            window.open("{{ route('order.report') }}?tanggal=" + encodeURIComponent(tanggal) + "&type=new",
                '_blank');
        });
    </script>
@endpush
