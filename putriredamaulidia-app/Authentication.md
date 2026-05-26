# Membuat Authentikasi 

Authentikasi (Authentication) adalah proses verifikasi identitas pengguna. Laravel menyediakan fitur authentikasi bawaan melalui **facade `Auth`** dan **middleware `auth`**, sehingga kita tidak perlu membangun sistem keamanan dari nol.

Pada materi ini kita akan membuat authentikasi **custom** secara manual (tanpa Laravel Breeze/Jetstream) agar lebih memahami alurnya.

---

## Alur Kerja Authentikasi

```
Pengguna                      Laravel
   |                              |
   |--- GET /login  ------------>|  (tampilkan form login)
   |<-- View login.blade.php ----|
   |                              |
   |--- POST /login (email+pass)->|  (proses login)
   |    Auth::attempt()           |
   |<-- redirect /dashboard -----|  (berhasil)
   |    atau balik ke login       |  (gagal)
   |                              |
   |--- POST /logout ----------->|  (proses logout)
   |    Auth::logout()            |
   |<-- redirect /login ---------|
```

---

## Langkah 1: Membuat AuthController

Controller authentikasi bertanggung jawab menangani proses **register**, **login**, dan **logout**.

Buat controller baru: `app/Http/Controllers/AuthController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ==================== REGISTER ====================

    // Tampilkan form register
    public function registerForm()
    {
        return view('auth.register', ['title' => 'Daftar Akun']);
    }

    // Proses simpan user baru
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Simpan user baru ke database
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // enkripsi password
        ]);

        // Redirect ke halaman login dengan pesan sukses
        return redirect('/login')->with('success', 'Akun berhasil dibuat. Silakan login.');
    }

    // ==================== LOGIN ====================

    // Tampilkan form login
    public function loginForm()
    {
        return view('auth.login', ['title' => 'Login']);
    }

    // Proses autentikasi pengguna
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Ambil kredensial dari request
        $credentials = $request->only('email', 'password');

        // Coba login dengan Auth::attempt()
        // Parameter kedua true = aktifkan fitur "Remember Me"
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerasi session untuk mencegah Session Fixation Attack
            $request->session()->regenerate();

            // Redirect ke halaman yang dituju atau /dashboard
            return redirect()->intended('/dashboard');
        }

        // Jika gagal, kembali ke form login dengan pesan error
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }

    // ==================== LOGOUT ====================

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout(); // hapus sesi login

        // Invalidasi & regenerasi token session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
```

### Penjelasan Metode Penting

| Metode | Fungsi |
|---|---|
| `Auth::attempt($credentials)` | Memeriksa email & password ke database secara otomatis |
| `Hash::make($password)` | Mengenkripsi password dengan algoritma bcrypt |
| `$request->session()->regenerate()` | Mencegah serangan **Session Fixation** |
| `redirect()->intended('/dashboard')` | Mengarahkan ke URL yang awalnya dituju sebelum diminta login |
| `Auth::logout()` | Menghapus data sesi pengguna yang sedang login |

---

## Langkah 2: Membuat DashboardController

`DashboardController` bertugas mengambil data statistik dari database dan mengirimkannya ke view dashboard.

Buat controller baru: `app/Http/Controllers/DashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProduk       = Product::count();
        $produkAktif       = Product::where('is_active', true)->count();
        $produkTidakAktif  = Product::where('is_active', false)->count();
        $nilaiStok         = 'Rp ' . number_format(Product::sum('price'), 0, ',', '.');
        $produkTerbaru     = Product::latest()->take(5)->get();

        return view('dashboard', compact(
            'totalProduk',
            'produkAktif',
            'produkTidakAktif',
            'nilaiStok',
            'produkTerbaru'
        ));
    }
}
```

### Penjelasan DashboardController

| Variabel | Sumber Data | Keterangan |
|---|---|---|
| `$totalProduk` | `Product::count()` | Total seluruh data produk |
| `$produkAktif` | `where('is_active', true)->count()` | Jumlah produk dengan status aktif |
| `$produkTidakAktif` | `where('is_active', false)->count()` | Jumlah produk dengan status tidak aktif |
| `$nilaiStok` | `Product::sum('price')` | Total nilai harga semua produk |
| `$produkTerbaru` | `latest()->take(5)->get()` | 5 produk paling baru berdasarkan waktu buat |

