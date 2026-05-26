# Materi Authorization di Laravel

## Pengantar
Authorization adalah proses untuk menentukan apakah user memiliki hak akses untuk melakukan aksi tertentu pada aplikasi. Laravel menyediakan dua cara utama untuk mengimplementasikan authorization:
1. **Gates** - Closure-based authorization
2. **Policies** - Class-based authorization untuk model tertentu

## Persiapan

### 1. Pastikan Authentication Sudah Berjalan
Pastikan sistem authentication sudah dikonfigurasi (Laravel Breeze, Jetstream, atau manual).

### 2. Tambahkan Kolom Role pada Tabel Users (Opsional)
Jika ingin membedakan role user:

```bash
php artisan make:migration add_role_to_users_table
```

Edit migration:
```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('user'); // admin, user, editor, dll
    });
}
```

Jalankan migration:
```bash
php artisan migrate
```

---

## BAGIAN 1: Menggunakan Gates

### Langkah 1: Mendefinisikan Gates

Buat Provider dengan nama AuthServiceProvider 
menggunakan perintah 
`php artisan make:provider AuthServiceProvider`

Edit file `app/Providers/AuthServiceProvider.php`:

```php
<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // akan kita isi nanti untuk Policy
    ];

    public function boot(): void
    {
        // Gate untuk mengecek apakah user adalah admin
        Gate::define('manage-products', function (User $user) {
            return $user->role === 'admin';
        });

        // Gate untuk update product (bisa admin atau owner)
        Gate::define('update-product', function (User $user, Product $product) {
            return $user->role === 'admin' || $user->id === $product->user_id;
        });

        // Gate untuk delete product (hanya admin)
        Gate::define('delete-product', function (User $user, Product $product) {
            return $user->role === 'admin';
        });

        // Gate untuk create product (user yang sudah login)
        Gate::define('create-product', function (User $user) {
            return $user !== null;
        });
    }
}
```

### Langkah 2: Menggunakan Gates di Controller

Edit `ProductController.php`:

```php
use Illuminate\Support\Facades\Gate;

public function create()
{
    // Cek authorization menggunakan Gate
    Gate::authorize('create-product');
    
    $title = "Tambah Produk";
    return view('produk.create', compact('title'));
}

public function edit(string $id)
{
    $product = Product::findOrFail($id);
    
    // Cek authorization dengan parameter
    Gate::authorize('update-product', $product);
    
    $title = "Edit Produk";
    return view('produk.edit', compact('product', 'title'));
}

public function destroy(string $id)
{
    $product = Product::findOrFail($id);
    
    Gate::authorize('delete-product', $product);
    
    $product->delete();
    return redirect()->route('produk.index')
        ->with('success', 'Produk berhasil dihapus.');
}
```

### Langkah 3: Menggunakan Gates di Blade Template

Edit view `produk/index.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $title }}</h1>
    
    @can('create-product')
        <a href="{{ route('produk.create') }}" class="btn btn-primary mb-3">
            Tambah Produk
        </a>
    @endcan

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td>{{ ucfirst($product->status) }}</td>
                <td>
                    <a href="{{ route('produk.show', $product->id) }}" class="btn btn-sm btn-info">Detail</a>
                    
                    @can('update-product', $product)
                        <a href="{{ route('produk.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    @endcan
                    
                    @can('delete-product', $product)
                        <form action="{{ route('produk.destroy', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Yakin ingin menghapus?')">
                                Hapus
                            </button>
                        </form>
                    @endcan
                    
                    @cannot('update-product', $product)
                        <span class="badge bg-secondary">Tidak dapat edit</span>
                    @endcannot
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $products->links() }}
</div>
@endsection
```

---

## BAGIAN 2: Menggunakan Policies

### Langkah 1: Membuat Policy Class

Jalankan command artisan:

```bash
php artisan make:policy ProductPolicy --model=Product
```

File akan dibuat di `app/Policies/ProductPolicy.php`

### Langkah 2: Mendefinisikan Policy Methods

Edit `app/Policies/ProductPolicy.php`:

```php
<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine if the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        // Semua user yang login bisa melihat daftar produk
        return true;
    }

    /**
     * Determine if the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        // Semua user bisa melihat detail produk
        return true;
    }

    /**
     * Determine if the user can create products.
     */
    public function create(User $user): bool
    {
        // Hanya user dengan role admin atau editor yang bisa create
        return in_array($user->role, ['admin', 'editor']);
    }

    /**
     * Determine if the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        // Admin bisa update semua, user lain hanya miliknya
        return $user->role === 'admin' || $user->id === $product->user_id;
    }

    /**
     * Determine if the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        // Hanya admin yang bisa delete
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can restore the product.
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can permanently delete the product.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }
}
```

### Langkah 3: Registrasi Policy

Edit `app/Providers/AuthServiceProvider.php`:

```php
<?php

namespace App\Providers;

use App\Models\Product;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
    ];

    public function boot(): void
    {
        // Gates yang sudah didefinisikan sebelumnya (opsional, bisa dihapus jika memakai Policy)
    }
}
```

### Langkah 4: Menggunakan Policy di Controller

Edit `ProductController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Cek policy viewAny
        $this->authorize('viewAny', Product::class);
        
        $title = "Daftar Produk";
        $products = Product::paginate(10);
        return view('produk.index', compact('title', 'products'));
    }

    public function create()
    {
        // Cek policy create
        $this->authorize('create', Product::class);
        
        $title = "Tambah Produk";
        return view('produk.create', compact('title'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:new,used',
            'is_active' => 'nullable|boolean',
            'release_date' => 'nullable|date',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['user_id'] = auth()->id(); // Tambahkan user_id
        
        Product::create($validated);
        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        
        // Cek policy view
        $this->authorize('view', $product);
        
        $title = "Detail Produk";
        return view('produk.detail', compact('product', 'title'));
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        
        // Cek policy update
        $this->authorize('update', $product);
        
        $title = "Edit Produk";
        return view('produk.edit', compact('product', 'title'));
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:new,used',
            'is_active' => 'nullable|boolean',
            'release_date' => 'nullable|date',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $product->update($validated);

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        
        // Cek policy delete
        $this->authorize('delete', $product);
        
        $product->delete();

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
```

### Langkah 5: Menggunakan @can dan @cannot di Blade dengan Policy

Edit `produk/index.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $title }}</h1>
    
    {{-- Menggunakan @can dengan Policy --}}
    @can('create', App\Models\Product::class)
        <a href="{{ route('produk.create') }}" class="btn btn-primary mb-3">
            Tambah Produk
        </a>
    @endcan
    
    {{-- Alternatif: menggunakan @cannot --}}
    @cannot('create', App\Models\Product::class)
        <div class="alert alert-info">
            Anda tidak memiliki izin untuk menambah produk.
        </div>
    @endcannot

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Harga</th>
                <th>Status</th>
                <th>Owner</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td>
                    <span class="badge bg-{{ $product->status === 'new' ? 'success' : 'warning' }}">
                        {{ ucfirst($product->status) }}
                    </span>
                </td>
                <td>{{ $product->user->name ?? 'N/A' }}</td>
                <td>
                    {{-- Tombol Detail (semua user bisa lihat) --}}
                    @can('view', $product)
                        <a href="{{ route('produk.show', $product->id) }}" 
                           class="btn btn-sm btn-info">Detail</a>
                    @endcan
                    
                    {{-- Tombol Edit (hanya yang punya izin) --}}
                    @can('update', $product)
                        <a href="{{ route('produk.edit', $product->id) }}" 
                           class="btn btn-sm btn-warning">Edit</a>
                    @else
                        <button class="btn btn-sm btn-secondary" disabled>Edit</button>
                    @endcan
                    
                    {{-- Tombol Delete (hanya admin) --}}
                    @can('delete', $product)
                        <form action="{{ route('produk.destroy', $product->id) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Yakin ingin menghapus?')">
                                Hapus
                            </button>
                        </form>
                    @endcan
                    
                    {{-- Pesan jika tidak bisa edit --}}
                    @cannot('update', $product)
                        <small class="text-muted">(Bukan pemilik)</small>
                    @endcannot
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada produk</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $products->links() }}
</div>
@endsection
```

---

## Penggunaan Lanjutan

### 1. Middleware Authorization

Buat middleware untuk proteksi route:

```php
Route::middleware(['auth', 'can:create,App\Models\Product'])->group(function () {
    Route::get('/produk/create', [ProductController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProductController::class, 'store'])->name('produk.store');
});
```

### 2. Menggunakan Gate::allows() dan Gate::denies()

Di Controller atau Blade:

```php
if (Gate::allows('update-product', $product)) {
    // User bisa update
}

if (Gate::denies('update-product', $product)) {
    abort(403, 'Unauthorized action.');
}
```

### 3. Menggunakan @canany

Untuk multiple permissions:

```blade
@canany(['update', 'delete'], $product)
    <div class="alert alert-info">
        Anda memiliki akses untuk edit atau hapus produk ini.
    </div>
@endcanany
```

### 4. Policy dengan Before Hook

Edit `ProductPolicy.php`:

```php
/**
 * Perform pre-authorization checks.
 */
public function before(User $user, string $ability): bool|null
{
    // Super admin bypass semua authorization
    if ($user->role === 'superadmin') {
        return true;
    }

    return null; // Lanjut ke method policy lainnya
}
```

---

## Perbedaan Gates vs Policies

| Aspek | Gates | Policies |
|-------|-------|----------|
| Struktur | Closure-based | Class-based |
| Kompleksitas | Simple authorization | Complex model authorization |
| Organisasi | Di AuthServiceProvider | File terpisah per model |
| Penggunaan | General actions | Model-specific actions |
| Auto-discovery | Tidak | Ya (jika mengikuti konvensi) |

## Kesimpulan

1. **Gunakan Gates** untuk authorization sederhana yang tidak terikat pada model tertentu
2. **Gunakan Policies** untuk authorization yang kompleks dan terkait dengan model spesifik
3. **@can** dan **@cannot** memudahkan conditional rendering di Blade
4. Selalu **authorize** di controller sebelum melakukan aksi penting
5. Kombinasikan dengan **middleware** untuk proteksi route

## Testing Authorization

Contoh test untuk Policy:

```php
public function test_admin_can_delete_product()
{
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();
    
    $this->actingAs($admin);
    
    $response = $this->delete(route('produk.destroy', $product->id));
    
    $response->assertRedirect(route('produk.index'));
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
}

public function test_regular_user_cannot_delete_product()
{
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::factory()->create();
    
    $this->actingAs($user);
    
    $response = $this->delete(route('produk.destroy', $product->id));
    
    $response->assertForbidden();
}
```