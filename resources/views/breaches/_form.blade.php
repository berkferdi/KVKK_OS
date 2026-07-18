@php
    /** @var \App\Domain\Breaches\Models\DataBreach|null $breach */
    $breach = $breach ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="title">Başlık *</label>
        <input type="text" name="title" id="title" class="form-control" required value="{{ old('title', $breach?->title) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="breach_code">Kod</label>
        <input type="text" name="breach_code" id="breach_code" class="form-control" value="{{ old('breach_code', $breach?->breach_code) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $breach?->status?->value ?? 'investigating') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="breach_type">İhlal türü</label>
        <select name="breach_type" id="breach_type" class="form-select">
            @foreach ($breachTypes as $type)
                <option value="{{ $type->value }}" @selected(old('breach_type', $breach?->breach_type?->value ?? 'confidentiality') === $type->value)>{{ $type->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="severity">Önem</label>
        <select name="severity" id="severity" class="form-select">
            @foreach ($severities as $severity)
                <option value="{{ $severity->value }}" @selected(old('severity', $breach?->severity?->value ?? 'medium') === $severity->value)>{{ $severity->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="branch_id">Şube</label>
        <select name="branch_id" id="branch_id" class="form-select">
            <option value="">—</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $breach?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="assigned_to_name">Sorumlu</label>
        <input type="text" name="assigned_to_name" id="assigned_to_name" class="form-control" value="{{ old('assigned_to_name', $breach?->assigned_to_name) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="discovered_at">Tespit</label>
        <input type="datetime-local" name="discovered_at" id="discovered_at" class="form-control" value="{{ old('discovered_at', $breach?->discovered_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="occurred_at">Oluşma (tahmini)</label>
        <input type="datetime-local" name="occurred_at" id="occurred_at" class="form-control" value="{{ old('occurred_at', $breach?->occurred_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="affected_subjects_count">Etkilenen kişi sayısı</label>
        <input type="number" name="affected_subjects_count" id="affected_subjects_count" class="form-control" min="0" value="{{ old('affected_subjects_count', $breach?->affected_subjects_count) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="authority_notification_due_at">Kurum bildirim son tarihi</label>
        <input type="datetime-local" name="authority_notification_due_at" id="authority_notification_due_at" class="form-control" value="{{ old('authority_notification_due_at', $breach?->authority_notification_due_at?->format('Y-m-d\TH:i')) }}" placeholder="Boş: tespit +72 saat">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="authority_notified_at">Kuruma bildirildi</label>
        <input type="datetime-local" name="authority_notified_at" id="authority_notified_at" class="form-control" value="{{ old('authority_notified_at', $breach?->authority_notified_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="subjects_notified_at">İlgili kişilere bildirildi</label>
        <input type="datetime-local" name="subjects_notified_at" id="subjects_notified_at" class="form-control" value="{{ old('subjects_notified_at', $breach?->subjects_notified_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="subjects_notification_required" id="subjects_notification_required" class="form-check-input" value="1" @checked(old('subjects_notification_required', $breach?->subjects_notification_required))>
            <label class="form-check-label" for="subjects_notification_required">İlgili kişi bildirimi gerekli</label>
        </div>
    </div>
    <div class="col-md-8">
        <label class="form-label" for="data_categories">Etkilenen veri kategorileri</label>
        <input type="text" name="data_categories" id="data_categories" class="form-control" value="{{ old('data_categories', $breach?->data_categories) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="description">İhlal açıklaması</label>
        <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $breach?->description) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="consequences">Olası sonuçlar</label>
        <textarea name="consequences" id="consequences" rows="3" class="form-control">{{ old('consequences', $breach?->consequences) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="measures_taken">Alınan tedbirler</label>
        <textarea name="measures_taken" id="measures_taken" rows="3" class="form-control">{{ old('measures_taken', $breach?->measures_taken) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="root_cause">Kök neden</label>
        <textarea name="root_cause" id="root_cause" rows="3" class="form-control">{{ old('root_cause', $breach?->root_cause) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $breach?->notes) }}</textarea>
    </div>
</div>
