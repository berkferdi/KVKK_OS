@php
    /** @var \App\Domain\Identity\Models\Role|null $role */
    $role = $role ?? null;
    $selected = old('permissions', $role?->permissions?->pluck('id')->all() ?? []);
@endphp

<div class="mb-3">
    <label class="form-label" for="name">Rol adı *</label>
    <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $role?->name) }}">
</div>

<label class="form-label">Yetkiler</label>
@foreach ($permissions as $group => $items)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white text-uppercase small fw-bold">{{ $group }}</div>
        <div class="card-body">
            <div class="row">
                @foreach ($items as $permission)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]"
                                   id="perm_{{ $permission->id }}" value="{{ $permission->id }}"
                                @checked(in_array($permission->id, $selected, false))>
                            <label class="form-check-label" for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach
