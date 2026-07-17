<?php

namespace App\Http\Controllers\Web\Customers;

use App\Application\Services\Customers\CustomerService;
use App\Domain\Customers\Enums\CustomerStatus;
use App\Domain\Customers\Enums\CustomerType;
use App\Domain\Customers\Models\Customer;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\StoreCustomerRequest;
use App\Http\Requests\Customers\UpdateCustomerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customers,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', Customer::class);

        return view('customers.index', [
            'company' => $company,
            'customers' => $this->customers->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [Customer::class, $company]);

        return view('customers.create', [
            'company' => $company,
            'customerTypes' => CustomerType::cases(),
            'statuses' => CustomerStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreCustomerRequest $request, Company $company): RedirectResponse
    {
        $customer = $this->customers->create($company, $request->validated());

        return redirect()
            ->route('companies.customers.show', [$company, $customer])
            ->with('success', 'Müşteri kaydı oluşturuldu.');
    }

    public function show(Company $company, Customer $customer): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $customer);
        abort_unless($customer->company_id === $company->id, 404);
        $customer->load('branch');

        return view('customers.show', compact('company', 'customer'));
    }

    public function edit(Company $company, Customer $customer): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $customer);
        abort_unless($customer->company_id === $company->id, 404);

        return view('customers.edit', [
            'company' => $company,
            'customer' => $customer,
            'customerTypes' => CustomerType::cases(),
            'statuses' => CustomerStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(
        UpdateCustomerRequest $request,
        Company $company,
        Customer $customer,
    ): RedirectResponse {
        abort_unless($customer->company_id === $company->id, 404);
        $this->customers->update($customer, $request->validated());

        return redirect()
            ->route('companies.customers.show', [$company, $customer])
            ->with('success', 'Müşteri kaydı güncellendi.');
    }

    public function destroy(Company $company, Customer $customer): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $customer);
        abort_unless($customer->company_id === $company->id, 404);
        $this->customers->delete($customer);

        return redirect()
            ->route('companies.customers.index', $company)
            ->with('success', 'Müşteri kaydı silindi.');
    }
}
