<?php

namespace App\Infrastructure\Repositories\Documents;

use App\Domain\Documents\Enums\GenerationStatus;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends BaseRepository<GeneratedDocument>
 */
class GeneratedDocumentRepository extends BaseRepository
{
    public function __construct(GeneratedDocument $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, GeneratedDocument>
     */
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('company_id', $companyId)
            ->with('template')
            ->latest('generated_at')
            ->latest('id')
            ->paginate($perPage);
    }

    public function supersedeActiveForTemplate(int $companyId, int $templateId): void
    {
        $this->model->newQuery()
            ->where('company_id', $companyId)
            ->where('document_template_id', $templateId)
            ->where('status', GenerationStatus::Generated->value)
            ->update(['status' => GenerationStatus::Superseded->value]);
    }

    public function nextVersionForTemplate(int $companyId, int $templateId): int
    {
        $max = $this->model->newQuery()
            ->where('company_id', $companyId)
            ->where('document_template_id', $templateId)
            ->max('version');

        return ((int) $max) + 1;
    }
}
