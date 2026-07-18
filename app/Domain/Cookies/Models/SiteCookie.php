<?php

namespace App\Domain\Cookies\Models;

use App\Domain\Cookies\Enums\CookieCategory;
use App\Domain\Cookies\Enums\CookieStatus;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Domain\Websites\Models\Website;
use Database\Factories\SiteCookieFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteCookie extends Model
{
    /** @use HasFactory<SiteCookieFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $table = 'site_cookies';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'website_id',
        'uuid',
        'name',
        'cookie_code',
        'category',
        'provider',
        'purpose',
        'duration',
        'duration_days',
        'domain',
        'is_third_party',
        'requires_consent',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'category' => CookieCategory::class,
            'status' => CookieStatus::class,
            'is_third_party' => 'boolean',
            'requires_consent' => 'boolean',
            'duration_days' => 'integer',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): SiteCookieFactory
    {
        return SiteCookieFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<Website, $this>
     */
    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
