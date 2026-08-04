@php
    /** @var \App\Domain\Customers\Models\Customer|null $customer */
    $customer = $customer ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Ad / Unvan *</label>
        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $customer?->name) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="customer_code">Müşteri kodu</label>
        <input type="text" name="customer_code" id="customer_code" class="form-control" value="{{ old('customer_code', $customer?->customer_code) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="customer_type">Tür</label>
        <select name="customer_type" id="customer_type" class="form-select">
            @foreach ($customerTypes as $type)
                <option value="{{ $type->value }}" @selected(old('customer_type', $customer?->customer_type?->value ?? 'individual') === $type->value)>{{ $type->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="contact_person">İlgili kişi</label>
        <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ old('contact_person', $customer?->contact_person) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="tax_number">Vergi / TCKN</label>
        <input type="text" name="tax_number" id="tax_number" class="form-control" value="{{ old('tax_number', $customer?->tax_number) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="branch_id">Şube</label>
        <select name="branch_id" id="branch_id" class="form-select">
            <option value="">—</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $customer?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="email">E-posta</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $customer?->email) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="phone">Telefon</label>
        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $customer?->phone) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $customer?->status?->value ?? 'active') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="address">Adres</label>
        <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $customer?->address) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="city">İl</label>
        <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $customer?->city) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="district">İlçe</label>
        <input type="text" name="district" id="district" class="form-control" value="{{ old('district', $customer?->district) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="privacy_notice_signed_at">Aydınlatma imza</label>
        <input type="date" name="privacy_notice_signed_at" id="privacy_notice_signed_at" class="form-control" value="{{ old('privacy_notice_signed_at', $customer?->privacy_notice_signed_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="consent_obtained_at">Açık rıza tarihi</label>
        <input type="date" name="consent_obtained_at" id="consent_obtained_at" class="form-control" value="{{ old('consent_obtained_at', $customer?->consent_obtained_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="marketing_consent" id="marketing_consent" class="form-check-input" value="1" @checked(old('marketing_consent', $customer?->marketing_consent))>
            <label class="form-check-label" for="marketing_consent">Pazarlama izni</label>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="data_categories">İşlenen veri kategorileri</label>
        <textarea name="data_categories" id="data_categories" rows="2" class="form-control">{{ old('data_categories', $customer?->data_categories) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $customer?->notes) }}</textarea>
    </div>
</div>
