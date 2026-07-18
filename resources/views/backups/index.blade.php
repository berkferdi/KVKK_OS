@extends('layouts.admin')
@section('title', 'Yedekler — KVKK 360')
@section('page_title', 'Sistem Yedekleri')
@section('page_subtitle', 'Veritabanı ZIP arşivleri')

@section('page_actions')
    <form method="POST" action="{{ route('backups.store') }}" class="d-inline">
        @csrf
        <label class="form-check form-check-inline small me-2">
            <input class="form-check-input" type="checkbox" name="include_storage" value="1">
            Depolama dahil
        </label>
        <button class="btn btn-sm text-white" style="background:#1f6f5b;" type="submit">Yedek al</button>
    </form>
@endsection

@section('content')
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
            <tr>
                <th>Tarih</th>
                <th>Durum</th>
                <th>Sürücü</th>
                <th>Boyut</th>
                <th>Tetikleyen</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($backups as $backup)
                <tr>
                    <td>{{ $backup->created_at?->format('d.m.Y H:i') }}</td>
                    <td><span class="badge text-bg-secondary">{{ $backup->status->value }}</span></td>
                    <td>{{ $backup->driver }}</td>
                    <td>{{ $backup->size_bytes !== null ? number_format($backup->size_bytes / 1024, 1).' KB' : '—' }}</td>
                    <td>{{ $backup->triggeredByUser?->name ?: 'sistem' }}</td>
                    <td class="text-end text-nowrap">
                        @if ($backup->status->value === 'completed' && $backup->path)
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('backups.download', $backup) }}">İndir</a>
                        @endif
                        <form method="POST" action="{{ route('backups.destroy', $backup) }}" class="d-inline" onsubmit="return confirm('Yedek silinsin mi?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Henüz yedek yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($backups->hasPages())
        <div class="card-footer bg-white">{{ $backups->links() }}</div>
    @endif
</div>
@endsection
