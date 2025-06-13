<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ menuAktif(['member.index']) }}" aria-current="page"
            href="{{ route('member.index') }}">Karyawan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ menuAktif(['position.index']) }}" aria-current="page"
            href="{{ route('position.index') }}">Jabatan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ menuAktif(['division.index']) }}" aria-current="page"
            href="{{ route('division.index') }}">Divisi</a>
    </li>
</ul>
