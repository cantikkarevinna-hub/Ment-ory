@extends('layouts.app')

@section('title', 'Data Sampah (Terhapus)')

@section('content')
<!-- Header Banner Data Sampah -->
<div class="main-banner d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold m-0">Data Sampah (Terhapus)</h2>
        <div class="small text-white-50 mt-1">* Data di halaman ini disimpan sebagai riwayat barang yang telah dihapus.</div>
    </div>
    <a href="{{ route('inventory.index') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-primary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Section Table Barang Terhapus -->
<div class="content-card">
    <h5 class="fw-bold text-dark mb-3">List barang yang terhapus</h5>

    <div class="table-box">
        <table class="table table-custom mb-0">
            <thead>
                <tr class="text-secondary small">
                    <th>Nama Barang</th>
                    <th>NO. Seri</th>
                    <th>Barcode</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deletedItems as $item)
                <tr>
                    <td class="fw-bold">{{ $item->nama_barang }}</td>
                    <td>{{ $item->no_seri }}</td>
                    <td>{{ $item->barcode }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>
                        <form action="{{ route('inventory.restore', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">Belum ada data barang yang dihapus.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection