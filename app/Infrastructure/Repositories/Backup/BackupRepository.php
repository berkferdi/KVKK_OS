<?php

namespace App\Infrastructure\Repositories\Backup;

use App\Domain\Backup\Models\Backup;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * @extends BaseRepository<Backup>
 */
class BackupRepository extends BaseRepository
{
    public function __construct(Backup $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, Backup>
     */
    public function paginateLatest(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('triggeredByUser')
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, Backup>
     */
    public function completedOldestFirst(): Collection
    {
        return $this->model->newQuery()
            ->where('status', 'completed')
            ->orderBy('id')
            ->get();
    }
}
