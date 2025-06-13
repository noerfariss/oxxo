@props([
    'id', // ID unik modal, misal: 'modalKios'
    'title' => 'Data',
    'function' => 'openModal',
    'route' => '', // route untuk AJAX DataTable
    'initialdata' => [],
    'headers' => [],
    'columns' => [], // contoh: ['checkbox', 'namefull']
    'isMultiple' => true,
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="{{ $id }}Label">{{ $title }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="search-{{ $id }}"
                            placeholder="Cari...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover" id="datatable-{{ $id }}">
                        <thead>
                            <tr>
                                @foreach ($headers as $col)
                                    <th>{{ $col ? ucfirst($col) : $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm"
                    id="btnSave-{{ $id }}">Tambahkan</button>
            </div>
        </div>
    </div>
</div>

@push('script')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

    <style>
        table tr {
            cursor: pointer;
        }
    </style>

    <script>
        let typeChecked = {{ $isMultiple }} ? 'checkbox' : 'radio';

        const initialSelected = {!! json_encode($initialdata) !!}
        const box = $('#box-modal-selected-{{ $id }}');


        if (typeChecked == 'checkbox') {
            if (initialSelected.length > 0) {
                box.empty();

                initialSelected.forEach(function(item) {
                    box.append(`
                        <div class="list-group d-flex mb-2">
                            <a href="#" class="list-group-item list-group-item-action delete-modal-selected-{{ $id }}" aria-current="true">
                                <div class="d-flex justify-content-between align-items-center">
                                    ${item.name}
                                    <input type="hidden" name="{{ $id }}[]" value="${item.id}">
                                    <i class='bx bx-trash'></i>
                                </div>
                            </a>
                        </div>
                    `);
                });
            } else {
                box.html(`<span class="badge bg-danger mb-2">Data belum ditambahkan</span>`);
            }

        } else {
            if (initialSelected.length == 0) {
                box.html(`<span class="badge bg-danger mb-2">Data belum ditambahkan</span>`);

            } else {
                box.html(`
                    <div class="list-group d-flex mb-2">
                        <a href="#" class="list-group-item list-group-item-action delete-modal-selected-{{ $id }}" aria-current="true">
                            <div class="d-flex justify-content-between align-items-center">
                                ${initialSelected.name}
                                <input type="hidden" name="{{ $id }}[]" value="${initialSelected.id}">
                                <i class='bx bx-trash'></i>
                            </div>
                        </a>
                    </div>
                `);
            }
        }

        function {{ $function }}() {
            $('#{{ $id }}').modal('show');

            let datatable = $('#datatable-{{ $id }}').DataTable({
                scrollY: false,
                scrollX: true,
                processing: true,
                serverSide: true,
                searching: false,
                lengthChange: false,
                pageLength: 10,
                bDestroy: true,
                info: false,
                responsive: true,
                ajax: {
                    url: @json($route),
                    type: "GET",
                    data: function(d) {
                        d.search = $('#search-{{ $id }}').val();
                    }
                },
                columnDefs: [{
                    width: "10%",
                    targets: 0
                }],
                columns: [
                    @foreach ($columns as $col)
                        {
                            data: '{{ $col }}'
                        },
                    @endforeach
                ]
            });

            // Toggle checkbox on row click
            $('#datatable-{{ $id }} tbody').off('click').on('click', 'tr', function(e) {
                // Hindari toggle saat klik langsung di checkbox
                if (!$(e.target).is(`input[type="${typeChecked}"]`)) {
                    const checkbox = $(this).find(`input[type="${typeChecked}"], input[type="${typeChecked}"]`);
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
            });

            // Handle klik tombol simpan
            $('#btnSave-{{ $id }}').off('click').on('click', function() {
                let selected = [];

                // Ambil semua checkbox yang tercentang
                $(`#datatable-{{ $id }} tbody input[type="${typeChecked}"]:checked`).each(function() {
                    const id = $(this).val();
                    const label = $(this).data('label');

                    selected.push({
                        id: id,
                        label: label
                    });
                });

                let box = $('#box-modal-selected-{{ $id }}');

                if (typeChecked === "radio") {
                    box.empty(); // Hanya satu pilihan, hapus dulu
                }

                // Ambil ID yang sudah ada di DOM
                let existingIds = [];
                box.find('input[name="{{ $id }}[]"]').each(function() {
                    existingIds.push($(this).val());
                });

                if (selected.length === 0 && existingIds.length === 0) {
                    box.html(`<span class="badge bg-danger">Kios belum ditambahkan</span>`);
                } else {
                    selected.forEach(item => {
                        if (!existingIds.includes(item.id)) {
                            box.append(`
                                <div class="list-group d-flex mb-2">
                                    <a href="#" class="list-group-item list-group-item-action delete-modal-selected-{{ $id }}" aria-current="true">
                                        <div class="d-flex justify-content-between align-items-center">
                                            ${item.label}
                                            <input type="hidden" name="{{ $id }}[]" value="${item.id}">
                                            <i class='bx bx-trash'></i>
                                        </div>
                                    </a>
                                </div>
                            `);
                        }
                    });

                    // Hapus badge merah jika masih ada
                    box.find('.badge.bg-danger').remove();
                }

                // Simpan semua data ke hidden input dalam format JSON
                let result = [];
                box.find('input[name="{{ $id }}[]"]').each(function() {
                    result.push({
                        id: $(this).val(),
                        label: $(this).closest('.list-group-item').text().trim()
                    });
                });

                $('#modal-selected-{{ $id }}').val(JSON.stringify(result));

                $('#{{ $id }}').modal('hide');
            });


            $('#search-{{ $id }}').keyup(function() {
                datatable.search($(this).val()).draw();
            });
        }

        $(document).on('click', '.delete-modal-selected-{{ $id }}', function(e) {
            e.preventDefault();
            $(this).closest('.list-group').remove();

            // Update hidden input setelah penghapusan
            let updated = [];
            $('#box-modal-selected-{{ $id }}').find('input[name="{{ $id }}[]"]').each(
                function() {
                    updated.push({
                        id: $(this).val(),
                        label: $(this).closest('.list-group-item').text().trim()
                    });
                });

            $('#modal-selected-{{ $id }}').val(JSON.stringify(updated));

            // Jika kosong, tampilkan badge merah
            if (updated.length === 0) {
                $('#box-modal-selected-{{ $id }}').html(
                    `<span class="badge bg-danger">Data belum ditambahkan</span>`);
            }
        });
    </script>
@endpush
