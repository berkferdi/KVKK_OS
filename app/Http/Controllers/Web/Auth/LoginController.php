<?php

namespace App\Http\Controllers\Web\Auth;

use App\Application\Services\Audit\AuditLogger;
use App\Application\Services\TenantContext;
use App\Domain\Organization\Models\Tenant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
        private readonly TenantContext $tenantContext,
    ) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
        ], $request->boolean('remember'))) {
            $this->auditLogger->log('auth.login_failed', null, null, [
                'email' => $credentials['email'],
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'E-posta veya şifre hatalı.']);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();
        $user->forceFill(['last_login_at' => now()])->save();

        $tenant = $user->tenants()->orderByPivot('is_owner', 'desc')->first();

        if ($tenant === null && $user->is_super_admin) {
            $tenant = Tenant::query()->orderBy('id')->first();
        }

        if ($tenant !== null) {
            $request->session()->put('tenant_id', $tenant->id);
            $this->tenantContext->set($tenant);
        }

        $this->auditLogger->log('auth.login_success', $user, null, [
            'tenant_id' => $tenant?->id,
        ], $tenant?->id);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        $tenantId = $request->session()->get('tenant_id');

        if ($user !== null) {
            $this->auditLogger->log('auth.logout', $user, null, null, $tenantId);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $this->tenantContext->clear();

        return redirect()->route('login');
    }
}
