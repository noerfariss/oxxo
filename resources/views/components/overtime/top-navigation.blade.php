<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ menuAktif(['overtime.index']) }}" aria-current="page"
            href="{{ route('overtime.index') }}">Lembur</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ menuAktif(['overtime.setting']) }}" aria-current="page"
            href="{{ route('overtime.setting') }}">Pegaturan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ menuAktif(['overtime.report']) }}" aria-current="page"
            href="{{ route('overtime.report') }}">Laporan</a>
    </li>
</ul>
