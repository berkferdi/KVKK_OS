<?php

namespace App\Http\Controllers\Web\Identity;

use App\Application\Services\Identity\RoleService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Identity\StorePermissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Permission::class);

        $permissions = $this->roleService->permissionsCatalog()->groupBy(
            fn ($p) => explode('.', $p->name)[0] ?? 'other'
        );

        return view('permissions.index', compact('permissions'));
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->roleService->createPermission($request->string('name')->toString());

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Yetki eklendi.');
    }
}
