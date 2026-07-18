@php
    /** @var \App\Domain\Organization\Models\Branch|null $branch */
    $branch = $branch ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Şube Adı *</label>
        <input type="text" name="name" id="name" class="form-control" required
               value="{{ old('name', $branch?->name) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="code">Kod</label>
        <input type="text" name="code" id="code" class="form-control"
               value="{{ old('code', $branch?->code) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="phone">Telefon</label>
        <input type="text" name="phone" id="phone" class="form-control"
               value="{{ old('phone', $branch?->phone) }}">
    </div>
    <div class="col-md-8">
        <label class="form-label" for="address">Adres</label>
        <input type="text" name="address" id="address" class="form-control"
               value="{{ old('address', $branch?->address) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label" for="city">İl</label>
        <input type="text" name="city" id="city" class="form-control"
               value="{{ old('city', $branch?->city) }}">
    </div>
    <div class="col-md-2">
        <label class="form-label" for="district">İlçe</label>
        <input type="text" name="district" id="district" class="form-control"
               value="{{ old('district', $branch?->district) }}">
    </div>
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_hq" id="is_hq" value="1"
                @checked(old('is_hq', $branch?->is_hq))>
            <label class="form-check-label" for="is_hq">Merkez şube</label>
        </div>
    </div>
</div>
