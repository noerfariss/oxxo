<ul class="nav nav-pills flex-row align-items-center mb-3">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}"
            href="{{ route('member.dashboard') }}"><i class="bx bx-user me-1"></i>
            Home</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('member.profile') ? 'active' : '' }}"
            href="{{ route('member.profile') }}"><i class="bx bx-user me-1"></i>
            Profil</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('member.profile.password') ? 'active' : '' }}"
            href="{{ route('member.profile.password') }}"><i class="bx bx-bell me-1"></i>
            Password</a>
    </li>
    <li class="nav-item">
        <a class="nav-link
        {{ request()->routeIs('member.license.index') ||
        request()->routeIs('member.license.create') ||
        request()->routeIs('member.license.show')
            ? 'active'
            : '' }}"
            href="{{ route('member.license.index') }}"><i class="bx bx-link-alt me-1"></i>
            Deposit</a>
    </li>
</ul>
