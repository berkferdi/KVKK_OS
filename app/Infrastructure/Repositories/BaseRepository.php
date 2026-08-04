<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Shared\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 *
 * @implements RepositoryInterface<TModel>
 */
abstract class BaseRepository implements RepositoryInterface
{
    /** @var TModel */
    protected Model $model;

    /**
     * @param  TModel  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection<int, TModel>
     */
    public function all(): Collection
    {
        /** @var Collection<int, TModel> $results */
        $results = $this->model->newQuery()->latest('id')->get();

        return $results;
    }

    /**
     * @return TModel|null
     */
    public function find(int $id): ?Model
    {
        /** @var TModel|null $result */
        $result = $this->model->newQuery()->find($id);

        return $result;
    }

    /**
     * @return TModel|null
     */
    public function findByUuid(string $uuid): ?Model
    {
        /** @var TModel|null $result */
        $result = $this->model->newQuery()->where('uuid', $uuid)->first();

        return $result;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return TModel
     */
    public function create(array $data): Model
    {
        /** @var TModel $created */
        $created = $this->model->newQuery()->create($data);

        return $created;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return TModel
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        /** @var TModel $model */
        return $model->refresh();
    }

    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }

    /**
     * @return LengthAwarePaginator<int, TModel>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, TModel> $paginator */
        $paginator = $this->model->newQuery()->latest('id')->paginate($perPage);

        return $paginator;
    }
}
