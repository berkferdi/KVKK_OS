@extends('layouts.admin')

@section('title', 'Roller — KVKK 360')
@section('page_title', 'Roller')
@section('page_subtitle', 'Tenant rol tanımları')

@section('page_actions')
    @can('create', App\Domain\Identity\Models\Role::class)
        <a href="{{ route('roles.create') }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Rol</a>
    @endcan
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
            <tr>
                <th>Rol</th>
                <th>Yetki sayısı</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->permissions->count() }}</td>
                    <td class="text-end">
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
                        <form method="POST" action="{{ route('roles.destroy', $role) }}" class="d-inline"
                              onsubmit="return confirm('Rol silinsin mi?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-muted py-4">Rol yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
