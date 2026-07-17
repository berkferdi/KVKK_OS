<?php

namespace App\Http\Controllers\Web\Personnel;

use App\Application\Services\Personnel\EmployeeService;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Personnel\Enums\EmployeeStatus;
use App\Domain\Personnel\Enums\EmploymentType;
use App\Domain\Personnel\Models\Employee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Personnel\StoreEmployeeRequest;
use App\Http\Requests\Personnel\UpdateEmployeeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeService $employees,
    ) {}

    public function index(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('viewAny', Employee::class);

        return view('personnel.index', [
            'company' => $company,
            'employees' => $this->employees->paginateForCompany($company),
        ]);
    }

    public function create(Company $company): View
    {
        $this->authorize('view', $company);
        $this->authorize('create', [Employee::class, $company]);

        return view('personnel.create', [
            'company' => $company,
            'employmentTypes' => EmploymentType::cases(),
            'statuses' => EmployeeStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreEmployeeRequest $request, Company $company): RedirectResponse
    {
        $employee = $this->employees->create($company, $request->validated());

        return redirect()
            ->route('companies.personnel.show', [$company, $employee])
            ->with('success', 'Personel kaydı oluşturuldu.');
    }

    public function show(Company $company, Employee $employee): View
    {
        $this->authorize('view', $company);
        $this->authorize('view', $employee);
        abort_unless($employee->company_id === $company->id, 404);
        $employee->load('branch');

        return view('personnel.show', compact('company', 'employee'));
    }

    public function edit(Company $company, Employee $employee): View
    {
        $this->authorize('view', $company);
        $this->authorize('update', $employee);
        abort_unless($employee->company_id === $company->id, 404);

        return view('personnel.edit', [
            'company' => $company,
            'employee' => $employee,
            'employmentTypes' => EmploymentType::cases(),
            'statuses' => EmployeeStatus::cases(),
            'branches' => Branch::query()
                ->where('company_id', $company->id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(
        UpdateEmployeeRequest $request,
        Company $company,
        Employee $employee,
    ): RedirectResponse {
        abort_unless($employee->company_id === $company->id, 404);
        $this->employees->update($employee, $request->validated());

        return redirect()
            ->route('companies.personnel.show', [$company, $employee])
            ->with('success', 'Personel kaydı güncellendi.');
    }

    public function destroy(Company $company, Employee $employee): RedirectResponse
    {
        $this->authorize('view', $company);
        $this->authorize('delete', $employee);
        abort_unless($employee->company_id === $company->id, 404);
        $this->employees->delete($employee);

        return redirect()
            ->route('companies.personnel.index', $company)
            ->with('success', 'Personel kaydı silindi.');
    }
}
