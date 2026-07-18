<?php

namespace App\Http\Controllers\Web\Identity;

use App\Application\Services\Identity\RoleService;
use App\Domain\Identity\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Identity\StoreRoleRequest;
use App\Http\Requests\Identity\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Role::class);

        return view('roles.index', [
            'roles' => $this->roleService->all(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Role::class);

        return view('roles.create', [
            'permissions' => $this->roleService->permissionsCatalog()->groupBy(
                fn ($p) => explode('.', $p->name)[0] ?? 'other'
            ),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = $this->roleService->create(
            $request->string('name')->toString(),
            $request->input('permissions', []),
        );

        return redirect()
            ->route('roles.edit', $role)
            ->with('success', 'Rol oluşturuldu.');
    }

    public function edit(Role $role): View
    {
        $this->authorize('update', $role);
        $role->load('permissions');

        return view('roles.edit', [
            'role' => $role,
            'permissions' => $this->roleService->permissionsCatalog()->groupBy(
                fn ($p) => explode('.', $p->name)[0] ?? 'other'
            ),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->roleService->update(
            $role,
            $request->string('name')->toString(),
            $request->input('permissions', []),
        );

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol güncellendi.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);
        $this->roleService->delete($role);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol silindi.');
    }
}
