<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('order.index') ? 'active' : '' }}" aria-current="page"
            href="{{ route('order.index') }}">Data Order</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('order.out') ? 'active' : '' }}" aria-current="page"
            href="{{ route('order.out') }}">Barang Keluar</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('order.in') ? 'active' : '' }}" aria-current="page"
            href="{{ route('order.in') }}">Barang Masuk</a>
    </li>

</ul>
