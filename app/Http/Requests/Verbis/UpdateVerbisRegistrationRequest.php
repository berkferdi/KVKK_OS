<?php

namespace App\Http\Requests\Verbis;

use App\Domain\Verbis\Enums\VerbisRegistrationStatus;
use App\Domain\Verbis\Models\VerbisRegistration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVerbisRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var VerbisRegistration $registration */
        $registration = $this->route('registration');

        return $this->user()?->can('updateRegistration', $registration) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'registration_number' => ['nullable', 'string', 'max:64'],
            'registered_at' => ['nullable', 'date'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:64'],
            'is_exempt' => ['nullable', 'boolean'],
            'exemption_reason' => ['nullable', 'string'],
            'last_reviewed_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(VerbisRegistrationStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('is_exempt')) {
            $this->merge(['is_exempt' => false]);
        }
    }
}
