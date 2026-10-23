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
            'name.required' => 'Nama fasilitas wajib diisi.',
            'code.required' => 'Kode fasilitas wajib diisi.',
            'code.unique' => 'Kode fasilitas sudah digunakan.',
            'type.required' => 'Pilih jenis fasilitas.',
            'address.required' => 'Alamat wajib diisi.',
            'city.required' => 'Kota/kabupaten wajib diisi.',
            'province.required' => 'Provinsi wajib diisi.',
            'latitude.required' => 'Tentukan titik lokasi pada peta.',
            'longitude.required' => 'Tentukan titik lokasi pada peta.',
            'contact_person.required' => 'Nama penanggung jawab wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ];
    }
}
