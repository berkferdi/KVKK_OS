<?php

namespace App\Infrastructure\Repositories\Identity;

use App\Infrastructure\Repositories\BaseRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginateForTenant(int $tenantId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->whereHas('tenants', fn ($q) => $q->where('tenants.id', $tenantId))
            ->with(['roles'])
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string|int>  $roleIds
     */
    public function createForTenant(int $tenantId, array $data, array $roleIds = [], bool $isOwner = false): User
    {
        return DB::transaction(function () use ($tenantId, $data, $roleIds, $isOwner): User {
            /** @var User $user */
            $user = $this->model->newQuery()->create($data);
            $user->tenants()->attach($tenantId, ['is_owner' => $isOwner]);

            if ($roleIds !== []) {
                setPermissionsTeamId($tenantId);
                $user->syncRoles($roleIds);
            }

            return $user->load('roles');
        });
    }
}