> **Catatan:** `compact()` adalah fungsi PHP bawaan yang mengemas beberapa variabel menjadi sebuah array asosiatif, lalu diteruskan ke view agar bisa diakses langsung dengan nama variabelnya.

---

## Langkah 3: Membuat View

### 3a. View Home — `resources/views/home.blade.php`

Halaman beranda publik yang dapat diakses oleh siapa saja (tanpa login).

```blade
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
                <h5>Manajemen Produk</h5>
                <p class="text-muted">Tambah, edit, dan hapus data produk dengan cepat dan mudah.</p>
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
```

**Penjelasan elemen penting pada `home.blade.php`:**
- `@auth ... @else ... @endauth` — menampilkan konten berbeda tergantung status login.
- `session('success')` — menampilkan notifikasi sukses setelah register berhasil.
- Navbar menampilkan tombol **Dashboard** jika sudah login, atau **Login/Daftar** jika belum.

---

### 3b. View Dashboard — `resources/views/dashboard.blade.php`

Halaman dashboard hanya dapat diakses oleh pengguna yang sudah login. View ini menerima variabel dari `DashboardController`.

```blade
@extends('app.master')
@section('title', 'Dashboard')

@section('sidebar')
    @parent
@endsection

@section('content')
    <div class="mt-4 mb-3">
        <h1>Dashboard</h1>
        <p class="text-muted">Selamat datang, <strong>{{ Auth::user()->name }}</strong>!</p>
    </div>

    {{-- Kartu Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-primary">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-2 fw-bold">{{ $totalProduk }}</div>
                        <div>Total Produk</div>
                    </div>
                    <i class="bi bi-box-seam fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-primary border-0">
                    <a href="{{ url('/produk') }}" class="text-white text-decoration-none small">
                        Lihat semua &rarr;
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-success">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-2 fw-bold">{{ $produkAktif }}</div>
                        <div>Produk Aktif</div>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-success border-0">
                    <a href="{{ url('/produk') }}" class="text-white text-decoration-none small">
                        Lihat semua &rarr;
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-danger">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-2 fw-bold">{{ $produkTidakAktif }}</div>
                        <div>Produk Tidak Aktif</div>
                    </div>
                    <i class="bi bi-x-circle fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-danger border-0">
                    <a href="{{ url('/produk') }}" class="text-white text-decoration-none small">
                        Lihat semua &rarr;
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-white bg-warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fs-2 fw-bold">{{ $nilaiStok }}</div>
                        <div>Total Nilai Stok</div>
                    </div>
                    <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                </div>
                <div class="card-footer bg-warning border-0">
                    <span class="text-white small">Nilai total inventori</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Produk Terbaru --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">5 Produk Terbaru</h5>
            <a href="{{ url('/produk/create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Tambah Produk
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Produk</th>
                        <th>Status</th>
                        <th>Harga</th>
                        <th>Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produkTerbaru as $produk)
                        <tr>
                            <td>{{ $produk->name }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $produk->status }}</span>
                            </td>
                            <td>Rp {{ number_format($produk->price, 0, ',', '.') }}</td>
                            <td>
                                @if ($produk->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Belum ada data produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer text-end">
            <a href="{{ url('/produk') }}" class="btn btn-outline-primary btn-sm">Lihat Semua Produk</a>
        </div>
    </div>
@endsection
```

**Penjelasan elemen penting pada `dashboard.blade.php`:**
- `@extends('app.master')` — view ini mewarisi layout utama.
- `Auth::user()->name` — menampilkan nama pengguna yang sedang login.
- Empat kartu statistik menampilkan variabel dari controller (`$totalProduk`, `$produkAktif`, `$produkTidakAktif`, `$nilaiStok`).
- `@forelse ... @empty` — menampilkan data barang terbaru, atau pesan kosong jika belum ada data.

---

### 3c. View Login — `resources/views/auth/login.blade.php`

