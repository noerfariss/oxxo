<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('auth.index') }}" class="app-brand-link">
            <h2 class="app-brand-text demo menu-text fw-bolder ms-2">
                {{ env('APP_NAME') }}
            </h2>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <ul class="menu-inner py-1 mt-2">
        <!-- Dashboard -->
        <li class="menu-item {{ menuAktif(['auth.index']) }}">
            <a href="{{ route('auth.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-pie-chart"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        @can('CASHIER_READ')
            <li class="menu-item {{ menuAktif(['cashier.index']) }}">
                <a href="{{ route('cashier.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-cable-car"></i>
                    <div data-i18n="Analytics">Kasir</div>
                </a>
            </li>
        @endcan


        @canany(['OUTLETKIOS_READ', 'OFFICE_READ', 'PRODUCT_READ', 'CATEGORY_READ'])
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Master</span></li>
        @endcanany

        @canany(['OUTLETKIOS_READ', 'OFFICE_READ'])

            @can('MEMBER_READ')
                <li class="menu-item {{ menuAktif(['member.index', 'member.edit', 'member.create', 'deposit.index']) }}">
                    <a href="{{ route('member.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-user"></i>
                        <div data-i18n="Analytics">Customer</div>
                    </a>
                </li>
            @endcan

            <li
                class="menu-item {{ menuAktif(['office.index', 'office.create', 'office.edit', 'office.outletkios', 'kios.index', 'kios.create', 'kios.edit']) }}">
                <a href="#" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-store'></i>
                    <div data-i18n="Form Layouts">Outlet</div>
                </a>
                <ul class="menu-sub">
                    @can('OUTLETKIOS_READ')
                        <li class="menu-item {{ menuAktif(['kios.index', 'kios.create', 'kios.edit']) }}">
                            <a href="{{ route('kios.index') }} " class="menu-link">
                                <div data-i18n="Vertical Form">Kios</div>
                            </a>
                        </li>
                    @endcan
                    @can('OFFICE_READ')
                        <li
                            class="menu-item {{ menuAktif(['office.index', 'office.create', 'office.edit', 'office.outletkios']) }}">
                            <a href="{{ route('office.index') }}" class="menu-link">
                                <div data-i18n="Horizontal Form">Outlet</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['PRODUCT_READ', 'CATEGORY_READ'])
            <li
                class="menu-item {{ menuAktif(['product.index', 'product.create', 'product.edit', 'category.index', 'category.create', 'category.edit', 'productattribute.index', 'productattribute.create', 'productattribute.edit']) }}">
                <a href="#" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-palette'></i>
                    <div data-i18n="Form Layouts">Data Produk</div>
                </a>
                <ul class="menu-sub">
                    @can('PRODUCT_READ')
                        <li class="menu-item {{ menuAktif(['product.index', 'product.create', 'product.edit']) }}">
                            <a href="{{ route('product.index') }} " class="menu-link">
                                <div data-i18n="Vertical Form">Produk</div>
                            </a>
                        </li>
                    @endcan
                    @can('CATEGORY_READ')
                        <li class="menu-item {{ menuAktif(['category.index', 'category.create', 'category.edit']) }}">
                            <a href="{{ route('category.index') }}" class="menu-link">
                                <div data-i18n="Horizontal Form">Kategori</div>
                            </a>
                        </li>
                    @endcan
                    @can('PRODUCTATTRIBUTE_READ')
                        <li
                            class="menu-item {{ menuAktif(['productattribute.index', 'productattribute.create', 'productattribute.edit']) }}">
                            <a href="{{ route('productattribute.index') }}" class="menu-link">
                                <div data-i18n="Horizontal Form">Pengaturan</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['PENGATURAN_READ', 'USERWEB_READ', 'ROLE_READ'])
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Pengaturan</span></li>
        @endcanany


        @can('USERWEB_READ')
            <li class="menu-item {{ menuAktif(['user.index', 'user.create', 'user.edit']) }} ">
                <a href="{{ route('user.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div data-i18n="Analytics">User</div>
                </a>
            </li>
        @endcan

        @can('ROLE_READ')
            <li class="menu-item {{ menuAktif(['role.index', 'role.create', 'role.edit']) }}">
                <a href="{{ route('role.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-outline"></i>
                    <div data-i18n="Analytics">Role</div>
                </a>
            </li>
        @endcan

        @can('PENGATURAN_READ')
            <li class="menu-item {{ menuAktif(['setting.show']) }}">
                <a href="{{ route('setting.show') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-cog"></i>
                    <div data-i18n="Analytics">Pengaturan</div>
                </a>
            </li>
        @endcan

    </ul>
</aside>
