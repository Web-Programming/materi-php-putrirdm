# Panduan REST API dengan Laravel

> Proyek: **pakjr1** | Framework: Laravel 13 | PHP 8.3

---

## Daftar Isi

1. [Apa itu REST API?](#1-apa-itu-rest-api)
2. [Langkah 1 — Install API Router](#2-langkah-1--install-api-router)
3. [Langkah 2 — Buat API Resource Controller (CRUD Produk)](#3-langkah-2--buat-api-resource-controller-crud-produk)
4. [Langkah 3 — Daftarkan Route API](#4-langkah-3--daftarkan-route-api)
5. [Langkah 4 — Buat API Register & Login](#5-langkah-4--buat-api-register--login)
6. [Langkah 5 — Proteksi Route dengan Sanctum](#6-langkah-5--proteksi-route-dengan-sanctum)
7. [Dokumentasi & Uji Coba API](#7-dokumentasi--uji-coba-api)

---

## 1. Apa itu REST API?

**REST API** (Representational State Transfer Application Programming Interface) adalah standar arsitektur untuk komunikasi antar sistem melalui protokol HTTP.

| Metode HTTP | Fungsi         | Contoh URL             |
|-------------|----------------|------------------------|
| `GET`       | Ambil data     | `GET /api/products`    |
| `POST`      | Tambah data    | `POST /api/products`   |
| `PUT/PATCH` | Ubah data      | `PUT /api/products/1`  |
| `DELETE`    | Hapus data     | `DELETE /api/products/1`|

**Format response** yang digunakan adalah **JSON** (JavaScript Object Notation).

---

## 2. Langkah 1 — Install API Router

Sejak Laravel 11, file `routes/api.php` tidak dibuat secara otomatis. Kita perlu menginstallnya secara terpisah menggunakan perintah Artisan.

### Jalankan perintah berikut:

```bash
php artisan install:api
```

Perintah ini akan:
- Membuat file `routes/api.php`
- Menginstall **Laravel Sanctum** (untuk autentikasi token)
- Membuat migrasi tabel `personal_access_tokens`
- Mendaftarkan middleware Sanctum secara otomatis

### Jalankan migrasi setelah install:

```bash
php artisan migrate
```

### Verifikasi — cek `routes/api.php` yang baru dibuat:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
```

### Tambahkan trait `HasApiTokens` pada Model User

Buka file `app/Models/User.php` dan pastikan menggunakan trait `HasApiTokens` dari Sanctum:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ← tambahkan ini

class User extends Authenticatable
{
    use HasApiTokens, Notifiable; // ← tambahkan HasApiTokens
    
    // ... kode lainnya
}
```

---

## 3. Langkah 2 — Buat API Resource Controller (CRUD Produk)

Kita akan membuat controller API khusus untuk modul **Product** yang sudah ada.  
Controller ini **terpisah** dari `ProductController.php` yang digunakan untuk tampilan web (blade).

### Buat controller dengan perintah Artisan:

```bash
php artisan make:controller Api/ProductController --api
```

Flag `--api` menghasilkan 5 method standar (tanpa `create` dan `edit` karena API tidak butuh form HTML).

### Isi file `app/Http/Controllers/Api/ProductController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Menampilkan semua produk (dengan pagination)
     */
    public function index(): JsonResponse
    {
        $products = Product::paginate(10);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar produk berhasil diambil.',
            'data'    => $products,
        ], 200);
    }

    /**
     * POST /api/products
     * Menyimpan produk baru
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string',
            'status'       => 'required|in:new,used',
            'is_active'    => 'nullable|boolean',
            'release_date' => 'nullable|date',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil ditambahkan.',
            'data'    => $product,
        ], 201); // 201 = Created
    }

    /**
     * GET /api/products/{id}
     * Menampilkan detail satu produk
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail produk berhasil diambil.',
            'data'    => $product,
        ], 200);
    }

    /**
     * PUT /api/products/{id}
     * Memperbarui data produk
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string',
            'status'       => 'required|in:new,used',
            'is_active'    => 'nullable|boolean',
            'release_date' => 'nullable|date',
        ]);

        $product->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil diperbarui.',
            'data'    => $product,
        ], 200);
    }

    /**
     * DELETE /api/products/{id}
     * Menghapus produk
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Produk berhasil dihapus.',
        ], 200);
    }
}
```

### Penjelasan HTTP Status Code yang digunakan:

| Kode | Arti                  | Digunakan saat                        |
|------|-----------------------|---------------------------------------|
| 200  | OK                    | Berhasil GET, PUT, DELETE             |
| 201  | Created               | Berhasil POST (data baru dibuat)      |
| 404  | Not Found             | Data tidak ditemukan                  |
| 422  | Unprocessable Entity  | Validasi gagal (otomatis oleh Laravel)|
| 401  | Unauthorized          | Belum login / token tidak valid       |

---

## 4. Langkah 3 — Daftarkan Route API

Buka file `routes/api.php` dan tambahkan route untuk product:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthApiController;

// ── Route Publik (tanpa autentikasi) ──────────────────────────
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login',    [AuthApiController::class, 'login']);

// ── Route Terproteksi (membutuhkan token Sanctum) ─────────────
Route::middleware('auth:sanctum')->group(function () {

    // Informasi user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout
    Route::post('/logout', [AuthApiController::class, 'logout']);

    // CRUD Produk — menggunakan apiResource (auto 5 route)
    Route::apiResource('products', ProductController::class);
});
```

### Cek semua route API yang terdaftar:

```bash
php artisan route:list --path=api
```

Output yang diharapkan:

```
POST   api/register          → AuthApiController@register
POST   api/login             → AuthApiController@login
GET    api/user              → Closure
POST   api/logout            → AuthApiController@logout
GET    api/products          → ProductController@index
POST   api/products          → ProductController@store
GET    api/products/{product}→ ProductController@show
PUT    api/products/{product}→ ProductController@update
DELETE api/products/{product}→ ProductController@destroy
```

---

## 5. Langkah 4 — Buat API Register & Login

### Buat controller autentikasi API:

```bash
php artisan make:controller Api/AuthApiController
```

### Isi file `app/Http/Controllers/Api/AuthApiController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * POST /api/register
     * Mendaftarkan user baru dan mengembalikan token
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Buat user baru
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Buat token untuk user yang baru daftar
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Registrasi berhasil.',
            'data'    => [
                'user'  => $user,
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * POST /api/login
     * Login dan mengembalikan token
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Cek kredensial
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $user = Auth::user();

        // Hapus token lama (opsional: agar hanya 1 sesi aktif)
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil.',
            'data'    => [
                'user'  => $user,
                'token' => $token,
            ],
        ], 200);
    }

    /**
     * POST /api/logout
     * Logout — hapus token yang sedang dipakai
     * (Route ini membutuhkan middleware auth:sanctum)
     */
    public function logout(Request $request): JsonResponse
    {
        // Hapus hanya token yang digunakan saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil.',
        ], 200);
    }
}
```

### Penjelasan alur autentikasi Sanctum:

```
[Client]  ──POST /api/register──▶  [Server]
                                    ↓ buat user + token
          ◀── { token: "..." } ────

[Client]  ──POST /api/login ─────▶  [Server]
                                    ↓ verifikasi + token
          ◀── { token: "..." } ────

[Client]  ──GET /api/products ───▶  [Server]
          Header: Authorization: Bearer {token}
                                    ↓ validasi token
          ◀── { data: [...] } ─────
```

---

## 6. Langkah 5 — Proteksi Route dengan Sanctum

Route yang berada di dalam blok `middleware('auth:sanctum')` pada `routes/api.php` **wajib menyertakan token** di setiap request.

### Cara menyertakan token di Header HTTP:

```
Authorization: Bearer 1|abc123xyz...
```

Jika token tidak disertakan atau tidak valid, server akan merespons:

```json
{
    "message": "Unauthenticated."
}
```

dengan status kode **401 Unauthorized**.

---

## 7. Dokumentasi & Uji Coba API

Gunakan salah satu tool berikut untuk menguji API:
- **Postman** — Aplikasi desktop populer untuk uji API
- **Thunder Client** — Extension ringan di dalam VS Code
- **curl** — Command line tool

---

### A. Uji Register

**Method:** `POST`  
**URL:** `http://localhost:8000/api/register`  
**Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**

```json
{
    "name": "Nur Rachmat",
    "email": "user123@google.com",
    "password": "123456",
    "password_confirmation": "123456"
}
```

**Response Berhasil (201):**

```json
{
    "status": "success",
    "message": "Registrasi berhasil.",
    "data": {
        "user": {
            "id": 1,
            "name": "Nur Rachmat",
            "email": "user123@google.com",
            "created_at": "2026-05-26T10:00:00.000000Z",
            "updated_at": "2026-05-26T10:00:00.000000Z"
        },
        "token": "1|abc123xyzTokenPanjang..."
    }
}
```

---

### B. Uji Login

**Method:** `POST`  
**URL:** `http://localhost:8000/api/login`  
**Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**

```json
{
    "email": "user123@gmail.com",
    "password": "123456"
}
```

**Response Berhasil (200):**

```json
{
    "status": "success",
    "message": "Login berhasil.",
    "data": {
        "user": {
            "id": 1,
            "name": "Nur Rachmat",
            "email": "user123@gmail.com"
        },
        "token": "2|newTokenSetelahLogin..."
    }
}
```

**Response Gagal (401):**

```json
{
    "status": "error",
    "message": "Email atau password salah."
}
```

> **Simpan token** dari response login untuk digunakan di request berikutnya!

---

### C. Uji GET Semua Produk

**Method:** `GET`  
**URL:** `http://localhost:8000/api/products`  
**Headers:**

```
Accept: application/json
Authorization: Bearer 2|newTokenSetelahLogin...
```

**Response Berhasil (200):**

```json
{
    "status": "success",
    "message": "Daftar produk berhasil diambil.",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "Laptop ASUS",
                "price": "8500000.00",
                "description": "Laptop gaming terbaik",
                "status": "new",
                "is_active": true,
                "release_date": "2026-01-15",
                "created_at": "2026-05-26T10:00:00.000000Z",
                "updated_at": "2026-05-26T10:00:00.000000Z"
            }
        ],
        "per_page": 10,
        "total": 1
    }
}
```

---

### D. Uji GET Detail Produk

**Method:** `GET`  
**URL:** `http://localhost:8000/api/products/1`  
**Headers:**

```
Accept: application/json
Authorization: Bearer 2|newTokenSetelahLogin...
```

**Response Berhasil (200):**

```json
{
    "status": "success",
    "message": "Detail produk berhasil diambil.",
    "data": {
        "id": 1,
        "name": "Laptop ASUS",
        "price": "8500000.00",
        "description": "Laptop gaming terbaik",
        "status": "new",
        "is_active": true,
        "release_date": "2026-01-15"
    }
}
```

**Response Tidak Ditemukan (404):**

```json
{
    "status": "error",
    "message": "Produk tidak ditemukan."
}
```

---

### E. Uji POST Tambah Produk

**Method:** `POST`  
**URL:** `http://localhost:8000/api/products`  
**Headers:**

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer 2|newTokenSetelahLogin...
```

**Body (JSON):**

```json
{
    "name": "Keyboard Mechanical",
    "price": 750000,
    "description": "Keyboard gaming dengan switch red",
    "status": "new",
    "is_active": true,
    "release_date": "2026-03-10"
}
```

**Response Berhasil (201):**

```json
{
    "status": "success",
    "message": "Produk berhasil ditambahkan.",
    "data": {
        "id": 2,
        "name": "Keyboard Mechanical",
        "price": "750000.00",
        "description": "Keyboard gaming dengan switch red",
        "status": "new",
        "is_active": true,
        "release_date": "2026-03-10",
        "created_at": "2026-05-26T11:00:00.000000Z",
        "updated_at": "2026-05-26T11:00:00.000000Z"
    }
}
```

**Response Validasi Gagal (422):**

```json
{
    "message": "The name field is required.",
    "errors": {
        "name": ["The name field is required."],
        "price": ["The price field is required."]
    }
}
```

---

### F. Uji PUT Update Produk

**Method:** `PUT`  
**URL:** `http://localhost:8000/api/products/2`  
**Headers:**

```
Content-Type: application/json
Accept: application/json
Authorization: Bearer 2|newTokenSetelahLogin...
```

**Body (JSON):**

```json
{
    "name": "Keyboard Mechanical RGB",
    "price": 850000,
    "description": "Keyboard gaming dengan switch red dan lampu RGB",
    "status": "new",
    "is_active": true,
    "release_date": "2026-03-10"
}
```

**Response Berhasil (200):**

```json
{
    "status": "success",
    "message": "Produk berhasil diperbarui.",
    "data": {
        "id": 2,
        "name": "Keyboard Mechanical RGB",
        "price": "850000.00",
        "description": "Keyboard gaming dengan switch red dan lampu RGB",
        "status": "new",
        "is_active": true,
        "release_date": "2026-03-10"
    }
}
```

---

### G. Uji DELETE Hapus Produk

**Method:** `DELETE`  
**URL:** `http://localhost:8000/api/products/2`  
**Headers:**

```
Accept: application/json
Authorization: Bearer 2|newTokenSetelahLogin...
```

**Response Berhasil (200):**

```json
{
    "status": "success",
    "message": "Produk berhasil dihapus."
}
```

---

### H. Uji Logout

**Method:** `POST`  
**URL:** `http://localhost:8000/api/logout`  
**Headers:**

```
Accept: application/json
Authorization: Bearer 2|newTokenSetelahLogin...
```

**Response Berhasil (200):**

```json
{
    "status": "success",
    "message": "Logout berhasil."
}
```

---

## Ringkasan Endpoint API

| No | Method   | Endpoint               | Deskripsi                      | Auth?  |
|----|----------|------------------------|--------------------------------|--------|
| 1  | `POST`   | `/api/register`        | Daftar user baru               | Tidak  |
| 2  | `POST`   | `/api/login`           | Login, mendapatkan token       | Tidak  |
| 3  | `POST`   | `/api/logout`          | Logout, hapus token            | Ya     |
| 4  | `GET`    | `/api/user`            | Info user yang sedang login    | Ya     |
| 5  | `GET`    | `/api/products`        | Daftar semua produk            | Ya     |
| 6  | `POST`   | `/api/products`        | Tambah produk baru             | Ya     |
| 7  | `GET`    | `/api/products/{id}`   | Detail satu produk             | Ya     |
| 8  | `PUT`    | `/api/products/{id}`   | Update produk                  | Ya     |
| 9  | `DELETE` | `/api/products/{id}`   | Hapus produk                   | Ya     |

---

## Struktur File yang Dibuat

```
app/
└── Http/
    └── Controllers/
        └── Api/
            ├── AuthApiController.php   ← Controller autentikasi API
            └── ProductController.php  ← Controller CRUD produk API

routes/
└── api.php                            ← File route API (dibuat saat install:api)
```