Buat folder baru: `resources/views/auth/`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Aplikasi Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">

            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">{{ $title }}</h4>
                </div>
                <div class="card-body p-4">

                    {{-- Tampilkan pesan sukses (setelah register) --}}
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    {{-- Tampilkan error validasi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ url('/login') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="nama@email.com"
                                required
                                autofocus
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                placeholder="Masukkan password"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ingat Saya</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>

                </div>
                <div class="card-footer text-center text-muted">
                    Belum punya akun? <a href="{{ url('/register') }}">Daftar di sini</a>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
```

### 3d. View Register — `resources/views/auth/register.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Aplikasi Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">{{ $title }}</h4>
                </div>
                <div class="card-body p-4">

                    {{-- Tampilkan error validasi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ url('/register') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Masukkan nama lengkap"
                                required
                                autofocus
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="nama@email.com"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                placeholder="Minimal 6 karakter"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input
                                type="password"
                                class="form-control"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Ulangi password"
                                required
                            >
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Daftar</button>
                        </div>
                    </form>

                </div>
                <div class="card-footer text-center text-muted">
                    Sudah punya akun? <a href="{{ url('/login') }}">Login di sini</a>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
```

> **Catatan tentang `password_confirmation`:**  
> Aturan validasi `confirmed` pada controller mengharuskan adanya field dengan nama `password_confirmation` di form. Laravel otomatis mencocokkan keduanya.

---

## Langkah 4: Mendaftarkan Route

Buka file `routes/web.php` dan tambahkan route authentikasi:

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// ==================== HOME ====================

Route::get('/', function () {
    return view('home');
})->name('home');

// ==================== ROUTE AUTHENTIKASI ====================

// Tampilkan form register
Route::get('/register', [AuthController::class, 'registerForm'])
    ->name('register')
    ->middleware('guest'); // hanya bisa diakses jika BELUM login

// Proses simpan register
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest');

// Tampilkan form login
Route::get('/login', [AuthController::class, 'loginForm'])
    ->name('login')          // nama route ini WAJIB 'login' agar middleware auth berfungsi
    ->middleware('guest');

// Proses login
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest');

// Proses logout (gunakan POST untuk keamanan, bukan GET)
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');   // hanya bisa diakses jika SUDAH login


// ==================== ROUTE YANG DILINDUNGI ====================

// Semua route di dalam group ini hanya bisa diakses jika sudah login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/produk', [ProductController::class, 'index']);
    Route::get('/produk/create', [ProductController::class, 'create']);
    Route::get('/produk/{id}', [ProductController::class, 'show']);
    Route::get('/produk/edit/{id}', [ProductController::class, 'edit']);
    Route::post('/produk', [ProductController::class, 'store']);
    Route::put('/produk/update/{id}', [ProductController::class, 'update']);
    Route::delete('/produk/{id}', [ProductController::class, 'destroy']);

    //Daftarkan Route Lainnya di Sini : 
    // - Route Supplier
});

```

### Penjelasan Route

| Route | Method | Middleware | Fungsi |
|---|---|---|---|
| `/register` | GET | `guest` | Tampilkan form registrasi |
| `/register` | POST | `guest` | Simpan data user baru |
| `/login` | GET | `guest` | Tampilkan form login |
| `/login` | POST | `guest` | Proses autentikasi |
| `/logout` | POST | `auth` | Proses logout |
| `/dashboard` | GET | `auth` | Halaman dashboard (dilindungi) |
| `/produk` | GET | `auth` | Daftar produk (dilindungi) |

> **Kenapa logout menggunakan POST?**  
> Menggunakan GET untuk logout rentan terhadap serangan **CSRF** (Cross-Site Request Forgery). Dengan POST + `@csrf`, request dapat diverifikasi sebagai request yang sah dari form kita sendiri.

---

## Langkah 5: Mengatur Middleware

### 5a. Memahami Middleware Bawaan Laravel

Laravel sudah menyediakan dua middleware penting:

| Middleware | Alias | Fungsi |
|---|---|---|
| `Illuminate\Auth\Middleware\Authenticate` | `auth` | Wajib login untuk mengakses route |
| `Illuminate\Auth\Middleware\RedirectIfAuthenticated` | `guest` | Redirect ke home jika sudah login |

### 5b. Konfigurasi Redirect di `bootstrap/app.php`

Secara default, middleware `auth` akan redirect ke route bernama `login` jika pengguna belum terautentikasi. Ini bisa dikonfigurasi di `bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Konfigurasi redirect saat belum login
        // (opsional, default sudah mengarah ke route 'login')
        $middleware->redirectGuestsTo('/login');

        // Konfigurasi redirect saat sudah login dan mencoba akses halaman guest
        $middleware->redirectUsersTo('/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

### 5c. Menambahkan Tombol Logout di Navbar

Buka file `resources/views/app/navbar.blade.php` dan tambahkan kondisi login/logout:

```blade
{{-- Cek apakah pengguna sudah login --}}
@auth
    <span class="navbar-text me-3">
        Halo, <strong>{{ Auth::user()->name }}</strong>
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
```

### 5d. Menampilkan Info User di View (Opsional)

Di dalam view Blade manapun, Anda dapat mengakses data user yang sedang login:

```blade
{{-- Cek apakah sudah login --}}
@auth
    <p>Selamat datang, {{ Auth::user()->name }}</p>
    <p>Email: {{ Auth::user()->email }}</p>
@endauth

{{-- Atau menggunakan direktif @guest --}}
@guest
    <p>Anda belum login.</p>
@endguest
```

Di dalam Controller atau kode PHP:

```php
use Illuminate\Support\Facades\Auth;

// Ambil data user yang sedang login
$user = Auth::user();       // objek User
$userId = Auth::id();       // hanya ID-nya
$isLogin = Auth::check();   // true/false
```

---

## Ringkasan Struktur File yang Dibuat/Diubah

```
app/
  Http/
    Controllers/
      AuthController.php        ← BARU: controller login, register, logout
      DashboardController.php   ← BARU: controller halaman dashboard

resources/
  views/
    home.blade.php              ← BARU: halaman beranda (publik)
    dashboard.blade.php         ← BARU: halaman dashboard (dilindungi auth)
    auth/
      login.blade.php           ← BARU: halaman form login
      register.blade.php        ← BARU: halaman form register
    app/
      navbar.blade.php          ← EDIT: tambah tombol logout & info user
      sidebar.blade.php         ← EDIT: link Dashboard mengarah ke /dashboard

routes/
  web.php                       ← EDIT: tambah route auth, home & group middleware

bootstrap/
  app.php                       ← EDIT: konfigurasi redirect middleware
```

---

## Alur Lengkap Authentikasi

```
1. Pengguna buka / (home)
   → Tampilkan halaman beranda publik
   → Terdapat tombol Login dan Daftar

2. Pengguna buka /register
   → Isi form → POST /register
   → Controller simpan user ke DB (password di-hash)
   → Redirect ke /login

3. Pengguna buka /login
   → Isi email & password → POST /login
   → Auth::attempt() periksa ke tabel users
   → Jika cocok: session dibuat, redirect ke /dashboard
   → Jika tidak: balik ke form login dengan pesan error

4. Pengguna akses /dashboard (route dilindungi middleware 'auth')
   → Jika sudah login: tampilkan halaman dashboard
   → Jika belum login: redirect otomatis ke /login

5. Pengguna akses /produk (route dilindungi middleware 'auth')
   → Jika sudah login: tampilkan halaman
   → Jika belum login: redirect otomatis ke /login

4. Pengguna klik Logout
   → POST /logout
   → Auth::logout() hapus session
   → Redirect ke /login
```

---

## Catatan Keamanan

- **Jangan simpan password plaintext** — selalu gunakan `Hash::make()` saat menyimpan dan `Auth::attempt()` saat memverifikasi.
- **Selalu sertakan `@csrf`** di setiap form POST untuk mencegah serangan CSRF.
- **Gunakan `session()->regenerate()`** setelah login berhasil untuk mencegah Session Fixation Attack.
- **Gunakan POST untuk logout**, bukan GET, agar tidak bisa dipicu dari link eksternal.
- **Middleware `guest`** mencegah pengguna yang sudah login mengakses kembali halaman login/register.