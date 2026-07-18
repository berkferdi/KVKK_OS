<?php

namespace App\Http\Controllers\Web\Websites;

use App\Application\Services\Websites\WebsiteService;
use App\Domain\Organization\Models\Company;
use App\Domain\Websites\Enums\WebsiteStatus;
use App\Domain\Websites\Models\Website;
use App\Http\Controllers\Controller;
use App\Http\Requests\Websites\StoreWebsiteRequest;
use App\Http\Requests\Websites\UpdateWebsiteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function __construct(
        private readonly WebsiteService $websites,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', Website::class);

        return view('websites.index', [
            'company' => $company,
            'websites' => $this->websites->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [Website::class, $company]);

        return view('websites.create', [
            'company' => $company,
            'statuses' => WebsiteStatus::cases(),
        ]);
    }

    public function store(StoreWebsiteRequest $request, Company $company): RedirectResponse
    {
        $website = $this->websites->create($company, $request->validated());

        return redirect()
            ->route('companies.websites.show', [$company, $website])
            ->with('success', 'Web sitesi kaydı oluşturuldu.');
    }

    public function show(Company $company, Website $website): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $website);
        abort_unless((int) $website->company_id === (int) $company->id, 404);

        return view('websites.show', compact('company', 'website'));
    }

    public function edit(Company $company, Website $website): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $website);
        abort_unless((int) $website->company_id === (int) $company->id, 404);

        return view('websites.edit', [
            'company' => $company,
            'website' => $website,
            'statuses' => WebsiteStatus::cases(),
        ]);
    }

    public function update(
        UpdateWebsiteRequest $request,
        Company $company,
        Website $website,
    ): RedirectResponse {
        abort_unless((int) $website->company_id === (int) $company->id, 404);
        $this->websites->update($website, $request->validated());

        return redirect()
            ->route('companies.websites.show', [$company, $website])
            ->with('success', 'Web sitesi kaydı güncellendi.');
    }

    public function destroy(Company $company, Website $website): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $website);
        abort_unless((int) $website->company_id === (int) $company->id, 404);
        $this->websites->delete($website);

        return redirect()
            ->route('companies.websites.index', $company)
            ->with('success', 'Web sitesi kaydı silindi.');
    }
}
