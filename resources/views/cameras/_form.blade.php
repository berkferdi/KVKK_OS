@php
    /** @var \App\Domain\Cameras\Models\Camera|null $camera */
    $camera = $camera ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Kamera adı *</label>
        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $camera?->name) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="camera_code">Kod</label>
        <input type="text" name="camera_code" id="camera_code" class="form-control" value="{{ old('camera_code', $camera?->camera_code) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="camera_type">Tür</label>
        <select name="camera_type" id="camera_type" class="form-select">
            @foreach ($cameraTypes as $type)
                <option value="{{ $type->value }}" @selected(old('camera_type', $camera?->camera_type?->value ?? 'indoor') === $type->value)>{{ $type->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="location">Konum</label>
        <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $camera?->location) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="coverage_area">Kapsama alanı</label>
        <input type="text" name="coverage_area" id="coverage_area" class="form-control" value="{{ old('coverage_area', $camera?->coverage_area) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="branch_id">Şube</label>
        <select name="branch_id" id="branch_id" class="form-select">
            <option value="">—</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $camera?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="retention_days">Saklama (gün)</label>
        <input type="number" name="retention_days" id="retention_days" class="form-control" min="1" max="3650" value="{{ old('retention_days', $camera?->retention_days) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="storage_location">Depolama</label>
        <input type="text" name="storage_location" id="storage_location" class="form-control" placeholder="NVR / Bulut" value="{{ old('storage_location', $camera?->storage_location) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="installed_at">Kurulum</label>
        <input type="date" name="installed_at" id="installed_at" class="form-control" value="{{ old('installed_at', $camera?->installed_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $camera?->status?->value ?? 'active') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="notice_posted_at">Aydınlatma tabela tarihi</label>
        <input type="date" name="notice_posted_at" id="notice_posted_at" class="form-control" value="{{ old('notice_posted_at', $camera?->notice_posted_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-8 d-flex align-items-end gap-4 flex-wrap">
        <div class="form-check mb-2">
            <input type="checkbox" name="is_recording" id="is_recording" class="form-check-input" value="1" @checked(old('is_recording', $camera?->is_recording ?? true))>
            <label class="form-check-label" for="is_recording">Kayıt alıyor</label>
        </div>
        <div class="form-check mb-2">
            <input type="checkbox" name="records_audio" id="records_audio" class="form-check-input" value="1" @checked(old('records_audio', $camera?->records_audio))>
            <label class="form-check-label" for="records_audio">Ses kaydı</label>
        </div>
        <div class="form-check mb-2">
            <input type="checkbox" name="notice_posted" id="notice_posted" class="form-check-input" value="1" @checked(old('notice_posted', $camera?->notice_posted))>
            <label class="form-check-label" for="notice_posted">Aydınlatma tabelası var</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $camera?->notes) }}</textarea>
    </div>
</div>
