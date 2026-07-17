<?php

namespace App\Application\Services\Suppliers;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Organization\Models\Company;
use App\Domain\Suppliers\Enums\SupplierStatus;
use App\Domain\Suppliers\Models\Supplier;
use App\Infrastructure\Repositories\Suppliers\SupplierRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierService
{
    public function __construct(
        private readonly SupplierRepository $suppliers,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Supplier>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->suppliers->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): Supplier
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';
        $data['processes_personal_data'] = (bool) ($data['processes_personal_data'] ?? false);

        /** @var Supplier $supplier */
        $supplier = $this->suppliers->create($data);
        $this->auditLogger->log('supplier.created', $supplier, null, [
            'name' => $supplier->name,
            'supplier_code' => $supplier->supplier_code,
        ], $company->tenant_id);

        return $supplier;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        if (array_key_exists('processes_personal_data', $data)) {
            $data['processes_personal_data'] = (bool) $data['processes_personal_data'];
        }

        $old = [
            'name' => $supplier->name,
            'status' => $supplier->status instanceof SupplierStatus ? $supplier->status->value : null,
        ];
        /** @var Supplier $updated */
        $updated = $this->suppliers->update($supplier, $data);
        $this->auditLogger->log('supplier.updated', $updated, $old, [
            'name' => $updated->name,
            'status' => $updated->status instanceof SupplierStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(Supplier $supplier): bool
    {
        $old = ['name' => $supplier->name];
        $deleted = $this->suppliers->delete($supplier);
        if ($deleted) {
            $this->auditLogger->log('supplier.deleted', $supplier, $old, null, $supplier->tenant_id);
        }

        return $deleted;
    }
}
