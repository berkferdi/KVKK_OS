<?php

namespace App\Http\Controllers\Web\Cookies;

use App\Application\Services\Cookies\SiteCookieService;
use App\Domain\Cookies\Enums\CookieCategory;
use App\Domain\Cookies\Enums\CookieStatus;
use App\Domain\Cookies\Models\SiteCookie;
use App\Domain\Organization\Models\Company;
use App\Domain\Websites\Models\Website;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cookies\StoreSiteCookieRequest;
use App\Http\Requests\Cookies\UpdateSiteCookieRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiteCookieController extends Controller
{
    public function __construct(
        private readonly SiteCookieService $cookies,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', SiteCookie::class);

        return view('cookies.index', [
            'company' => $company,
            'cookies' => $this->cookies->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [SiteCookie::class, $company]);

        return view('cookies.create', [
            'company' => $company,
            'categories' => CookieCategory::cases(),
            'statuses' => CookieStatus::cases(),
            'websites' => Website::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreSiteCookieRequest $request, Company $company): RedirectResponse
    {
        $cookie = $this->cookies->create($company, $request->validated());

        return redirect()
            ->route('companies.cookies.show', [$company, $cookie])
            ->with('success', 'Çerez kaydı oluşturuldu.');
    }

    public function show(Company $company, SiteCookie $cookie): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $cookie);
        abort_unless((int) $cookie->company_id === (int) $company->id, 404);
        $cookie->load('website');

        return view('cookies.show', compact('company', 'cookie'));
    }

    public function edit(Company $company, SiteCookie $cookie): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $cookie);
        abort_unless((int) $cookie->company_id === (int) $company->id, 404);

        return view('cookies.edit', [
            'company' => $company,
            'cookie' => $cookie,
            'categories' => CookieCategory::cases(),
            'statuses' => CookieStatus::cases(),
            'websites' => Website::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(
        UpdateSiteCookieRequest $request,
        Company $company,
        SiteCookie $cookie,
    ): RedirectResponse {
        abort_unless((int) $cookie->company_id === (int) $company->id, 404);
        $this->cookies->update($cookie, $request->validated());

        return redirect()
            ->route('companies.cookies.show', [$company, $cookie])
            ->with('success', 'Çerez kaydı güncellendi.');
    }

    public function destroy(Company $company, SiteCookie $cookie): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $cookie);
        abort_unless((int) $cookie->company_id === (int) $company->id, 404);
        $this->cookies->delete($cookie);

        return redirect()
            ->route('companies.cookies.index', $company)
            ->with('success', 'Çerez kaydı silindi.');
    }
}
