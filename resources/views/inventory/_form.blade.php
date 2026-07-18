@php
    /** @var \App\Domain\Inventory\Models\ProcessingActivity|null $activity */
    $activity = $activity ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="name">Faaliyet adı *</label>
        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $activity?->name) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="code">Kod</label>
        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $activity?->code) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="purpose">Amaç</label>
        <input type="text" name="purpose" id="purpose" class="form-control" value="{{ old('purpose', $activity?->purpose) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="legal_basis">Hukuki sebep</label>
        <select name="legal_basis" id="legal_basis" class="form-select">
            <option value="">—</option>
            @foreach ($legalBases as $basis)
                <option value="{{ $basis->value }}" @selected(old('legal_basis', $activity?->legal_basis?->value) === $basis->value)>
                    {{ $basis->value }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $activity?->status?->value ?? 'draft') === $status->value)>
                    {{ $status->value }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="branch_id">Şube</label>
        <select name="branch_id" id="branch_id" class="form-select">
            <option value="">—</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $activity?->branch_id) === (string) $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label" for="legal_basis_detail">Hukuki sebep detayı</label>
        <input type="text" name="legal_basis_detail" id="legal_basis_detail" class="form-control"
               value="{{ old('legal_basis_detail', $activity?->legal_basis_detail) }}">
    </div>
    <div class="col-12">
        <label class="form-label" for="description">Açıklama</label>
        <textarea name="description" id="description" rows="2" class="form-control">{{ old('description', $activity?->description) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="data_categories">Veri kategorileri (virgülle)</label>
        <input type="text" name="data_categories" id="data_categories" class="form-control"
               value="{{ old('data_categories', implode(', ', $activity?->data_categories ?? [])) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="data_subject_categories">İlgili kişi grupları (virgülle)</label>
        <input type="text" name="data_subject_categories" id="data_subject_categories" class="form-control"
               value="{{ old('data_subject_categories', implode(', ', $activity?->data_subject_categories ?? [])) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="recipients">Alıcılar (virgülle)</label>
        <input type="text" name="recipients" id="recipients" class="form-control"
               value="{{ old('recipients', implode(', ', $activity?->recipients ?? [])) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="retention_period">Saklama süresi</label>
        <input type="text" name="retention_period" id="retention_period" class="form-control"
               value="{{ old('retention_period', $activity?->retention_period) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="transfer_countries">Yurt dışı ülkeler</label>
        <input type="text" name="transfer_countries" id="transfer_countries" class="form-control"
               value="{{ old('transfer_countries', $activity?->transfer_countries) }}">
    </div>
    <div class="col-12">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="cross_border_transfer" id="cross_border_transfer" value="1"
                @checked(old('cross_border_transfer', $activity?->cross_border_transfer))>
            <label class="form-check-label" for="cross_border_transfer">Yurt dışına aktarım var</label>
        </div>
        <label class="form-label" for="security_measures">Güvenlik tedbirleri</label>
        <textarea name="security_measures" id="security_measures" rows="2" class="form-control">{{ old('security_measures', $activity?->security_measures) }}</textarea>
    </div>
</div>
