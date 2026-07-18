@php
    /** @var \App\Domain\Trainings\Models\TrainingRecord|null $training */
    $training = $training ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="title">Başlık *</label>
        <input type="text" name="title" id="title" class="form-control" required value="{{ old('title', $training?->title) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="training_code">Kod</label>
        <input type="text" name="training_code" id="training_code" class="form-control" value="{{ old('training_code', $training?->training_code) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Durum</label>
        <select name="status" id="status" class="form-select">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $training?->status?->value ?? 'planned') === $status->value)>{{ $status->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="training_type">Eğitim türü</label>
        <select name="training_type" id="training_type" class="form-select">
            @foreach ($trainingTypes as $type)
                <option value="{{ $type->value }}" @selected(old('training_type', $training?->training_type?->value ?? 'awareness') === $type->value)>{{ $type->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="delivery_method">Yöntem</label>
        <select name="delivery_method" id="delivery_method" class="form-select">
            @foreach ($deliveryMethods as $method)
                <option value="{{ $method->value }}" @selected(old('delivery_method', $training?->delivery_method?->value ?? 'in_person') === $method->value)>{{ $method->value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="branch_id">Şube</label>
        <select name="branch_id" id="branch_id" class="form-select">
            <option value="">—</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected((string) old('branch_id', $training?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="trainer_name">Eğitmen</label>
        <input type="text" name="trainer_name" id="trainer_name" class="form-control" value="{{ old('trainer_name', $training?->trainer_name) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="planned_at">Planlanan</label>
        <input type="datetime-local" name="planned_at" id="planned_at" class="form-control" value="{{ old('planned_at', $training?->planned_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="conducted_at">Gerçekleşme</label>
        <input type="datetime-local" name="conducted_at" id="conducted_at" class="form-control" value="{{ old('conducted_at', $training?->conducted_at?->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="next_training_due_at">Sonraki eğitim</label>
        <input type="datetime-local" name="next_training_due_at" id="next_training_due_at" class="form-control" value="{{ old('next_training_due_at', $training?->next_training_due_at?->format('Y-m-d\TH:i')) }}" placeholder="Boş: gerçekleşme +1 yıl">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="participant_count">Katılımcı sayısı</label>
        <input type="number" name="participant_count" id="participant_count" class="form-control" min="0" value="{{ old('participant_count', $training?->participant_count) }}">
    </div>
    <div class="col-12">
        <label class="form-label" for="participant_names">Katılımcılar</label>
        <textarea name="participant_names" id="participant_names" rows="2" class="form-control">{{ old('participant_names', $training?->participant_names) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="topics">Konular</label>
        <textarea name="topics" id="topics" rows="3" class="form-control">{{ old('topics', $training?->topics) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="materials">Materyaller</label>
        <textarea name="materials" id="materials" rows="3" class="form-control">{{ old('materials', $training?->materials) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="attendance_notes">Katılım notları</label>
        <textarea name="attendance_notes" id="attendance_notes" rows="3" class="form-control">{{ old('attendance_notes', $training?->attendance_notes) }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="notes">Notlar</label>
        <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $training?->notes) }}</textarea>
    </div>
</div>
