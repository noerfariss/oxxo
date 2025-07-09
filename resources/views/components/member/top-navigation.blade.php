<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ menuAktif(['member.edit']) }}" aria-current="page"
            href="{{ route('member.edit', ['member' => $member]) }}">Customer</a>
    </li>
    @if ($member->is_member)
        <li class="nav-item">
            <a class="nav-link  {{ menuAktif(['deposit.index']) }}" aria-current="page"
                href="{{ route('deposit.index', ['member' => $member]) }}">Deposit</a>
        </li>
    @endif
</ul>
