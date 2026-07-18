@php
    /** @var \App\Models\User|null $user */
    $user = $user ?? null;
    $selectedRoles = old('roles', $user?->roles?->pluck('id')->all() ?? []);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Ad Soyad *</label>
        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $user?->name) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="email">E-posta *</label>
        <input type="email" name="email" id="email" class="form-control" required value="{{ old('email', $user?->email) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="phone">Telefon</label>
        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user?->phone) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="password">Şifre {{ $user ? '(opsiyonel)' : '*' }}</label>
        <input type="password" name="password" id="password" class="form-control" @if(!$user) required @endif>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="password_confirmation">Şifre Tekrar</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" @if(!$user) required @endif>
    </div>
    <div class="col-md-12">
        <label class="form-label">Roller</label>
        <div class="row">
            @forelse ($roles as $role)
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" id="role_{{ $role->id }}"
                               value="{{ $role->id }}" @checked(in_array($role->id, $selectedRoles, false))>
                        <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted small">Henüz rol yok. Önce Rol oluşturun.</div>
            @endforelse
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                @checked(old('is_active', $user?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
    </div>
    @if (!$user)
        <div class="col-md-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_owner" id="is_owner" value="1"
                    @checked(old('is_owner'))>
                <label class="form-check-label" for="is_owner">Tenant sahibi</label>
            </div>
        </div>
    @endif
</div>
