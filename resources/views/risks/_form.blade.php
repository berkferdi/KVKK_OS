@php
    /** @var \App\Domain\Risk\Models\RiskAssessment|null $risk */
    $risk = $risk ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="title">Başlık *</label>
        <input type="text" name="title" id="title" class="form-control" required value="{{ old('title', $risk?->title) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="code">Kod</label>
        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $risk?->code) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="processing_activity_id">İlişkili envanter</label>
        <select name="processing_activity_id" id="processing_activity_id" class="form-select">
            <option value="">—</option>
            @foreach ($activities as $activity)
                <option value="{{ $activity->id }}" @selected((string) old('processing_activity_id', $risk?->processing_activity_id) === (string) $activity->id)>
                    {{ $activity->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="asset_type">Varlık tipi</label>
        <input type="text" name="asset_type" id="asset_type" class="form-control" value="{{ old('asset_type', $risk?->asset_type) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $risk?->status?->value ?? 'open') === $status->value)>
                    {{ $status->value }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="threat">Tehdit</label>
        <input type="text" name="threat" id="threat" class="form-control" value="{{ old('threat', $risk?->threat) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="vulnerability">Zafiyet</label>
        <input type="text" name="vulnerability" id="vulnerability" class="form-control" value="{{ old('vulnerability', $risk?->vulnerability) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="likelihood">Olasılık (1-5) *</label>
        <input type="number" name="likelihood" id="likelihood" class="form-control" min="1" max="5" required
               value="{{ old('likelihood', $risk?->likelihood ?? 1) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="impact">Etki (1-5) *</label>
        <input type="number" name="impact" id="impact" class="form-control" min="1" max="5" required
               value="{{ old('impact', $risk?->impact ?? 1) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="owner_name">Sorumlu</label>
        <input type="text" name="owner_name" id="owner_name" class="form-control" value="{{ old('owner_name', $risk?->owner_name) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="review_date">Gözden geçirme</label>
        <input type="date" name="review_date" id="review_date" class="form-control"
               value="{{ old('review_date', $risk?->review_date?->format('Y-m-d')) }}">
    </div>
    <div class="col-12">
        <label class="form-label" for="description">Açıklama</label>
        <textarea name="description" id="description" rows="2" class="form-control">{{ old('description', $risk?->description) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="existing_controls">Mevcut kontroller</label>
        <textarea name="existing_controls" id="existing_controls" rows="2" class="form-control">{{ old('existing_controls', $risk?->existing_controls) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="mitigation_plan">Azaltım planı</label>
        <textarea name="mitigation_plan" id="mitigation_plan" rows="2" class="form-control">{{ old('mitigation_plan', $risk?->mitigation_plan) }}</textarea>
    </div>
</div>
