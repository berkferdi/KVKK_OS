<?php

namespace App\Http\Requests\Documents;

use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGeneratedDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [GeneratedDocument::class, $company]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Company $company */
        $company = $this->route('company');

        return [
            'document_template_id' => [
                'required',
                'integer',
                Rule::exists('document_templates', 'id')->where(
                    fn ($q) => $q->where('tenant_id', $company->tenant_id)->where('is_active', true)
                ),
            ],
        ];
    }
}
