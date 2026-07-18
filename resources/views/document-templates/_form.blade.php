@php
    /** @var \App\Domain\Documents\Models\DocumentTemplate|null $template */
    $template = $template ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="code">Kod *</label>
        <input type="text" name="code" id="code" class="form-control" required value="{{ old('code', $template?->code) }}">
    </div>
    <div class="col-md-5">
        <label class="form-label" for="title">Başlık *</label>
        <input type="text" name="title" id="title" class="form-control" required value="{{ old('title', $template?->title) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="category">Kategori</label>
        <select name="category" id="category" class="form-select">
            @foreach ($categories as $category)
                <option value="{{ $category->value }}" @selected(old('category', $template?->category?->value ?? 'other') === $category->value)>{{ $category->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label" for="description">Açıklama</label>
        <input type="text" name="description" id="description" class="form-control" value="{{ old('description', $template?->description) }}">
    </div>
    <div class="col-12">
        <label class="form-label" for="body">Şablon gövdesi *</label>
        <textarea name="body" id="body" rows="12" class="form-control font-monospace" required placeholder="@{{firma_unvani}}, @{{adres}}, @{{mersis}} ...">{{ old('body', $template?->body) }}</textarea>
        <div class="form-text">Placeholder formatı: <code>{{'{{'}}firma_unvani{{'}}'}}</code>, <code>{{'{{'}}adres{{'}}'}}</code>, <code>{{'{{'}}mersis{{'}}'}}</code></div>
    </div>
    <div class="col-12">
        <div class="form-check">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" @checked(old('is_active', $template?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Aktif</label>
        </div>
    </div>
</div>
