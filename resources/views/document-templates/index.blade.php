@extends('layouts.admin')
@section('title', 'Belge Şablonları — KVKK 360')
@section('page_title', 'Belge Şablonları')
@section('page_subtitle', 'Belge Motoru')
@section('page_actions')
    @can('create', App\Domain\Documents\Models\DocumentTemplate::class)
        <a href="{{ route('document-templates.create') }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Şablon</a>
    @endcan
@endsection
@section('content')
@can('create', App\Domain\Documents\Models\DocumentTemplate::class)
<div class="alert alert-warning border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <strong>Varsayılan şablon metinlerini güncelle</strong>
        <div class="small mb-0">Kamera / web / gizlilik / çerez şablonlarını tam hukuki metinlere çeker. Elle özelleştirdiğiniz şablonlara dokunmaz.</div>
    </div>
    <form method="POST" action="{{ route('document-templates.refresh-seed') }}" class="m-0"
          onsubmit="return confirm('Seed kaynaklı varsayılan şablonlar güncel tam metinlerle yenilenecek. Elle özelleştirdiğiniz şablonlara dokunulmaz. Devam?')">
        @csrf
        <button type="submit" class="btn btn-warning">Varsayılanları Yenile</button>
    </form>
</div>
@endcan
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Kod</th><th>Başlık</th><th>Kategori</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($templates as $template)
                <tr>
                    <td><code>{{ $template->code }}</code></td>
                    <td><a href="{{ route('document-templates.show', $template) }}">{{ $template->title }}</a></td>
                    <td>{{ $template->category->value }}</td>
                    <td>
                        @if($template->is_active)
                            <span class="badge text-bg-success">aktif</span>
                        @else
                            <span class="badge text-bg-secondary">pasif</span>
                        @endif
                    </td>
                    <td class="text-end"><a href="{{ route('document-templates.edit', $template) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Şablon yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($templates->hasPages())<div class="card-footer">{{ $templates->links() }}</div>@endif
</div>
@endsection
