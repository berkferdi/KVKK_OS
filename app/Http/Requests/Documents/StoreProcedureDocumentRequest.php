<?php

namespace App\Http\Requests\Documents;

use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Enums\ProcedureCategory;
use App\Domain\Documents\Models\ProcedureDocument;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProcedureDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [ProcedureDocument::class, $company]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Company $company */
        $company = $this->route('company');

        return [
            'title' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'category' => ['nullable', Rule::enum(ProcedureCategory::class)],
            'version' => ['nullable', 'string', 'max:32'],
            'summary' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'steps' => ['nullable', 'string'],
            'policy_document_id' => [
                'nullable',
                'integer',
                Rule::exists('policy_documents', 'id')->where(fn ($q) => $q->where('company_id', $company->id)),
            ],
            'effective_from' => ['nullable', 'date'],
            'review_date' => ['nullable', 'date'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(DocumentStatus::class)],
        ];
    }
}
