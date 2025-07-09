<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ menuAktif(['member.edit']) }}" aria-current="page"
            href="{{ route('member.edit', ['member' => $member]) }}">Customer</a>
    </li>
    <li class="nav-item">
        <a class="nav-link " aria-current="page" href="{{ route('member.index') }}">Deposit</a>
    </li>
</ul>
