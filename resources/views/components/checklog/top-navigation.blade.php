<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ menuAktif(['checklog.index']) }}" aria-current="page"
            href="{{ route('checklog.index') }}">Checklog</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ menuAktif(['attendance.index']) }}" aria-current="page"
            href="{{ route('attendance.index') }}">Izin/Sakit</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ menuAktif(['attendance.report']) }}" aria-current="page"
            href="{{ route('attendance.report') }}">Laporan</a>
    </li>
</ul>
