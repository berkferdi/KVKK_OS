<?php

namespace App\Http\Requests\Documents;

use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Enums\PolicyCategory;
use App\Domain\Documents\Models\PolicyDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePolicyDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var PolicyDocument $policy */
        $policy = $this->route('policy');

        return $this->user()?->can('update', $policy) ?? false;
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
