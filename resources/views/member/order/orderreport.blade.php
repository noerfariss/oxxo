@extends('member.layouts.layout')

@section('konten')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-sm-12">
                <x-order.top-navigation />
            </div>
        </div>
        <div class="card mb-4">
            <h5 class="card-header">Report</h5>

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
                        <div class="mt-2">
                            <label class="form-label fw-bold">Order Type</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ordertype" value="new" checked
                                    id="new">
                                <label class="form-check-label" for="new">
                                    Drop Off (Barang Masuk)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ordertype" value="done"
                                    id="done">
                                <label class="form-check-label" for="done">
                                    Done (Barang Selesai)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ordertype" value="out" disabled
                                    id="out">
                                <label class="form-check-label" for="out">
                                    Pickup (Barang Keluar)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 mt-2">
                        <input type="text" id="cari" class="form-control" placeholder="Cari...">
                        <div class="mt-2">
                            <label class="form-label fw-bold">Payment</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="paymenttype" value="outstanding"
                                    checked id="outstanding">
                                <label class="form-check-label" for="outstanding">
                                    Outstanding
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="paymenttype" value="ppc" checked
                                    id="ppc">
                                <label class="form-check-label" for="ppc">
                                    PPC
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="paymenttype" value="cash" checked
                                    id="cash">
                                <label class="form-check-label" for="cash">
                                    Cash
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="paymenttype" value="card" checked
                                    id="card">
                                <label class="form-check-label" for="card">
                                    Card
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 mt-2">
                        <button onclick="datatables.ajax.reload()" class="btn btn-primary btn-sm">GO</button>
                        <button id="btnReport" class="btn btn-success btn-sm">Report</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <label class="form-label fw-bold">Label Report</label>
                        <div class="row">
                            <div class="col-sm-6">
                                <select class="form-control" name="labelreport" id="labelreport">
                                    <option value="">-- Custom ---</option>
                                    <option value="new" data-text="Drop Off (Barang Masuk)">Drop Off (Barang Masuk)
                                    </option>
                                    <option value="done" data-text="Done (Barang Selesai)">Done (Barang Selesai)</option>
                                    <option value="out" data-text="Pickup (Barang Selesai)">Pickup (Barang Selesai)
                                    </option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="labelreporttext" id="labelreporttext"
                                    placeholder="Buat label report">
                            </div>
                        </div>

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
            },
        });
    </script>

    <script>
        $('#labelreport').change(function() {
            let selected = $(this).find(':selected');
            let val = selected.val();
            let text = selected.data('text');

            if (val === "") {
                // Custom → kosongkan input & bisa diisi manual
                $('#labelreporttext').val('').prop('readonly', false);
            } else {
                // Isi otomatis dari data-text & readonly
                $('#labelreporttext').val(text).prop('readonly', true);
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('input[name="ordertype"]').change(function() {
                let isPickup = $('#out').is(':checked');
                let isNewOrDone = $('#new').is(':checked') || $('#done').is(':checked');

                if (isPickup) {
                    $('#new, #done').prop('disabled', true);
                    $('#outstanding').prop('disabled', true).removeAttr('checked');
                } else if (isNewOrDone) {
                    $('#out').prop('disabled', true);
                    $('#outstanding').prop('disabled', false).prop('checked', true);
                } else {
                    // reset kalau semua unchecked
                    $('#new, #done, #out').prop('disabled', false);
                    $('#outstanding').prop('disabled', false).prop('checked', true);
                }
            });
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
                url: "{{ route('order.report.ajax') }}",
                type: "POST",
                data: function(d) {
                    d._token = $("input[name=_token]").val();

                    // text input
                    d.cari = $('#cari').val();
                    d.dates = $('#tanggal').val();

                    // ordertype (checkbox)
                    d.ordertype = $('input[name="ordertype"]:checked').map(function() {
                        return this.value;
                    }).get();

                    // paymenttype (checkbox)
                    d.paymenttype = $('input[name="paymenttype"]:checked').map(function() {
                        return this.value;
                    }).get();

                    // label report (select + text)
                    d.labelreport = $('#labelreport').val();
                    d.labelreporttext = $('#labelreporttext').val();
                },
            },
            columns: [{
                    data: 'idstatus'
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
            $('#modalDetailTableLabel').text('Report');

            let dataTable = `
            <table class="table table-sm table-hover">
                <tbody>
                    <tr>
                        <td class="col-form-label">Bill ID</td>
                        <td>:</td>
                        <td>${data.idstatus}</td>
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
            `;

            $('#modalDetailTableBody').html(dataTable);
        });
    </script>

    <script>
        $('#btnReport').click(function() {
            let tanggal = $('#tanggal').val();
            if (!tanggal) {
                alert('Silakan pilih tanggal terlebih dahulu.');
                return;
            }

            // Ambil semua filter
            let ordertype = $('input[name="ordertype"]:checked').map(function() {
                return this.value;
            }).get();

            let paymenttype = $('input[name="paymenttype"]:checked').map(function() {
                return this.value;
            }).get();

            let labelreport = $('#labelreport').val();
            let labelreporttext = $('#labelreporttext').val();
            let cari = $('#cari').val();

            // Susun URL
            let url = "{{ route('order.report') }}" +
                "?tanggal=" + encodeURIComponent(tanggal) +
                "&ordertype=" + encodeURIComponent(JSON.stringify(ordertype)) +
                "&paymenttype=" + encodeURIComponent(JSON.stringify(paymenttype)) +
                "&labelreport=" + encodeURIComponent(labelreport) +
                "&labelreporttext=" + encodeURIComponent(labelreporttext) +
                "&cari=" + encodeURIComponent(cari);

            window.open(url, '_blank');
        });
    </script>
@endpush
