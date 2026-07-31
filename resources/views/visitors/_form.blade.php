@php
    /** @var \App\Domain\Visitors\Models\Visitor|null $visitor */
    $visitor = $visitor ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="first_name">Ad *</label>
        <input type="text" name="first_name" id="first_name" class="form-control" required value="{{ old('first_name', $visitor?->first_name) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="last_name">Soyad *</label>
        <input type="text" name="last_name" id="last_name" class="form-control" required value="{{ old('last_name', $visitor?->last_name) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="visitor_code">Ziyaretçi kodu</label>
        <input type="text" name="visitor_code" id="visitor_code" class="form-control" value="{{ old('visitor_code', $visitor?->visitor_code) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="organization">Kurum</label>
        <input type="text" name="organization" id="organization" class="form-control" value="{{ old('organization', $visitor?->organization) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="host_name">Görüşülecek kişi</label>
        <input type="text" name="host_name" id="host_name" class="form-control" value="{{ old('host_name', $visitor?->host_name) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="branch_id">Şube</label>
        <select name="branch_id" id="branch_id" class="form-select">
            <option value="">—</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $visitor?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="email">E-posta</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $visitor?->email) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="phone">Telefon</label>
        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $visitor?->phone) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="purpose">Ziyaret amacı</label>
        <input type="text" name="purpose" id="purpose" class="form-control" value="{{ old('purpose', $visitor?->purpose) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="visited_at">Giriş</label>
        <input type="datetime-local" name="visited_at" id="visited_at" class="form-control" value="{{ old('visited_at', $visitor?->visited_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="left_at">Çıkış</label>
        <input type="datetime-local" name="left_at" id="left_at" class="form-control" value="{{ old('left_at', $visitor?->left_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $visitor?->status?->value ?? 'checked_in') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="privacy_notice_signed_at">Aydınlatma imza</label>
        <input type="date" name="privacy_notice_signed_at" id="privacy_notice_signed_at" class="form-control" value="{{ old('privacy_notice_signed_at', $visitor?->privacy_notice_signed_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4 d-flex align-items-end gap-4">
        <div class="form-check mb-2">
            <input type="checkbox" name="badge_issued" id="badge_issued" class="form-check-input" value="1" @checked(old('badge_issued', $visitor?->badge_issued))>
            <label class="form-check-label" for="badge_issued">Kart verildi</label>
        </div>
        <div class="form-check mb-2">
            <input type="checkbox" name="photo_captured" id="photo_captured" class="form-check-input" value="1" @checked(old('photo_captured', $visitor?->photo_captured))>
            <label class="form-check-label" for="photo_captured">Fotoğraf alındı</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $visitor?->notes) }}</textarea>
    </div>
</div>
