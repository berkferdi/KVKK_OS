<?php

namespace App\Application\Services\Documents;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Models\ProcedureDocument;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Documents\ProcedureDocumentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProcedureDocumentService
{
    public function __construct(
        private readonly ProcedureDocumentRepository $procedures,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, ProcedureDocument>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->procedures->paginateForCompany($company->id, $perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Company $company, array $data): ProcedureDocument
    {
        $data['company_id'] = $company->id;
        $data['tenant_id'] = $company->tenant_id;
        $data['source'] = $data['source'] ?? 'manual';

        /** @var ProcedureDocument $procedure */
        $procedure = $this->procedures->create($data);
        $this->auditLogger->log('procedure.created', $procedure, null, [
            'title' => $procedure->title,
        ], $company->tenant_id);

        return $procedure;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(ProcedureDocument $procedure, array $data): ProcedureDocument
    {
        $old = [
            'title' => $procedure->title,
            'status' => $procedure->status instanceof DocumentStatus ? $procedure->status->value : null,
        ];
        /** @var ProcedureDocument $updated */
        $updated = $this->procedures->update($procedure, $data);
        $this->auditLogger->log('procedure.updated', $updated, $old, [
            'title' => $updated->title,
            'status' => $updated->status instanceof DocumentStatus ? $updated->status->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(ProcedureDocument $procedure): bool
    {
        $old = ['title' => $procedure->title];
        $deleted = $this->procedures->delete($procedure);
        if ($deleted) {
            $this->auditLogger->log('procedure.deleted', $procedure, $old, null, $procedure->tenant_id);
        }

        return $deleted;
    }
}
