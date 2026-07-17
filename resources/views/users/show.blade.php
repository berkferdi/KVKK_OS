@extends('layouts.admin')

@section('title', $user->name.' — KVKK 360')
@section('page_title', $user->name)
@section('page_subtitle', $user->email)

@section('page_actions')
    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
    @can('delete', $user)
        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline"
              onsubmit="return confirm('Kullanıcı silinsin mi?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-outline-danger" type="submit">Sil</button>
        </form>
    @endcan
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Telefon</dt><dd class="col-sm-9">{{ $user->phone ?: '—' }}</dd>
            <dt class="col-sm-3">Durum</dt>
            <dd class="col-sm-9">{{ $user->is_active ? 'Aktif' : 'Pasif' }}</dd>
            <dt class="col-sm-3">Roller</dt>
            <dd class="col-sm-9">
                @forelse ($user->roles as $role)
                    <span class="badge text-bg-secondary">{{ $role->name }}</span>
                @empty
                    —
                @endforelse
            </dd>
            <dt class="col-sm-3">Son giriş</dt>
            <dd class="col-sm-9">{{ $user->last_login_at?->format('d.m.Y H:i') ?: '—' }}</dd>
        </dl>
    </div>
</div>
@endsection
