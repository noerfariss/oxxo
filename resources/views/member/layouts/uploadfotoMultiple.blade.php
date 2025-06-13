    <div class="row mb-3">
        <label class="col-sm-3 col-form-label" for="box-foto">{{ isset($title) ? $title : 'Foto' }}</label>
        <div class="col-sm-9">
            <div class="button-wrapper">
                <button type="button" class="account-file-input btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#modalUploadFoto{{ $multi }}">
                    <span class="d-none d-sm-block">Ganti {{ isset($title) ? $title : 'Foto' }}</span>
                    <i class="bx bx-upload d-block d-sm-none"></i>
                </button>

                <input type="hidden" name="{{ isset($fileName) ? $fileName : 'foto' }}" id="foto{{ $multi }}"
                    value="">

                <div>
                    <small class="text-muted mb-0">Format : JPG, GIF, PNG. Maksimal ukuran 7.000 Kb</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label" for="box-foto"></label>
        <div class="col-sm-9">
            <div id="box-foto{{ $multi }}">
                @isset($foto)
                    @if ($foto)
                        <img src="{{ url('/storage' . '/' . $foto) }}" class="rounded" style="width:40%">
                    @endif
                @endisset
            </div>

            <button type="button" class="btn btn-xs btn-outline-danger mt-2 mb-2" id="hapusFotoForm{{ $multi }}"
                onclick="hapusFoto{{ $multi }}('{{ $multi }}')"><i class='bx bx-x'></i> Hapus
                Foto</button>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="modalUploadFoto{{ $multi }}" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="modalUploadFoto{{ $multi }}Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUploadFoto{{ $multi }}Label">Unggah
                        {{ isset($title) ? $title : 'Foto' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="notif"></span>

                    <div class="dropzone" id="mydropzone{{ $multi }}"></div>
                    <input type="hidden" name="path" value="{{ $path }}">
                    @csrf
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm btn-simpan"
                        onclick="simpanFoto()">Tambahkan</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

    <script>
        $('#foto{{ $multi }}').val('{{ $foto }}');

        if ($('#foto{{ $multi }}').val() == '') {
            $('#hapusFotoForm{{ $multi }}').hide();
        }
        // If you use jQuery, you can use the jQuery plugin Dropzone ships with:
        Dropzone.autoDiscover = false;

        // Dropzone class:
        var myDropzone = new Dropzone("div#mydropzone{{ $multi }}", {
            url: "{{ route('master.foto') }}",
            maxFilesize: 7000,
            acceptedFiles: ".jpeg,.jpg,.png",
            method: 'post',
            createImageThumbnails: true,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val(),
            },
            params: {
                path: $('input[name="path"]').val(),
            },
            init: function() {
                this.on("addedfile", file => {
                    $('.btn-simpan').attr('disabled', 'disabled').text('Loading...');
                });
            },
            success: function(file, response) {
                $('.btn-simpan').removeAttr('disabled').text('Tambahkan');
                const foto = response.file;
                $('.modal-body #notif').html(`<div class="alert alert-success">Foto berhasil diunggah</div>`);
                $('#foto{{ $multi }}').val(foto);
            },
            error: function(file, response) {
                $('.btn-simpan').removeAttr('disabled').text('Tambahkan');
                const pesan = response.message;
                $('.modal-body #notif').html(`<div class="alert alert-danger">${pesan}</div>`);
            }
        });


        function simpanFoto() {
            let foto = $('#foto{{ $multi }}').val();

            if (foto === '' || foto === null) {
                $('#notif').html(`<div class="alert alert-danger">Tidak dapat menambahkan foto</div>`);
            } else {
                $('#modalUploadFoto{{ $multi }}').modal('hide');
                $('#box-foto{{ $multi }}').html(
                    `<img src="{{ url('/storage') }}/${foto}" class="rounded img-fluid">`);
            }
        }

        function hapusFoto{{ $multi }}('{{ $multi }}') {
            Swal.fire({
                title: 'Ingin menghapus foto?',
                // text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1A237E',
                cancelButtonColor: '#B71C1C',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#foto{{ $multi }}').val('');
                    $('#box-foto{{ $multi }}').html('');
                    $('#hapusFotoForm{{ $multi }}').hide();
                }
            });
        }
    </script>
