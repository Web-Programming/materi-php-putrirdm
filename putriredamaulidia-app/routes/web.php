<?php

use Illuminate\Support\Facades\Route;

//Route ke halaman utama (home)
Route::get('/', function () {
    echo "Hallo, Nama Saya Pak JR";
    //return view('welcome');
});
//Route ke halaman alamat
Route::get('/alamat', function(){
    echo "Jalan Rajawali 14. Palembang";
});

//Route ke halaman path1/path2/detail
Route::get('/path1/path2/detail', function(){
    echo "Jalan Rajawali 14";
    echo "<br>";
    echo "Rt. 01 Rw. 02";
    echo "<br>";
    echo "Kecamatan Alang-Alang Lebar";
    echo "<br>";
    echo "Kota Palembang";
    echo "<br>";
    echo "Provinsi Sumatera Selatan";
});

//Route Dinamis dengan parameter id
Route::get('/user/{id}', function($id){
    echo "User ID: " . $id;
});

//Route Dinamis dengan parameter nama
Route::get('/user2/{name}', function($name){
    echo "User Name: " . $name;
});

//Route Dinamis dengan opsional parameter nama
Route::get('/user3/{name?}', function($name = 'Tamu'){
    echo "User Name: " . $name;
});

//Route Dinamis dengan parameter nama dan id
Route::get('/user4/{id}/{name}', function($id, $name){
    echo "User ID: " . $id;
    echo "<br>";
    echo "User Name: " . $name;
});

//Router dengan metode POST
Route::post('/simpan', function(){
    echo "Data berhasil disimpan";
});

//Router dengan metode PUT
Route::put('/update/{id}', function($id){
    echo "Data berhasil diperbarui dengan ID: " . $id;
});

//Router dengan metode PATCH
Route::patch('/update2/{id}', function($id){
    echo "Data berhasil diperbarui dengan ID: " . $id;
});

//Router dengan metode DELETE
Route::delete('/hapus/{id}', function($id){
    echo "Data berhasil dihapus dengan ID: " . $id;
});
//Route untuk menampilkan halaman test_method
Route::get('/test-method', function(){
    return view('test_method');
});

use App\Http\Controllers\ProductController;
//php artisan make:controller ProductController --resource
Route::resource('/produk', ProductController::class);
Route::get('/produk/search', ProductController::class.'@search');

//Suplier
// Route::get('/supplier/', function(){
//     return view('supplier.index');
// });

//php artisan make:controller SupplierController --resource

Route::resource('/supplier', SupplierController::class);
use App\Http\Controllers\SupplierController;
//php artisan make:controller ProductController --resource
Route::resource('/produk', SupplierController::class);
Route::get('/produk/search', SupplierController::class.'@search');

//Suplier
// Route::get('/supplier/', function(){
//     return view('supplier.index');
// });

//php artisan make:controller SupplierController --resource
Route::resource('/supplier', SupplierController::class);