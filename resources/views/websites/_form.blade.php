@php
    /** @var \App\Domain\Websites\Models\Website|null $website */
    $website = $website ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Ad *</label>
        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $website?->name) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="website_code">Kod</label>
        <input type="text" name="website_code" id="website_code" class="form-control" value="{{ old('website_code', $website?->website_code) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $website?->status?->value ?? 'active') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label" for="url">URL *</label>
        <input type="url" name="url" id="url" class="form-control" required placeholder="https://" value="{{ old('url', $website?->url) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="platform">Platform</label>
        <input type="text" name="platform" id="platform" class="form-control" placeholder="WordPress / custom" value="{{ old('platform', $website?->platform) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="hosting_provider">Hosting</label>
        <input type="text" name="hosting_provider" id="hosting_provider" class="form-control" value="{{ old('hosting_provider', $website?->hosting_provider) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="privacy_policy_url">Gizlilik politikası URL</label>
        <input type="url" name="privacy_policy_url" id="privacy_policy_url" class="form-control" value="{{ old('privacy_policy_url', $website?->privacy_policy_url) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="privacy_policy_published_at">Politika yayın tarihi</label>
        <input type="date" name="privacy_policy_published_at" id="privacy_policy_published_at" class="form-control" value="{{ old('privacy_policy_published_at', $website?->privacy_policy_published_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-8 d-flex align-items-end gap-3 flex-wrap">
        <div class="form-check mb-2">
            <input type="checkbox" name="ssl_enabled" id="ssl_enabled" class="form-check-input" value="1" @checked(old('ssl_enabled', $website?->ssl_enabled ?? true))>
            <label class="form-check-label" for="ssl_enabled">SSL</label>
        </div>
        <div class="form-check mb-2">
            <input type="checkbox" name="privacy_policy_published" id="privacy_policy_published" class="form-check-input" value="1" @checked(old('privacy_policy_published', $website?->privacy_policy_published))>
            <label class="form-check-label" for="privacy_policy_published">Gizlilik politikası yayında</label>
        </div>
        <div class="form-check mb-2">
            <input type="checkbox" name="uses_cookies" id="uses_cookies" class="form-check-input" value="1" @checked(old('uses_cookies', $website?->uses_cookies))>
            <label class="form-check-label" for="uses_cookies">Çerez kullanır</label>
        </div>
    </div>
    <div class="col-12 d-flex gap-3 flex-wrap">
        <div class="form-check">
            <input type="checkbox" name="has_contact_form" id="has_contact_form" class="form-check-input" value="1" @checked(old('has_contact_form', $website?->has_contact_form))>
            <label class="form-check-label" for="has_contact_form">İletişim formu</label>
        </div>
        <div class="form-check">
            <input type="checkbox" name="has_newsletter" id="has_newsletter" class="form-check-input" value="1" @checked(old('has_newsletter', $website?->has_newsletter))>
            <label class="form-check-label" for="has_newsletter">Bülten</label>
        </div>
        <div class="form-check">
            <input type="checkbox" name="has_user_accounts" id="has_user_accounts" class="form-check-input" value="1" @checked(old('has_user_accounts', $website?->has_user_accounts))>
            <label class="form-check-label" for="has_user_accounts">Üye hesabı</label>
        </div>
        <div class="form-check">
            <input type="checkbox" name="has_payment" id="has_payment" class="form-check-input" value="1" @checked(old('has_payment', $website?->has_payment))>
            <label class="form-check-label" for="has_payment">Ödeme</label>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="data_collected">Toplanan veri kategorileri</label>
        <textarea name="data_collected" id="data_collected" rows="2" class="form-control">{{ old('data_collected', $website?->data_collected) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $website?->notes) }}</textarea>
    </div>
</div>
