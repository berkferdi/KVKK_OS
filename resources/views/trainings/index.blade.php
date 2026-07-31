@extends('layouts.admin')
@section('title', 'Eğitimler — KVKK 360')
@section('page_title', 'Eğitimler')
@section('page_subtitle', $company->trade_name)
@section('page_actions')
    @can('create', [App\Domain\Trainings\Models\TrainingRecord::class, $company])
        <a href="{{ route('companies.trainings.create', $company) }}" class="btn btn-sm text-white" style="background:#1f6f5b;">Yeni Eğitim</a>
    @endcan
    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">Firmaya dön</a>
@endsection
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Başlık</th><th>Tür</th><th>Plan</th><th>Sonraki</th><th>Durum</th><th></th></tr></thead>
            <tbody>
            @forelse ($trainings as $training)
                <tr>
                    <td><a href="{{ route('companies.trainings.show', [$company, $training]) }}">{{ $training->title }}</a></td>
                    <td>{{ $training->training_type->value }}</td>
                    <td>
                        {{ $training->planned_at?->format('d.m.Y H:i') ?: '—' }}
                        @if($training->isScheduleOverdue())
                            <span class="badge text-bg-warning">plan gecikti</span>
                        @endif
                    </td>
                    <td>
                        {{ $training->next_training_due_at?->format('d.m.Y') ?: '—' }}
                        @if($training->isNextTrainingOverdue())
                            <span class="badge text-bg-danger">vade geçti</span>
                        @endif
                    </td>
                    <td><span class="badge text-bg-secondary">{{ $training->status->value }}</span></td>
                    <td class="text-end"><a href="{{ route('companies.trainings.edit', [$company, $training]) }}" class="btn btn-sm btn-outline-primary">Düzenle</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Eğitim kaydı yok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($trainings->hasPages())<div class="card-footer">{{ $trainings->links() }}</div>@endif
</div>
@endsection
