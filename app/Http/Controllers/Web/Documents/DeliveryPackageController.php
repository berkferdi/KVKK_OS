<?php

namespace App\Http\Controllers\Web\Documents;

use App\Application\Services\Documents\DeliveryPackageService;
use App\Domain\Documents\Models\DeliveryPackage;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DeliveryPackageController extends Controller
{
    public function __construct(
        private readonly DeliveryPackageService $packages,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', DeliveryPackage::class);

        return view('delivery-packages.index', [
            'company' => $company,
            'packages' => $this->packages->paginateForCompany($company),
        ]);
    }

    public function store(Company $company): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('create', [DeliveryPackage::class, $company]);

        try {
            $package = $this->packages->create($company, request()->user());
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('companies.delivery-packages.index', $company)
                ->with('error', $e->getMessage());
        } catch (Throwable $e) {
            return redirect()
                ->route('companies.delivery-packages.index', $company)
                ->with('error', 'Teslim paketi oluşturulamadı: '.$e->getMessage());
        }

        return redirect()
            ->route('companies.delivery-packages.show', [$company, $package])
            ->with('success', 'Teslim paketi hazır.');
    }

    public function show(Company $company, DeliveryPackage $deliveryPackage): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $deliveryPackage);
        abort_unless((int) $deliveryPackage->company_id === (int) $company->id, 404);

        return view('delivery-packages.show', [
            'company' => $company,
            'package' => $deliveryPackage,
        ]);
    }

    public function download(Company $company, DeliveryPackage $deliveryPackage): StreamedResponse|RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('view', $deliveryPackage);
        abort_unless((int) $deliveryPackage->company_id === (int) $company->id, 404);

        try {
            return $this->packages->download($deliveryPackage);
        } catch (InvalidArgumentException|RuntimeException $e) {
            return redirect()
                ->route('companies.delivery-packages.show', [$company, $deliveryPackage])
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Company $company, DeliveryPackage $deliveryPackage): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $deliveryPackage);
        abort_unless((int) $deliveryPackage->company_id === (int) $company->id, 404);
        $this->packages->delete($deliveryPackage);

        return redirect()
            ->route('companies.delivery-packages.index', $company)
            ->with('success', 'Teslim paketi silindi.');
    }
}
