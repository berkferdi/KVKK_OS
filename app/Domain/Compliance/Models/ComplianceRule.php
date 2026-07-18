<?php

namespace App\Domain\Compliance\Models;

use App\Domain\Shared\Concerns\HasUuid;
use Database\Factories\ComplianceRuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplianceRule extends Model
{
    /** @use HasFactory<ComplianceRuleFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'uuid',
        'code',
        'name',
        'description',
        'is_active',
        'priority',
        'conditions',
        'actions',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'priority' => 'integer',
            'conditions' => 'array',
            'actions' => 'array',
        ];
    }

    protected static function newFactory(): ComplianceRuleFactory
    {
        return ComplianceRuleFactory::new();
    }
}
