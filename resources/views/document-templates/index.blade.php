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
