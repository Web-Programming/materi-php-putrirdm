<?php

namespace App\Http\Controllers;

use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Product::count();
        $barangTersedia = Product::where('is_active', 1)->count();
        $barangHabis = Product::where('is_active', 0)->count();
        $nilaiStok = 'Rp ' . number_format(Product::sum('price'), 0, ',', '.');
        $barangTerbaru = Product::latest()->take(5)->get();
        return view('dashboard', compact(
            'totalBarang',
            'barangTersedia',
            'barangHabis',
            'nilaiStok',
            'barangTerbaru'
        ));
    }
}