<?php

namespace App\Http\Requests\Documents;

use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Enums\ProcedureCategory;
use App\Domain\Documents\Models\ProcedureDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProcedureDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var ProcedureDocument $procedure */
        $procedure = $this->route('procedure');

        return $this->user()?->can('update', $procedure) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var ProcedureDocument $procedure */
        $procedure = $this->route('procedure');

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
                Rule::exists('policy_documents', 'id')->where(fn ($q) => $q->where('company_id', $procedure->company_id)),
            ],
            'effective_from' => ['nullable', 'date'],
            'review_date' => ['nullable', 'date'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(DocumentStatus::class)],
        ];
    }
}
