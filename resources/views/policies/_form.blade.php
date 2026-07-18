@php
    /** @var \App\Domain\Documents\Models\PolicyDocument|null $policy */
    $policy = $policy ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="title">Başlık *</label>
        <input type="text" name="title" id="title" class="form-control" required value="{{ old('title', $policy?->title) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="code">Kod</label>
        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $policy?->code) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="category">Kategori</label>
        <select name="category" id="category" class="form-select">
            <option value="">—</option>
            @foreach ($categories as $category)
                <option value="{{ $category->value }}" @selected(old('category', $policy?->category?->value) === $category->value)>{{ $category->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label" for="version">Versiyon</label>
        <input type="text" name="version" id="version" class="form-control" value="{{ old('version', $policy?->version ?? '1.0') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $policy?->status?->value ?? 'draft') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="owner_name">Sorumlu</label>
        <input type="text" name="owner_name" id="owner_name" class="form-control" value="{{ old('owner_name', $policy?->owner_name) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="effective_from">Yürürlük</label>
        <input type="date" name="effective_from" id="effective_from" class="form-control" value="{{ old('effective_from', $policy?->effective_from?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="review_date">Gözden geçirme</label>
        <input type="date" name="review_date" id="review_date" class="form-control" value="{{ old('review_date', $policy?->review_date?->format('Y-m-d')) }}">
    </div>
    <div class="col-12">
        <label class="form-label" for="summary">Özet</label>
        <textarea name="summary" id="summary" rows="2" class="form-control">{{ old('summary', $policy?->summary) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label" for="content">İçerik</label>
        <textarea name="content" id="content" rows="8" class="form-control">{{ old('content', $policy?->content) }}</textarea>
    </div>
</div>
