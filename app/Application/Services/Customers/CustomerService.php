<?php

namespace App\Application\Services\Customers;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Customers\Enums\CustomerStatus;
use App\Domain\Customers\Models\Customer;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Customers\CustomerRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepository $customers,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Customer>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->customers->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): Customer
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';
        $data['marketing_consent'] = (bool) ($data['marketing_consent'] ?? false);

        /** @var Customer $customer */
        $customer = $this->customers->create($data);
        $this->auditLogger->log('customer.created', $customer, null, [
            'name' => $customer->name,
            'customer_code' => $customer->customer_code,
        ], $company->tenant_id);

        return $customer;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Customer $customer, array $data): Customer
    {
        if (array_key_exists('marketing_consent', $data)) {
            $data['marketing_consent'] = (bool) $data['marketing_consent'];
        }

        $old = [
            'name' => $customer->name,
            'status' => $customer->status instanceof CustomerStatus ? $customer->status->value : null,
        ];
        /** @var Customer $updated */
        $updated = $this->customers->update($customer, $data);
        $this->auditLogger->log('customer.updated', $updated, $old, [
            'name' => $updated->name,
            'status' => $updated->status instanceof CustomerStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(Customer $customer): bool
    {
        $old = ['name' => $customer->name];
        $deleted = $this->customers->delete($customer);
        if ($deleted) {
            $this->auditLogger->log('customer.deleted', $customer, $old, null, $customer->tenant_id);
        }

        return $deleted;
    }
}
