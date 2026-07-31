@extends('layouts.admin')

@section('title', 'Kullanıcılar — KVKK 360')
@section('page_title', 'Kullanıcılar')
@section('page_subtitle', 'Tenant kullanıcıları')

@section('page_actions')
    @can('create', App\Models\User::class)
        <a href="{{ route('users.create') }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Kullanıcı</a>
    @endcan
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Ad</th>
                    <th>E-posta</th>
                    <th>Roller</th>
                    <th>Durum</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td><a href="{{ route('users.show', $user) }}">{{ $user->name }}</a></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach ($user->roles as $role)
                                <span class="badge text-bg-secondary">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if ($user->is_active)
                                <span class="badge text-bg-success">Aktif</span>
                            @else
                                <span class="badge text-bg-danger">Pasif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Kullanıcı yok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($users->hasPages())
        <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>
@endsection
