<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Aplikasi Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="bi bi-shop me-2"></i>Aplikasi Penjualan</a>
            <div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
                @else
                    <a href="{{ url('/login') }}" class="btn btn-outline-light btn-sm me-2">Login</a>
                    <a href="{{ url('/register') }}" class="btn btn-light btn-sm">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <i class="bi bi-shop display-1 text-primary"></i>
                <h1 class="mt-3 fw-bold">Aplikasi Penjualan</h1>
                <p class="lead text-muted mt-2">Kelola data barang, pesanan, dan laporan penjualan dengan mudah.</p>
                <div class="mt-4 d-flex justify-content-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-speedometer2 me-2"></i>Masuk ke Dashboard
                        </a>
                    @else
                        <a href="{{ url('/login') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                        <a href="{{ url('/register') }}" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-person-plus me-2"></i>Daftar Akun
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        <div class="row mt-5 g-4">
            <div class="col-md-4">

                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-box-seam display-5 text-primary mb-3"></i>
                    <h5>Manajemen Barang</h5>
                    <p class="text-muted">Tambah, edit, dan hapus data barang dengan cepat dan mudah.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-receipt display-5 text-success mb-3"></i>
                    <h5>Data Pesanan</h5>
                    <p class="text-muted">Pantau seluruh transaksi dan pesanan secara real-time.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center p-4">
                    <i class="bi bi-file-earmark-bar-graph display-5 text-warning mb-3"></i>
                    <h5>Laporan Penjualan</h5>
                    <p class="text-muted">Lihat ringkasan dan analisis laporan penjualan bulanan.</p>
                </div>
            </div>
        </div>
    </div>
    <footer class="text-center text-muted mt-5 py-4 border-top">
        &copy; {{ date('Y') }} Aplikasi Penjualan. All rights reserved.
    </footer>
</body>

</html>