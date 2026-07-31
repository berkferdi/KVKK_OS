@php
    /** @var \App\Domain\Cookies\Models\SiteCookie|null $cookie */
    $cookie = $cookie ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-5">
        <label class="form-label" for="name">Çerez adı *</label>
        <input type="text" name="name" id="name" class="form-control" required placeholder="_ga" value="{{ old('name', $cookie?->name) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="cookie_code">Kod</label>
        <input type="text" name="cookie_code" id="cookie_code" class="form-control" value="{{ old('cookie_code', $cookie?->cookie_code) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="category">Kategori</label>
        <select name="category" id="category" class="form-select">
            @foreach ($categories as $category)
                <option value="{{ $category->value }}" @selected(old('category', $cookie?->category?->value ?? 'necessary') === $category->value)>{{ $category->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="provider">Sağlayıcı</label>
        <input type="text" name="provider" id="provider" class="form-control" value="{{ old('provider', $cookie?->provider) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="website_id">Web sitesi</label>
        <select name="website_id" id="website_id" class="form-select">
            <option value="">—</option>
            @foreach ($websites as $site)
                <option value="{{ $site->id }}" @selected((string) old('website_id', $cookie?->website_id) === (string) $site->id)>{{ $site->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $cookie?->status?->value ?? 'active') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="domain">Domain</label>
        <input type="text" name="domain" id="domain" class="form-control" value="{{ old('domain', $cookie?->domain) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="duration">Süre (metin)</label>
        <input type="text" name="duration" id="duration" class="form-control" placeholder="2 yıl / oturum" value="{{ old('duration', $cookie?->duration) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="duration_days">Süre (gün)</label>
        <input type="number" name="duration_days" id="duration_days" class="form-control" min="0" value="{{ old('duration_days', $cookie?->duration_days) }}">
    </div>
    <div class="col-12">
        <label class="form-label" for="purpose">Amaç</label>
        <textarea name="purpose" id="purpose" rows="2" class="form-control">{{ old('purpose', $cookie?->purpose) }}</textarea>
    </div>
    <div class="col-md-6 d-flex align-items-end gap-4">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_third_party" id="is_third_party" class="form-check-input" value="1" @checked(old('is_third_party', $cookie?->is_third_party))>
            <label class="form-check-label" for="is_third_party">Üçüncü taraf</label>
        </div>
        <div class="form-check mb-2">
            <input type="checkbox" name="requires_consent" id="requires_consent" class="form-check-input" value="1" @checked(old('requires_consent', $cookie?->requires_consent ?? true))>
            <label class="form-check-label" for="requires_consent">Rıza gerektirir</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $cookie?->notes) }}</textarea>
    </div>
</div>
