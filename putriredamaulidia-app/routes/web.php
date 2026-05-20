<?php

// use Illuminate\Support\Facades\Route;

// //Route ke halaman utama (home)
// Route::get('/', function () {
//     echo "Hallo, Nama Saya Pak JR";
//     //return view('welcome');
// });
// //Route ke halaman alamat
// Route::get('/alamat', function(){
//     echo "Jalan Rajawali 14. Palembang";
// });

// //Route ke halaman path1/path2/detail
// Route::get('/path1/path2/detail', function(){
//     echo "Jalan Rajawali 14";
//     echo "<br>";
//     echo "Rt. 01 Rw. 02";
//     echo "<br>";
//     echo "Kecamatan Alang-Alang Lebar";
//     echo "<br>";
//     echo "Kota Palembang";
//     echo "<br>";
//     echo "Provinsi Sumatera Selatan";
// });

// //Route Dinamis dengan parameter id
// Route::get('/user/{id}', function($id){
//     echo "User ID: " . $id;
// });

// //Route Dinamis dengan parameter nama
// Route::get('/user2/{name}', function($name){
//     echo "User Name: " . $name;
// });

// //Route Dinamis dengan opsional parameter nama
// Route::get('/user3/{name?}', function($name = 'Tamu'){
//     echo "User Name: " . $name;
// });

// //Route Dinamis dengan parameter nama dan id
// Route::get('/user4/{id}/{name}', function($id, $name){
//     echo "User ID: " . $id;
//     echo "<br>";
//     echo "User Name: " . $name;
// });

// //Router dengan metode POST
// Route::post('/simpan', function(){
//     echo "Data berhasil disimpan";
// });

// //Router dengan metode PUT
// Route::put('/update/{id}', function($id){
//     echo "Data berhasil diperbarui dengan ID: " . $id;
// });

// //Router dengan metode PATCH
// Route::patch('/update2/{id}', function($id){
//     echo "Data berhasil diperbarui dengan ID: " . $id;
// });

// //Router dengan metode DELETE
// Route::delete('/hapus/{id}', function($id){
//     echo "Data berhasil dihapus dengan ID: " . $id;
// });

// //Route untuk menampilkan halaman test_method
// Route::get('/test-method', function(){
//     return view('test_method');
// });

// //Manampilkan halaman profil
// Route::get('/profil', function(){
//     return view("profile");
// });

// //Gunakan . untuk memisahkan folder dgn view
// // Route::get('/detailproduk', function(){
// //     return view("produk.detail");
// // });

// //mengirim data ke view
// // Route::get('/detailproduk/{name}', function($name){
// //     return view("produk.detail", 
// //         ['product_name' => $name, 
// //         'id'=> 101, 
// //         'color' => 'Silver',
// //         'stock' => 12
// //         ]
// //     );
// // });

// // Route::get('/produk/', function(){
// //     return view('produk.index');
// // });
// // Route::get('/produk/create', function(){
// //     return view('produk.create');
// // });
// // Route::get('/produk/search', function(){
// //     return view('produk.search');
// // });
// // Route::get('/produk/detail', function(){
// //     return view('produk.detail');
// // });

// use App\Http\Controllers\ProductController;
// //php artisan make:controller ProductController --resource
// Route::resource('/produk', ProductController::class);
// Route::get('/produk/search', ProductController::class.'@search');

// //Suplier
// // Route::get('/supplier/', function(){
// //     return view('supplier.index');
// // });

// //php artisan make:controller SupplierController --resource
// use App\Http\Controllers\SupplierController;
// Route::resource('/supplier', SupplierController::class);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
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
    ->name('login') // nama route ini WAJIB 'login' agar middleware auth berfungsi
    ->middleware('guest');
// Proses login
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest');
// Proses logout (gunakan POST untuk keamanan, bukan GET)
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth'); // hanya bisa diakses jika SUDAH login

// ==================== ROUTE YANG DILINDUNGI ====================
// Semua route di dalam group ini hanya bisa diakses jika sudah login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/produk', ProductController::class);
    Route::get('/produk/search', ProductController::class.'@search');
    //Daftarkan Route Lainnya di Sini :
    // - Route Supplier
    Route::resource('/supplier', SupplierController::class);
});