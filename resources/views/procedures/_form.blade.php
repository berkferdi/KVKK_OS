@php
    /** @var \App\Domain\Documents\Models\ProcedureDocument|null $procedure */
    $procedure = $procedure ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="title">Başlık *</label>
        <input type="text" name="title" id="title" class="form-control" required value="{{ old('title', $procedure?->title) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="code">Kod</label>
        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $procedure?->code) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="category">Kategori</label>
        <select name="category" id="category" class="form-select">
            <option value="">—</option>
            @foreach ($categories as $category)
                <option value="{{ $category->value }}" @selected(old('category', $procedure?->category?->value) === $category->value)>{{ $category->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="policy_document_id">İlişkili politika</label>
        <select name="policy_document_id" id="policy_document_id" class="form-select">
            <option value="">—</option>
            @foreach ($policies as $linkedPolicy)
                <option value="{{ $linkedPolicy->id }}" @selected((string) old('policy_document_id', $procedure?->policy_document_id) === (string) $linkedPolicy->id)>{{ $linkedPolicy->title }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label" for="version">Versiyon</label>
        <input type="text" name="version" id="version" class="form-control" value="{{ old('version', $procedure?->version ?? '1.0') }}">
    </div>
    <div class="col-md-2">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $procedure?->status?->value ?? 'draft') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="owner_name">Sorumlu</label>
        <input type="text" name="owner_name" id="owner_name" class="form-control" value="{{ old('owner_name', $procedure?->owner_name) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="effective_from">Yürürlük</label>
        <input type="date" name="effective_from" id="effective_from" class="form-control" value="{{ old('effective_from', $procedure?->effective_from?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="review_date">Gözden geçirme</label>
        <input type="date" name="review_date" id="review_date" class="form-control" value="{{ old('review_date', $procedure?->review_date?->format('Y-m-d')) }}">
    </div>
    <div class="col-12">
        <label class="form-label" for="summary">Özet</label>
        <textarea name="summary" id="summary" rows="2" class="form-control">{{ old('summary', $procedure?->summary) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="content">İçerik</label>
        <textarea name="content" id="content" rows="6" class="form-control">{{ old('content', $procedure?->content) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="steps">Adımlar</label>
        <textarea name="steps" id="steps" rows="6" class="form-control">{{ old('steps', $procedure?->steps) }}</textarea>
    </div>
</div>
