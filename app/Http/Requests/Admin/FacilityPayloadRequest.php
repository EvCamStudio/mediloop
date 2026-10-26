<?php

namespace App\Http\Requests\Admin;

use App\Enums\FacilityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FacilityPayloadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSysadmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => Str::upper(trim((string) $this->input('code')))]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $facility = $this->route('facility');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required', 'string', 'max:40', 'alpha_dash',
                Rule::unique('facilities', 'code')->ignore($facility),
            ],
            'type' => ['required', 'string', Rule::in(FacilityType::operationalValues())],
            'ownership' => ['nullable', 'string', Rule::in(['PEMERINTAH', 'SWASTA', 'LAINNYA'])],
            'license_number' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'cold_chain_ready' => ['boolean'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('admin.validation.name_required'),
            'code.required' => __('admin.validation.code_required'),
            'code.unique' => __('admin.validation.code_unique'),
            'type.required' => __('admin.validation.type_required'),
            'address.required' => __('admin.validation.address_required'),
            'city.required' => __('admin.validation.city_required'),
            'province.required' => __('admin.validation.province_required'),
            'latitude.required' => __('admin.validation.location_required'),
            'longitude.required' => __('admin.validation.location_required'),
            'contact_person.required' => __('admin.validation.contact_person_required'),
            'phone.required' => __('admin.validation.phone_required'),
            'email.email' => __('admin.validation.email_invalid'),
        ];
    }
}
