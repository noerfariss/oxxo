<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('order.index') ? 'active' : '' }}" aria-current="page"
            href="{{ route('order.index') }}">Drop Off (Barang Masuk)</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('order.done') ? 'active' : '' }}" aria-current="page"
            href="{{ route('order.done') }}">Done (Barang Selesai)</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('order.out') ? 'active' : '' }}" aria-current="page"
            href="{{ route('order.out') }}">Pickup (Barang Keluar)</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('order.report.index') ? 'active' : '' }}" aria-current="page"
            href="{{ route('order.report.index') }}">Report</a>
    </li>

</ul>
