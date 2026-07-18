<?php

namespace App\Application\Services\Personnel;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Organization\Models\Company;
use App\Domain\Personnel\Enums\EmployeeStatus;
use App\Domain\Personnel\Models\Employee;
use App\Infrastructure\Repositories\Personnel\EmployeeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeService
{
    public function __construct(
        private readonly EmployeeRepository $employees,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Employee>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->employees->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): Employee
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';
        $data['has_system_access'] = (bool) ($data['has_system_access'] ?? false);

        /** @var Employee $employee */
        $employee = $this->employees->create($data);
        $this->auditLogger->log('employee.created', $employee, null, [
            'name' => $employee->fullName(),
            'employee_code' => $employee->employee_code,
        ], $company->tenant_id);

        return $employee;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Employee $employee, array $data): Employee
    {
        if (array_key_exists('has_system_access', $data)) {
            $data['has_system_access'] = (bool) $data['has_system_access'];
        }

        $old = [
            'name' => $employee->fullName(),
            'status' => $employee->status instanceof EmployeeStatus ? $employee->status->value : null,
        ];
        /** @var Employee $updated */
        $updated = $this->employees->update($employee, $data);
        $this->auditLogger->log('employee.updated', $updated, $old, [
            'name' => $updated->fullName(),
            'status' => $updated->status instanceof EmployeeStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(Employee $employee): bool
    {
        $old = ['name' => $employee->fullName()];
        $deleted = $this->employees->delete($employee);
        if ($deleted) {
            $this->auditLogger->log('employee.deleted', $employee, $old, null, $employee->tenant_id);
        }

        return $deleted;
    }
}
