<?php

function menuAktif($routes)
{
    foreach ($routes as $route) {
        if (request()->routeIs($route)) {
            echo 'open active';
        } else {
            echo '';
        }
    }
}

function statusBtn()
{
    return '<div class="form-check form-switch float-end">
                <input class="form-check-input btn-check" type="checkbox" role="switch" id="flexSwitchCheckDefault" name="status" onclick="datatables.ajax.reload()" checked>
            </div>
            ';
}

function cekStatus($request)
{
    return $request ? true : false;
}

function exportBtn($tipe = [], $url = '', $filename = '')
{
    if (gettype($tipe) === 'array' && !empty($tipe)) {
        $btn = '<div class="float-end me-2">
                <button class="btn btn-sm btn-default dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bxs-file-doc"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="">';
        $btn .= (in_array('data', $tipe)) ? '<li><a class="dropdown-item btn-export" href="' . $url . '" data-ext="data" data-filename="' . $filename . '"><i class="bx bxs-spreadsheet"></i> Data</a></li>' : '';
        $btn .= (in_array('foto', $tipe)) ? '<li><a class="dropdown-item btn-export" href="' . $url . '" data-ext="foto" data-filename="' . $filename . '"><i class="bx bxs-file-image"></i> Data + Foto</a></li>' : '';
        $btn .= (in_array('pdf', $tipe)) ? '<li><a class="dropdown-item btn-export" href="' . $url . '" data-ext="pdf" data-filename="' . $filename . '"><i class="bx bxs-file-pdf"></i> PDF</a></li>' : '';
        $btn .= '</ul>
                </div>';

        return $btn;
    } else if (gettype($tipe) === 'string') {
        $btn = '<div class="float-end me-2">
                    <a class="btn btn-sm btn-default btn-export" href="' . $url . '" data-ext="data" data-filename="' . $filename . '"><i class="bx bxs-file-doc float-start"></i> Export</a>';
        $btn .= '</div>';

        return $btn;
    }
}

function pecahTanggal($tanggal)
{
    if (str_contains($tanggal, 'to')) {
        $pecah = explode(' ', $tanggal);
        $tmulai = trim($pecah[0]);
        $tkahir = trim($pecah[2]);
    } else {
        $tmulai = $tanggal;
        $tkahir = $tanggal;
    }

    return [$tmulai, $tkahir];
}

function pecahEmail($email)
{
    $explode = explode('@', $email);
    return $explode[0];
}

function genderTable($id)
{
    return $id ? '<span class="badge bg-success rounded-pill text-dark">Laki-laki</span>' : '<span class="badge bg-danger rounded-pill">Perempuan</span>';
}
