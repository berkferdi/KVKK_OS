<?php

namespace App\Http\Requests\Documents;

use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Enums\PolicyCategory;
use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePolicyDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [PolicyDocument::class, $company]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'category' => ['nullable', Rule::enum(PolicyCategory::class)],
            'version' => ['nullable', 'string', 'max:32'],
            'summary' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'effective_from' => ['nullable', 'date'],
            'review_date' => ['nullable', 'date'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(DocumentStatus::class)],
        ];
    }
}
