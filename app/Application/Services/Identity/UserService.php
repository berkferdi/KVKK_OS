<?php

namespace App\Application\Services\Identity;

use App\Application\Services\Audit\AuditLogger;
use App\Application\Services\TenantContext;
use App\Infrastructure\Repositories\Identity\UserRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly AuditLogger $auditLogger,
        private readonly TenantContext $tenantContext,
    ) {}

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $tenantId = $this->requireTenantId();

        return $this->users->paginateForTenant($tenantId, $perPage);
    }

    public function findByUuid(string $uuid): ?User
    {
        /** @var User|null $user */
        $user = $this->users->findByUuid($uuid);

        if ($user === null) {
            return null;
        }

        $tenantId = $this->tenantContext->id();
        if ($tenantId !== null && ! $user->belongsToTenant($tenantId) && ! $user->is_super_admin) {
            return null;
        }

        return $user;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string|int>  $roleIds
     */
    public function create(array $data, array $roleIds = [], bool $isOwner = false): User
    {
        $tenantId = $this->requireTenantId();

        unset($data['is_super_admin']);

        $user = $this->users->createForTenant($tenantId, $data, $roleIds, $isOwner);
        $this->auditLogger->log('user.created', $user, null, [
            'email' => $user->email,
            'roles' => $roleIds,
        ], $tenantId);

        return $user;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string|int>|null  $roleIds
     */
    public function update(User $user, array $data, ?array $roleIds = null): User
    {
        $tenantId = $this->requireTenantId();

        if (! $user->belongsToTenant($tenantId) && ! auth()->user()?->is_super_admin) {
            throw ValidationException::withMessages([
                'user' => 'Kullanıcı bu tenant’a ait değil.',
            ]);
        }

        $old = $user->only(['name', 'email', 'phone', 'is_active']);

        if (array_key_exists('password', $data) && ($data['password'] === null || $data['password'] === '')) {
            unset($data['password']);
        }

        unset($data['is_super_admin']);

        /** @var User $updated */
        $updated = $this->users->update($user, $data);

        if ($roleIds !== null) {
            setPermissionsTeamId($tenantId);
            $updated->syncRoles($roleIds);
        }

        $this->auditLogger->log('user.updated', $updated, $old, $updated->only(['name', 'email', 'phone', 'is_active']), $tenantId);

        return $updated->load('roles');
    }

    public function delete(User $user, User $actor): bool
    {
        $tenantId = $this->requireTenantId();

        if ($user->id === $actor->id) {
            throw ValidationException::withMessages([
                'user' => 'Kendi hesabınızı silemezsiniz.',
            ]);
        }

        if ($user->is_super_admin && ! $actor->is_super_admin) {
            throw ValidationException::withMessages([
                'user' => 'Süper admin silinemez.',
            ]);
        }

        $old = $user->only(['name', 'email']);
        $deleted = $this->users->delete($user);

        if ($deleted) {
            $this->auditLogger->log('user.deleted', $user, $old, null, $tenantId);
        }

        return $deleted;
    }

    private function requireTenantId(): int
    {
        $tenantId = $this->tenantContext->id();

        if ($tenantId === null) {
            throw ValidationException::withMessages([
                'tenant' => 'Aktif tenant seçilmedi.',
            ]);
        }

        return $tenantId;
    }
}
