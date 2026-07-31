<?php

namespace App\Infrastructure\Repositories\Documents;

use App\Domain\Documents\Models\DocumentTemplate;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<DocumentTemplate>
 */
class DocumentTemplateRepository extends BaseRepository
{
    public function __construct(DocumentTemplate $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, DocumentTemplate>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, DocumentTemplate>
     */
    public function activeForSelect(): Collection
    {
        return $this->model->newQuery()
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
    }
}
