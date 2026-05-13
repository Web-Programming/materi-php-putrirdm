@extends('app.master')

@section('title', $title)

@section('sidebar')
    @parent
@section('submenu-supplier')
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
    <h1 class="mb-4">{{ $title }}</h1>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Supplier</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suppliers as $index => $supplier)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $supplier['name'] }}</td>
                        <td>{{ $supplier['phone'] }}</td>
                        <td>{{ $supplier['address'] }}</td>
                        <td>
                            <a href="{{ url('/supplier/' . $supplier['id']) }}" class="btn btn-sm btn-info">Detail</a>
                            <a href="{{ url('/supplier/' . $supplier['id'] . '/edit') }}"
                                class="btn btn-sm btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection