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
    <h1 class="mb-4">{{ $title }}</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @can('create-product')
        <div class="mb-3">
            <a href="{{ route('produk.create') }}" class="btn btn-primary">Tambah Produk</a>
        </div>
    @endcan
    @cannot('create-product')
        <span class="badge bg-secondary">User tidak diizinkan menambah produk</span>
    @endcannot

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aktif</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- @for ($i = 0; $i < count($products); $i++)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $products[$i]['name'] }}</td>
                        <td>Rp {{ number_format($products[$i]['price'], 2, ',', '.') }}</td>
                        <td>
                            <a href="{{ url('/produk/' . $products[$i]['id']) }}" class="btn btn-sm btn-info">Detail</a>
                            <a href="{{ url('/produk/' . $products[$i]['id'] . '/edit') }}"
                                class="btn btn-sm btn-primary">Edit</a>
                        </td>
                    </tr>
                @endfor --}}
                @forelse ($products as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                        <td>{{ $item->name }}</td>
                        <td>Rp {{ number_format($item->price, 2, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status === 'new' ? 'success' : 'secondary' }}">
                                {{ $item->status === 'new' ? 'Baru' : 'Bekas' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">
                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            {{-- <a href="{{ url('/produk/' . $item->id) }}" class="btn btn-sm btn-info">Detail</a>
                            <a href="{{ url('/produk/' . $item->id . '/edit') }}"
                                class="btn btn-sm btn-primary">Edit</a> --}}
                            <a href="{{ route('produk.show', $item->id) }}" class="btn btn-sm btn-info">Detail</a>
                            <a href="{{ route('produk.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('produk.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                            </form>

                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteModal{{ $item->id }}">
                                Hapus
                            </button>

                            <!-- Modal Konfirmasi Delete -->
                            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1"
                                aria-labelledby="deleteModalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $item->id }}">
                                                Konfirmasi Hapus
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="mb-0">Apakah Anda yakin ingin menghapus produk
                                                <strong>{{ $item->name }}</strong>?
                                            </p>
                                            <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('produk.destroy', $item->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        <div>
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection