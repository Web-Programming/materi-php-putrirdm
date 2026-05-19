<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::count();
        $barangTersedia = Barang::where('status', 1)->count();
        $barangHabis = Barang::where('status', 0)->count();
        $nilaiStok = 'Rp ' . number_format(Barang::sum('harga'), 0, ',', '.');
        $barangTerbaru = Barang::latest()->take(5)->get();
        return view('dashboard', compact(
            'totalBarang',
            'barangTersedia',
            'barangHabis',
            'nilaiStok',
            'barangTerbaru'
        ));
    }
}
