@php
    /** @var \App\Domain\Suppliers\Models\Supplier|null $supplier */
    $supplier = $supplier ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Unvan *</label>
        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $supplier?->name) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="supplier_code">Tedarikçi kodu</label>
        <input type="text" name="supplier_code" id="supplier_code" class="form-control" value="{{ old('supplier_code', $supplier?->supplier_code) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="supplier_type">Tür</label>
        <select name="supplier_type" id="supplier_type" class="form-select">
            @foreach ($supplierTypes as $type)
                <option value="{{ $type->value }}" @selected(old('supplier_type', $supplier?->supplier_type?->value ?? 'services') === $type->value)>{{ $type->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="contact_person">İlgili kişi</label>
        <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ old('contact_person', $supplier?->contact_person) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="tax_number">Vergi No</label>
        <input type="text" name="tax_number" id="tax_number" class="form-control" value="{{ old('tax_number', $supplier?->tax_number) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="branch_id">Şube</label>
        <select name="branch_id" id="branch_id" class="form-select">
            <option value="">—</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $supplier?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="email">E-posta</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $supplier?->email) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="phone">Telefon</label>
        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $supplier?->phone) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $supplier?->status?->value ?? 'active') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="address">Adres</label>
        <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $supplier?->address) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="city">İl</label>
        <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $supplier?->city) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="district">İlçe</label>
        <input type="text" name="district" id="district" class="form-control" value="{{ old('district', $supplier?->district) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="contract_start">Sözleşme başlangıç</label>
        <input type="date" name="contract_start" id="contract_start" class="form-control" value="{{ old('contract_start', $supplier?->contract_start?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="contract_end">Sözleşme bitiş</label>
        <input type="date" name="contract_end" id="contract_end" class="form-control" value="{{ old('contract_end', $supplier?->contract_end?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="privacy_notice_signed_at">Aydınlatma imza</label>
        <input type="date" name="privacy_notice_signed_at" id="privacy_notice_signed_at" class="form-control" value="{{ old('privacy_notice_signed_at', $supplier?->privacy_notice_signed_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="dpa_signed_at">Veri işleme sözleşmesi</label>
        <input type="date" name="dpa_signed_at" id="dpa_signed_at" class="form-control" value="{{ old('dpa_signed_at', $supplier?->dpa_signed_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="processes_personal_data" id="processes_personal_data" class="form-check-input" value="1" @checked(old('processes_personal_data', $supplier?->processes_personal_data))>
            <label class="form-check-label" for="processes_personal_data">Kişisel veri işler</label>
        </div>
    </div>
    <div class="col-md-8">
        <label class="form-label" for="data_categories">İşlenen veri kategorileri</label>
        <input type="text" name="data_categories" id="data_categories" class="form-control" value="{{ old('data_categories', $supplier?->data_categories) }}">
    </div>
    <div class="col-12">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $supplier?->notes) }}</textarea>
    </div>
</div>
