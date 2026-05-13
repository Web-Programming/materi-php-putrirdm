@extends('app.master')

@section('title', $title)

@section('sidebar')
    @parent
@section('submenu-supplier')
    <a href="/supplier" class="list-group-item list-group-item-action ps-4">Daftar Supplier</a>
    <a href="/supplier/create"
        class="list-group-item list-group-item-action ps-4 
         {{ request()->is('supplier/create') ? 'active' : '' }}">Tambah
        Supplier</a>
    <a href="/supplier/search"
        class="list-group-item list-group-item-action ps-4 
         {{ request()->is('supplier/search') ? 'active' : '' }}">Cari
        Supplier</a>
@endsection
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $title }}</h1>
        <a href="{{ url('/supplier') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informasi Supplier</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>ID Supplier:</strong>
                </div>
                <div class="col-md-9">
                    {{ $supplier['id'] }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Nama Supplier:</strong>
                </div>
                <div class="col-md-9">
                    {{ $supplier['name'] }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Telepon:</strong>
                </div>
                <div class="col-md-9">
                    <i class="bi bi-telephone"></i> {{ $supplier['phone'] }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <strong>Alamat:</strong>
                </div>
                <div class="col-md-9">
                    <i class="bi bi-geo-alt"></i> {{ $supplier['address'] }}
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ url('/supplier/' . $supplier['id'] . '/edit') }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit Supplier
            </a>
            <a href="{{ url('/supplier') }}" class="btn btn-secondary">
                <i class="bi bi-list"></i> Lihat Semua Supplier
            </a>
        </div>
    </div>
</div>
@endsection