<?php

namespace App\Domain\Websites\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Domain\Websites\Enums\WebsiteStatus;
use Database\Factories\WebsiteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends Model
{
    /** @use HasFactory<WebsiteFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'uuid',
        'name',
        'website_code',
        'url',
        'platform',
        'hosting_provider',
        'has_contact_form',
        'has_newsletter',
        'has_user_accounts',
        'has_payment',
        'ssl_enabled',
        'privacy_policy_published',
        'privacy_policy_url',
        'privacy_policy_published_at',
        'uses_cookies',
        'data_collected',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => WebsiteStatus::class,
            'has_contact_form' => 'boolean',
            'has_newsletter' => 'boolean',
            'has_user_accounts' => 'boolean',
            'has_payment' => 'boolean',
            'ssl_enabled' => 'boolean',
            'privacy_policy_published' => 'boolean',
            'uses_cookies' => 'boolean',
            'privacy_policy_published_at' => 'date',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): WebsiteFactory
    {
        return WebsiteFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
