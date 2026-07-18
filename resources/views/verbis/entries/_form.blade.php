@php
    /** @var \App\Domain\Verbis\Models\VerbisEntry|null $entry */
    $entry = $entry ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="title">Başlık *</label>
        <input type="text" name="title" id="title" class="form-control" required value="{{ old('title', $entry?->title) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="code">Kod</label>
        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $entry?->code) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="processing_activity_id">İlişkili envanter</label>
        <select name="processing_activity_id" id="processing_activity_id" class="form-select">
            <option value="">—</option>
            @foreach ($activities as $activity)
                <option value="{{ $activity->id }}" @selected((string) old('processing_activity_id', $entry?->processing_activity_id) === (string) $activity->id)>{{ $activity->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="legal_basis">Hukuki sebep</label>
        <input type="text" name="legal_basis" id="legal_basis" class="form-control" value="{{ old('legal_basis', $entry?->legal_basis) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $entry?->status?->value ?? 'draft') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="purposes">Amaçlar</label>
        <textarea name="purposes" id="purposes" rows="2" class="form-control">{{ old('purposes', $entry?->purposes) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="data_subject_categories">İlgili kişi grupları</label>
        <textarea name="data_subject_categories" id="data_subject_categories" rows="2" class="form-control">{{ old('data_subject_categories', $entry?->data_subject_categories) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="data_categories">Veri kategorileri</label>
        <textarea name="data_categories" id="data_categories" rows="2" class="form-control">{{ old('data_categories', $entry?->data_categories) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="recipients">Alıcılar</label>
        <textarea name="recipients" id="recipients" rows="2" class="form-control">{{ old('recipients', $entry?->recipients) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="retention_period">Saklama süresi</label>
        <input type="text" name="retention_period" id="retention_period" class="form-control" value="{{ old('retention_period', $entry?->retention_period) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="transfer_countries">Yurt dışı ülkeler</label>
        <input type="text" name="transfer_countries" id="transfer_countries" class="form-control" value="{{ old('transfer_countries', $entry?->transfer_countries) }}">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="cross_border_transfer" id="cross_border_transfer" class="form-check-input" value="1" @checked(old('cross_border_transfer', $entry?->cross_border_transfer))>
            <label class="form-check-label" for="cross_border_transfer">Yurt dışı aktarım</label>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="security_measures">Güvenlik tedbirleri</label>
        <textarea name="security_measures" id="security_measures" rows="2" class="form-control">{{ old('security_measures', $entry?->security_measures) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $entry?->notes) }}</textarea>
    </div>
</div>
