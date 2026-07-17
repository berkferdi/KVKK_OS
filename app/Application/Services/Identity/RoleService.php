<?php

namespace App\Application\Services\Identity;

use App\Application\Services\Audit\AuditLogger;
use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleService
{
    public function __construct(
        private readonly TenantContext $tenantContext,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return Collection<int, Role>
     */
    public function all(): Collection
    {
        $tenantId = $this->requireTenantId();
        setPermissionsTeamId($tenantId);

        return Role::query()
            ->where('guard_name', 'web')
            ->where('tenant_id', $tenantId)
            ->with('permissions')
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): ?Role
    {
        $tenantId = $this->requireTenantId();
        setPermissionsTeamId($tenantId);

        /** @var Role|null $role */
        $role = Role::query()
            ->where('tenant_id', $tenantId)
            ->with('permissions')
            ->find($id);

        return $role;
    }

    /**
     * @param  list<int|string>  $permissionIds
     */
    public function create(string $name, array $permissionIds = []): Role
    {
        $tenantId = $this->requireTenantId();
        setPermissionsTeamId($tenantId);

        $role = new Role([
            'name' => $name,
            'guard_name' => 'web',
            'tenant_id' => $tenantId,
        ]);
        $role->save();

        if ($permissionIds !== []) {
            $role->syncPermissions($permissionIds);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->auditLogger->log('role.created', null, null, [
            'role_id' => $role->id,
            'name' => $role->name,
            'permissions' => $permissionIds,
        ], $tenantId);

        $role->load('permissions');

        return $role;
    }

    /**
     * @param  list<int|string>  $permissionIds
     */
    public function update(Role $role, string $name, array $permissionIds = []): Role
    {
        $tenantId = $this->requireTenantId();
        $this->assertTenantRole($role, $tenantId);
        setPermissionsTeamId($tenantId);

        $old = ['name' => $role->name, 'permissions' => $role->permissions->pluck('id')->all()];

        $role->name = $name;
        $role->save();
        $role->syncPermissions($permissionIds);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->auditLogger->log('role.updated', null, $old, [
            'role_id' => $role->id,
            'name' => $role->name,
            'permissions' => $permissionIds,
        ], $tenantId);

        $role->load('permissions');

        return $role;
    }

    public function delete(Role $role): bool
    {
        $tenantId = $this->requireTenantId();
        $this->assertTenantRole($role, $tenantId);
        setPermissionsTeamId($tenantId);

        if ($role->users()->count() > 0) {
            throw ValidationException::withMessages([
                'role' => 'Kullanıcılara atanmış rol silinemez.',
            ]);
        }

        $old = ['name' => $role->name];
        $deleted = (bool) $role->delete();

        if ($deleted) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $this->auditLogger->log('role.deleted', null, $old, null, $tenantId);
        }

        return $deleted;
    }

    /**
     * @return Collection<int, Permission>
     */
    public function permissionsCatalog(): Collection
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();
    }

    public function createPermission(string $name): Permission
    {
        $permission = Permission::query()->firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->auditLogger->log('permission.created', null, null, [
            'name' => $permission->name,
        ], $this->tenantContext->id());

        return $permission;
    }

    private function assertTenantRole(Role $role, int $tenantId): void
    {
        if ((int) $role->tenant_id !== $tenantId) {
            throw ValidationException::withMessages([
                'role' => 'Rol bu tenant’a ait değil.',
            ]);
        }
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
