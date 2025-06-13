<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('office.edit') ? 'active' : '' }}" aria-current="page"
            href="{{ route('office.edit', ['office' => $office]) }}">Outlet</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('office.outletkios') ? 'active' : '' }}" aria-current="page"
            href="{{ route('office.outletkios', ['office' => $office]) }}">Kios</a>
    </li>

</ul>
