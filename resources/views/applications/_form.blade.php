@php
    /** @var \App\Domain\Applications\Models\DataSubjectApplication|null $application */
    $application = $application ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="applicant_name">Başvuran *</label>
        <input type="text" name="applicant_name" id="applicant_name" class="form-control" required value="{{ old('applicant_name', $application?->applicant_name) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="application_code">Başvuru kodu</label>
        <input type="text" name="application_code" id="application_code" class="form-control" value="{{ old('application_code', $application?->application_code) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $application?->status?->value ?? 'received') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="applicant_email">E-posta</label>
        <input type="email" name="applicant_email" id="applicant_email" class="form-control" value="{{ old('applicant_email', $application?->applicant_email) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="applicant_phone">Telefon</label>
        <input type="text" name="applicant_phone" id="applicant_phone" class="form-control" value="{{ old('applicant_phone', $application?->applicant_phone) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="branch_id">Şube</label>
        <select name="branch_id" id="branch_id" class="form-select">
            <option value="">—</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $application?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="request_type">Talep türü</label>
        <select name="request_type" id="request_type" class="form-select">
            @foreach ($requestTypes as $type)
                <option value="{{ $type->value }}" @selected(old('request_type', $application?->request_type?->value ?? 'access') === $type->value)>{{ $type->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="channel">Kanal</label>
        <select name="channel" id="channel" class="form-select">
            @foreach ($channels as $channel)
                <option value="{{ $channel->value }}" @selected(old('channel', $application?->channel?->value ?? 'email') === $channel->value)>{{ $channel->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="assigned_to_name">Sorumlu</label>
        <input type="text" name="assigned_to_name" id="assigned_to_name" class="form-control" value="{{ old('assigned_to_name', $application?->assigned_to_name) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="received_at">Alınma tarihi</label>
        <input type="date" name="received_at" id="received_at" class="form-control" value="{{ old('received_at', $application?->received_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="due_at">Son yanıt tarihi</label>
        <input type="date" name="due_at" id="due_at" class="form-control" value="{{ old('due_at', $application?->due_at?->format('Y-m-d')) }}" placeholder="Boş bırakılırsa +30 gün">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="responded_at">Yanıt tarihi</label>
        <input type="date" name="responded_at" id="responded_at" class="form-control" value="{{ old('responded_at', $application?->responded_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="identity_verified" id="identity_verified" class="form-check-input" value="1" @checked(old('identity_verified', $application?->identity_verified))>
            <label class="form-check-label" for="identity_verified">Kimlik doğrulandı</label>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="request_summary">Talep özeti</label>
        <textarea name="request_summary" id="request_summary" rows="3" class="form-control">{{ old('request_summary', $application?->request_summary) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="response_summary">Yanıt özeti</label>
        <textarea name="response_summary" id="response_summary" rows="3" class="form-control">{{ old('response_summary', $application?->response_summary) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $application?->notes) }}</textarea>
    </div>
</div>
