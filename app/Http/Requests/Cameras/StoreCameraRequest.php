<?php

namespace App\Http\Requests\Cameras;

use App\Domain\Cameras\Enums\CameraStatus;
use App\Domain\Cameras\Enums\CameraType;
use App\Domain\Cameras\Models\Camera;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCameraRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [Camera::class, $company]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Company $company */
        $company = $this->route('company');

        return [
            'name' => ['required', 'string', 'max:255'],
            'camera_code' => ['nullable', 'string', 'max:64'],
            'camera_type' => ['nullable', Rule::enum(CameraType::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'coverage_area' => ['nullable', 'string', 'max:255'],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $company->id)),
            ],
            'is_recording' => ['nullable', 'boolean'],
            'records_audio' => ['nullable', 'boolean'],
            'retention_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'storage_location' => ['nullable', 'string', 'max:255'],
            'notice_posted' => ['nullable', 'boolean'],
            'notice_posted_at' => ['nullable', 'date'],
            'installed_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(CameraStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['is_recording', 'records_audio', 'notice_posted'] as $flag) {
            if (! $this->has($flag)) {
                $this->merge([$flag => false]);
            }
        }
    }
}
