@php
    /** @var \App\Domain\Personnel\Models\Employee|null $employee */
    $employee = $employee ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="first_name">Ad *</label>
        <input type="text" name="first_name" id="first_name" class="form-control" required value="{{ old('first_name', $employee?->first_name) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="last_name">Soyad *</label>
        <input type="text" name="last_name" id="last_name" class="form-control" required value="{{ old('last_name', $employee?->last_name) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="employee_code">Sicil No</label>
        <input type="text" name="employee_code" id="employee_code" class="form-control" value="{{ old('employee_code', $employee?->employee_code) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="email">E-posta</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $employee?->email) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="phone">Telefon</label>
        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $employee?->phone) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="branch_id">Şube</label>
        <select name="branch_id" id="branch_id" class="form-select">
            <option value="">—</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $employee?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="department">Departman</label>
        <input type="text" name="department" id="department" class="form-control" value="{{ old('department', $employee?->department) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="job_title">Unvan</label>
        <input type="text" name="job_title" id="job_title" class="form-control" value="{{ old('job_title', $employee?->job_title) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="employment_type">İstihdam türü</label>
        <select name="employment_type" id="employment_type" class="form-select">
            @foreach ($employmentTypes as $type)
                <option value="{{ $type->value }}" @selected(old('employment_type', $employee?->employment_type?->value ?? 'full_time') === $type->value)>{{ $type->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="hired_at">İşe giriş</label>
        <input type="date" name="hired_at" id="hired_at" class="form-control" value="{{ old('hired_at', $employee?->hired_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="left_at">İşten ayrılış</label>
        <input type="date" name="left_at" id="left_at" class="form-control" value="{{ old('left_at', $employee?->left_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $employee?->status?->value ?? 'active') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check mb-2">
            <input type="checkbox" name="has_system_access" id="has_system_access" class="form-check-input" value="1" @checked(old('has_system_access', $employee?->has_system_access))>
            <label class="form-check-label" for="has_system_access">Sistem erişimi var</label>
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="privacy_notice_signed_at">Aydınlatma imza</label>
        <input type="date" name="privacy_notice_signed_at" id="privacy_notice_signed_at" class="form-control" value="{{ old('privacy_notice_signed_at', $employee?->privacy_notice_signed_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="confidentiality_signed_at">Gizlilik taahhüdü</label>
        <input type="date" name="confidentiality_signed_at" id="confidentiality_signed_at" class="form-control" value="{{ old('confidentiality_signed_at', $employee?->confidentiality_signed_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="training_completed_at">KVKK eğitim</label>
        <input type="date" name="training_completed_at" id="training_completed_at" class="form-control" value="{{ old('training_completed_at', $employee?->training_completed_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-12">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $employee?->notes) }}</textarea>
    </div>
</div>
