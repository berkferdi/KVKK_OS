<?php

namespace App\Http\Controllers\Web\Suppliers;

use App\Application\Services\Suppliers\SupplierService;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Suppliers\Enums\SupplierStatus;
use App\Domain\Suppliers\Enums\SupplierType;
use App\Domain\Suppliers\Models\Supplier;
use App\Http\Controllers\Controller;
use App\Http\Requests\Suppliers\StoreSupplierRequest;
use App\Http\Requests\Suppliers\UpdateSupplierRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierService $suppliers,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', Supplier::class);

        return view('suppliers.index', [
            'company' => $company,
            'suppliers' => $this->suppliers->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [Supplier::class, $company]);

        return view('suppliers.create', [
            'company' => $company,
            'supplierTypes' => SupplierType::cases(),
            'statuses' => SupplierStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreSupplierRequest $request, Company $company): RedirectResponse
    {
        $supplier = $this->suppliers->create($company, $request->validated());

        return redirect()
            ->route('companies.suppliers.show', [$company, $supplier])
            ->with('success', 'Tedarikçi kaydı oluşturuldu.');
    }

    public function show(Company $company, Supplier $supplier): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $supplier);
        abort_unless($supplier->company_id === $company->id, 404);
        $supplier->load('branch');

        return view('suppliers.show', compact('company', 'supplier'));
    }

    public function edit(Company $company, Supplier $supplier): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $supplier);
        abort_unless($supplier->company_id === $company->id, 404);

        return view('suppliers.edit', [
            'company' => $company,
            'supplier' => $supplier,
            'supplierTypes' => SupplierType::cases(),
            'statuses' => SupplierStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(
        UpdateSupplierRequest $request,
        Company $company,
        Supplier $supplier,
    ): RedirectResponse {
        abort_unless($supplier->company_id === $company->id, 404);
        $this->suppliers->update($supplier, $request->validated());

        return redirect()
            ->route('companies.suppliers.show', [$company, $supplier])
            ->with('success', 'Tedarikçi kaydı güncellendi.');
    }

    public function destroy(Company $company, Supplier $supplier): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $supplier);
        abort_unless($supplier->company_id === $company->id, 404);
        $this->suppliers->delete($supplier);

        return redirect()
            ->route('companies.suppliers.index', $company)
            ->with('success', 'Tedarikçi kaydı silindi.');
    }
}
