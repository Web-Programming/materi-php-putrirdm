<div class="list-group list-group-flush">
    <a href="/dashboard"
        class="list-group-item list-group-item-action {{ request()->path() === '/dashboard' ? 'active' : '' }}">Dashboard</a>
    <a href="/produk/"
        class="list-group-item list-group-item-action {{ request()->is('produk') ? 'active' : '' }}">Produk</a>
    @yield('submenu-produk')
    <a href="/supplier"
        class="list-group-item list-group-item-action {{ request()->is('supplier') ? 'active' : '' }}">Supplier</a>
    @yield('submenu-supplier')
</div>