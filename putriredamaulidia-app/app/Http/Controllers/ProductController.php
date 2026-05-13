<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Daftar Produk";
        // $products = [
        //     ['id' => 1, 'name' => 'Laptop', 'price' => 7500000],
        //     ['id' => 2, 'name' => 'Mouse', 'price' => 150000],
        //     ['id' => 3, 'name' => 'Keyboard', 'price' => 300000],
        //     ['id' => 4, 'name' => 'Monitor', 'price' => 2500000],
        // ];
        
        //$products = Product::all(); //cara 1
        //$products = DB::select('SELECT * FROM products'); //cara 2
        $products = DB::table('products')->get(); //cara 3

        return view('produk.index', compact('title', 'products'));
        //return view('produk.index', [
        //    'products' => $products, 
        //    'title' => $title
        //]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produk.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = "Detail Produk";
        $product = ['id' => $id, 'name' => 'Monitor', 'price' => 2500000];
        return view('produk.detail', compact('id', 'product', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('produk.edit', ['id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    function search(Request $request)
    {
        return view('produk.search');
    }
}