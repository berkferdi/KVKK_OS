<?php

namespace App\Http\Controllers\Web\Identity;

use App\Application\Services\Identity\RoleService;
use App\Application\Services\Identity\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Identity\StoreUserRequest;
use App\Http\Requests\Identity\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly RoleService $roleService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('users.index', [
            'users' => $this->userService->paginate(15),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create', [
            'roles' => $this->roleService->all(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['roles', 'is_owner', 'password_confirmation']);
        $user = $this->userService->create(
            $data,
            $request->input('roles', []),
            $request->boolean('is_owner'),
        );

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Kullanıcı oluşturuldu.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);
        $user->load('roles');

        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);
        $user->load('roles');

        return view('users.edit', [
            'user' => $user,
            'roles' => $this->roleService->all(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->except(['roles', 'password_confirmation']);
        $this->userService->update($user, $data, $request->input('roles', []));

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Kullanıcı güncellendi.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        /** @var User $actor */
        $actor = auth()->user();
        $this->userService->delete($user, $actor);

        return redirect()
            ->route('users.index')
            ->with('success', 'Kullanıcı silindi.');
    }
}
