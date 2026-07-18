<?php

namespace App\Http\Requests\Documents;

use App\Domain\Documents\Enums\TemplateCategory;
use App\Domain\Documents\Models\DocumentTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var DocumentTemplate $template */
        $template = $this->route('document_template');

        return $this->user()?->can('update', $template) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var DocumentTemplate $template */
        $template = $this->route('document_template');

        return [
            'code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('document_templates', 'code')
                    ->where(fn ($q) => $q->where('tenant_id', $template->tenant_id))
                    ->ignore($template->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', Rule::enum(TemplateCategory::class)],
            'description' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('is_active')) {
            $this->merge(['is_active' => false]);
        }
    }
}
