@extends('layouts.admin')
@section('title', 'VERBİS Sicil — KVKK 360')
@section('page_title', 'VERBİS Sicil Bilgileri')
@section('page_subtitle', $company->trade_name)
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('companies.verbis.registration.update', [$company, $registration]) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label" for="registration_number">Sicil No</label>
            <input type="text" name="registration_number" id="registration_number" class="form-control" value="{{ old('registration_number', $registration->registration_number) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="registered_at">Kayıt tarihi</label>
            <input type="date" name="registered_at" id="registered_at" class="form-control" value="{{ old('registered_at', $registration->registered_at?->format('Y-m-d')) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="status">Durum</label>
            <select name="status" id="status" class="form-select">
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $registration->status->value) === $status->value)>{{ $status->value }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="contact_name">İrtibat kişisi</label>
            <input type="text" name="contact_name" id="contact_name" class="form-control" value="{{ old('contact_name', $registration->contact_name) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="contact_email">İrtibat e-posta</label>
            <input type="email" name="contact_email" id="contact_email" class="form-control" value="{{ old('contact_email', $registration->contact_email) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="contact_phone">İrtibat telefon</label>
            <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="{{ old('contact_phone', $registration->contact_phone) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label" for="last_reviewed_at">Son gözden geçirme</label>
            <input type="date" name="last_reviewed_at" id="last_reviewed_at" class="form-control" value="{{ old('last_reviewed_at', $registration->last_reviewed_at?->format('Y-m-d')) }}">
        </div>
        <div class="col-md-8 d-flex align-items-end">
            <div class="form-check mb-2">
                <input type="checkbox" name="is_exempt" id="is_exempt" class="form-check-input" value="1" @checked(old('is_exempt', $registration->is_exempt))>
                <label class="form-check-label" for="is_exempt">Kayıt yükümlülüğünden muaf</label>
            </div>
        </div>
        <div class="col-12">
            <label class="form-label" for="exemption_reason">Muafiyet gerekçesi</label>
            <textarea name="exemption_reason" id="exemption_reason" rows="2" class="form-control">{{ old('exemption_reason', $registration->exemption_reason) }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label" for="notes">Notlar</label>
            <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $registration->notes) }}</textarea>
        </div>
    </div>
    <div class="mt-4 d-flex gap-2">
        <button class="btn text-white" style="background:#1f6f5b;" type="submit">Kaydet</button>
        <a href="{{ route('companies.verbis.index', $company) }}" class="btn btn-outline-secondary">İptal</a>
    </div>
</form>
</div></div>
@endsection
