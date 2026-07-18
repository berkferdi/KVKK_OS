@php
    /** @var \App\Domain\Audits\Models\ComplianceAudit|null $audit */
    $audit = $audit ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="title">Başlık *</label>
        <input type="text" name="title" id="title" class="form-control" required value="{{ old('title', $audit?->title) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="audit_code">Kod</label>
        <input type="text" name="audit_code" id="audit_code" class="form-control" value="{{ old('audit_code', $audit?->audit_code) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $audit?->status?->value ?? 'planned') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="audit_type">Denetim türü</label>
        <select name="audit_type" id="audit_type" class="form-select">
            @foreach ($auditTypes as $type)
                <option value="{{ $type->value }}" @selected(old('audit_type', $audit?->audit_type?->value ?? 'internal') === $type->value)>{{ $type->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="result">Sonuç</label>
        <select name="result" id="result" class="form-select">
            @foreach ($results as $result)
                <option value="{{ $result->value }}" @selected(old('result', $audit?->result?->value ?? 'pending') === $result->value)>{{ $result->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="branch_id">Şube</label>
        <select name="branch_id" id="branch_id" class="form-select">
            <option value="">—</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $audit?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="auditor_name">Denetçi</label>
        <input type="text" name="auditor_name" id="auditor_name" class="form-control" value="{{ old('auditor_name', $audit?->auditor_name) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="planned_at">Planlanan</label>
        <input type="datetime-local" name="planned_at" id="planned_at" class="form-control" value="{{ old('planned_at', $audit?->planned_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="started_at">Başlangıç</label>
        <input type="datetime-local" name="started_at" id="started_at" class="form-control" value="{{ old('started_at', $audit?->started_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="completed_at">Tamamlanma</label>
        <input type="datetime-local" name="completed_at" id="completed_at" class="form-control" value="{{ old('completed_at', $audit?->completed_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="next_audit_due_at">Sonraki denetim</label>
        <input type="datetime-local" name="next_audit_due_at" id="next_audit_due_at" class="form-control" value="{{ old('next_audit_due_at', $audit?->next_audit_due_at?->format('Y-m-d\TH:i')) }}" placeholder="Boş: tamamlanma +1 yıl">
    </div>
    <div class="col-12">
        <label class="form-label" for="scope">Kapsam</label>
        <textarea name="scope" id="scope" rows="2" class="form-control">{{ old('scope', $audit?->scope) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="findings">Bulgular</label>
        <textarea name="findings" id="findings" rows="3" class="form-control">{{ old('findings', $audit?->findings) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="recommendations">Öneriler</label>
        <textarea name="recommendations" id="recommendations" rows="3" class="form-control">{{ old('recommendations', $audit?->recommendations) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="corrective_actions">Düzeltici faaliyetler</label>
        <textarea name="corrective_actions" id="corrective_actions" rows="3" class="form-control">{{ old('corrective_actions', $audit?->corrective_actions) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $audit?->notes) }}</textarea>
    </div>
</div>
