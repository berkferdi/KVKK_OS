@php
    /** @var \App\Domain\Organization\Models\Company|null $company */
    $company = $company ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="trade_name">Ticari Unvan *</label>
        <input type="text" name="trade_name" id="trade_name" class="form-control" required
               value="{{ old('trade_name', $company?->trade_name) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="title">Resmi Unvan</label>
        <input type="text" name="title" id="title" class="form-control"
               value="{{ old('title', $company?->title) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="tax_number">Vergi No</label>
        <input type="text" name="tax_number" id="tax_number" class="form-control"
               value="{{ old('tax_number', $company?->tax_number) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="tax_office">Vergi Dairesi</label>
        <input type="text" name="tax_office" id="tax_office" class="form-control"
               value="{{ old('tax_office', $company?->tax_office) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="mersis_number">MERSİS</label>
        <input type="text" name="mersis_number" id="mersis_number" class="form-control"
               value="{{ old('mersis_number', $company?->mersis_number) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="sgk_registration_number">SGK Sicil No</label>
        <input type="text" name="sgk_registration_number" id="sgk_registration_number" class="form-control"
               value="{{ old('sgk_registration_number', $company?->sgk_registration_number) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="trade_registry_number">Ticaret Sicil No</label>
        <input type="text" name="trade_registry_number" id="trade_registry_number" class="form-control"
               value="{{ old('trade_registry_number', $company?->trade_registry_number) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="nace_code">NACE</label>
        <input type="text" name="nace_code" id="nace_code" class="form-control"
               value="{{ old('nace_code', $company?->nace_code) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="email">E-posta</label>
        <input type="email" name="email" id="email" class="form-control"
               value="{{ old('email', $company?->email) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="kvkk_email">KVKK E-posta</label>
        <input type="email" name="kvkk_email" id="kvkk_email" class="form-control"
               value="{{ old('kvkk_email', $company?->kvkk_email) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="kep_address">KEP</label>
        <input type="email" name="kep_address" id="kep_address" class="form-control"
               value="{{ old('kep_address', $company?->kep_address) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="phone">Telefon</label>
        <input type="text" name="phone" id="phone" class="form-control"
               value="{{ old('phone', $company?->phone) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="website_url">Web</label>
        <input type="text" name="website_url" id="website_url" class="form-control"
               value="{{ old('website_url', $company?->website_url) }}">
    </div>
    <div class="col-md-8">
        <label class="form-label" for="address">Adres</label>
        <input type="text" name="address" id="address" class="form-control"
               value="{{ old('address', $company?->address) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label" for="city">İl</label>
        <input type="text" name="city" id="city" class="form-control"
               value="{{ old('city', $company?->city) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label" for="district">İlçe</label>
        <input type="text" name="district" id="district" class="form-control"
               value="{{ old('district', $company?->district) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label" for="postal_code">Posta Kodu</label>
        <input type="text" name="postal_code" id="postal_code" class="form-control"
               value="{{ old('postal_code', $company?->postal_code) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label" for="country">Ülke</label>
        <input type="text" name="country" id="country" class="form-control"
               value="{{ old('country', $company?->country ?? 'Türkiye') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="founded_at">Kuruluş Tarihi</label>
        <input type="date" name="founded_at" id="founded_at" class="form-control"
               value="{{ old('founded_at', optional($company?->founded_at)->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="authorized_person">Yetkili</label>
        <input type="text" name="authorized_person" id="authorized_person" class="form-control"
               value="{{ old('authorized_person', $company?->authorized_person) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="authorized_title">Yetkili Ünvanı</label>
        <input type="text" name="authorized_title" id="authorized_title" class="form-control"
               value="{{ old('authorized_title', $company?->authorized_title) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label" for="employee_count">Personel</label>
        <input type="number" name="employee_count" id="employee_count" class="form-control" min="0"
               value="{{ old('employee_count', $company?->employee_count) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $company?->status?->value ?? 'draft') === $status->value)>
                    {{ $status->value }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label" for="activity_summary">Faaliyet Özeti</label>
        <textarea name="activity_summary" id="activity_summary" rows="3" class="form-control">{{ old('activity_summary', $company?->activity_summary) }}</textarea>
    </div>
    <div class="col-md-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="has_camera" id="has_camera" value="1"
                @checked(old('has_camera', $company?->has_camera))>
            <label class="form-check-label" for="has_camera">Kamera var</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="has_website" id="has_website" value="1"
                @checked(old('has_website', $company?->has_website))>
            <label class="form-check-label" for="has_website">Web sitesi var</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="has_cookies" id="has_cookies" value="1"
                @checked(old('has_cookies', $company?->has_cookies))>
            <label class="form-check-label" for="has_cookies">Çerez işleniyor</label>
        </div>
    </div>
</div>
