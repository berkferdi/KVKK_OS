<?php

namespace App\Application\Services\Compliance;

use App\Domain\Compliance\Models\ComplianceRule;
use App\Domain\Organization\Enums\CompanyStatus;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Collection;

/**
 * DB tabanlı kural değerlendirici — KVKK kuralları kodda hard-code edilmez.
 */
class RuleEngine
{
    /**
     * @return Collection<int, array{rule: ComplianceRule, actions: list<array<string, mixed>>}>
     */
    public function evaluate(Company $company): Collection
    {
        $context = $this->buildContext($company);

        $rules = ComplianceRule::query()
            ->where('is_active', true)
            ->where(function ($q) use ($company): void {
                $q->whereNull('tenant_id')
                    ->orWhere('tenant_id', $company->tenant_id);
            })
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        $matched = collect();

        foreach ($rules as $rule) {
            if ($this->matches($rule->conditions ?? [], $context)) {
                $matched->push([
                    'rule' => $rule,
                    'actions' => $rule->actions ?? [],
                ]);
            }
        }

        return $matched;
    }

    /**
     * @return array<string, mixed>
     */
    public function buildContext(Company $company): array
    {
        return [
            'has_camera' => (bool) $company->has_camera,
            'has_website' => (bool) $company->has_website,
            'has_cookies' => (bool) $company->has_cookies,
            'employee_count' => (int) ($company->employee_count ?? 0),
            'nace_code' => $company->nace_code,
            'city' => $company->city,
            'status' => $company->status instanceof CompanyStatus
                ? $company->status->value
                : null,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $conditions
     * @param  array<string, mixed>  $context
     */
    public function matches(array $conditions, array $context): bool
    {
        if ($conditions === []) {
            return false;
        }

        foreach ($conditions as $condition) {
            $field = (string) ($condition['field'] ?? '');
            $operator = (string) ($condition['operator'] ?? 'eq');
            $expected = $condition['value'] ?? null;
            $actual = $context[$field] ?? null;

            if (! $this->compare($actual, $operator, $expected)) {
                return false;
            }
        }

        return true;
    }

    private function compare(mixed $actual, string $operator, mixed $expected): bool
    {
        return match ($operator) {
            'eq' => $actual == $expected,
            'neq' => $actual != $expected,
            'gt' => is_numeric($actual) && is_numeric($expected) && $actual > $expected,
            'gte' => is_numeric($actual) && is_numeric($expected) && $actual >= $expected,
            'lt' => is_numeric($actual) && is_numeric($expected) && $actual < $expected,
            'lte' => is_numeric($actual) && is_numeric($expected) && $actual <= $expected,
            'truthy' => (bool) $actual === true,
            'falsy' => (bool) $actual === false,
            default => false,
        };
    }
}
