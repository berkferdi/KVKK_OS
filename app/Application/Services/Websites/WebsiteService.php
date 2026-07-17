<?php

namespace App\Application\Services\Websites;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Organization\Models\Company;
use App\Domain\Websites\Enums\WebsiteStatus;
use App\Domain\Websites\Models\Website;
use App\Infrastructure\Repositories\Websites\WebsiteRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WebsiteService
{
    public function __construct(
        private readonly WebsiteRepository $websites,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Website>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->websites->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): Website
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';

        foreach ([
            'has_contact_form',
            'has_newsletter',
            'has_user_accounts',
            'has_payment',
            'ssl_enabled',
            'privacy_policy_published',
            'uses_cookies',
        ] as $flag) {
            $data[$flag] = (bool) ($data[$flag] ?? false);
        }

        /** @var Website $website */
        $website = $this->websites->create($data);
        $this->auditLogger->log('website.created', $website, null, [
            'name' => $website->name,
            'url' => $website->url,
        ], $company->tenant_id);

        return $website;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Website $website, array $data): Website
    {
        foreach ([
            'has_contact_form',
            'has_newsletter',
            'has_user_accounts',
            'has_payment',
            'ssl_enabled',
            'privacy_policy_published',
            'uses_cookies',
        ] as $flag) {
            if (array_key_exists($flag, $data)) {
                $data[$flag] = (bool) $data[$flag];
            }
        }

        $old = [
            'name' => $website->name,
            'status' => $website->status instanceof WebsiteStatus ? $website->status->value : null,
        ];
        /** @var Website $updated */
        $updated = $this->websites->update($website, $data);
        $this->auditLogger->log('website.updated', $updated, $old, [
            'name' => $updated->name,
            'status' => $updated->status instanceof WebsiteStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(Website $website): bool
    {
        $old = ['name' => $website->name];
        $deleted = $this->websites->delete($website);
        if ($deleted) {
            $this->auditLogger->log('website.deleted', $website, $old, null, $website->tenant_id);
        }

        return $deleted;
    }
}
