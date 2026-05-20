<a class="navbar-brand" href="#">My App</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
    aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="mainNavbar">
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Produk</a>
        </li>
    </ul>
    {{-- Cek apakah pengguna sudah login --}}
    @auth
        <span class="navbar-text me-3">
            Halo, <strong>{{ Auth::user()->name }}({{ Auth::user()->email }})</strong>
        </span>
        {{-- Tombol Logout menggunakan form POST --}}
        <form action="{{ url('/logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
        </form>
    @else
        <a href="{{ url('/login') }}" class="btn btn-outline-primary btn-sm me-2">Login</a>
        <a href="{{ url('/register') }}" class="btn btn-primary btn-sm">Daftar</a>
    @endauth
</div>