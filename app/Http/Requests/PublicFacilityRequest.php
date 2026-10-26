<?php

namespace App\Http\Requests;

use App\Enums\FacilityType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PublicFacilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required', 'string', 'max:40', 'alpha_dash',
                Rule::unique('facilities', 'code'),
                Rule::unique('facility_requests', 'code')
                    ->where(fn ($query) => $query->where('status', '!=', 'REJECTED')),
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
            'email' => ['required', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('facility.request.name_required'),
            'code.unique' => __('facility.request.code_unique'),
            'type.required' => __('facility.request.type_required'),
            'address.required' => __('facility.request.address_required'),
            'city.required' => __('facility.request.city_required'),
            'province.required' => __('facility.request.province_required'),
            'latitude.required' => __('facility.request.coordinates_required'),
            'longitude.required' => __('facility.request.coordinates_required'),
            'contact_person.required' => __('facility.request.contact_person_required'),
            'phone.required' => __('facility.request.phone_required'),
            'email.required' => __('facility.request.email_required'),
            'email.email' => __('facility.request.email_invalid'),
        ];
    }
}
