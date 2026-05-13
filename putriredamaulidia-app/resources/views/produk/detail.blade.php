@extends('app.master')
@section('title', $title)
@section('sidebar')
    @parent
@section('submenu-produk')
    <a href="/produk/create"
        class="list-group-item list-group-item-action ps-4 
        {{ request()->is('produk/create') ? 'active' : '' }}">Tambah
        Produk</a>
    <a href="/produk/search"
        class="list-group-item list-group-item-action ps-4 
        {{ request()->is('produk/search') ? 'active' : '' }}">Cari
        Produk</a>
@endsection
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $title }}</h1>
        <a href="{{ url('/produk') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informasi Produk</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>ID Produk:</strong>
                </div>
                <div class="col-md-9">
                    {{ $product['id'] }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Nama Produk:</strong>
                </div>
                <div class="col-md-9">
                    {{ $product['name'] }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Harga:</strong>
                </div>
                <div class="col-md-9">
                    <span class="badge bg-success fs-6">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ url('/produk/' . $product['id'] . '/edit') }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit Produk
            </a>
            <a href="{{ url('/produk') }}" class="btn btn-secondary">
                <i class="bi bi-list"></i> Lihat Semua Produk
            </a>
        </div>
    </div>
</div>
@endsection