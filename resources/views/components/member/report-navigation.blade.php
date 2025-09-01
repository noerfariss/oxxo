<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('member.index') ? 'active' : '' }}" aria-current="page"
            href="{{ route('member.index') }}">Customers</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('member.report.index') ? 'active' : '' }}" aria-current="page"
            href="{{ route('member.report.index') }}">Report Saldo</a>
    </li>
</ul>
