<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'facility_id' => ['required', 'integer', Rule::exists(Facility::class, 'id')->whereNot('type', 'DINKES')],
            'role' => ['required', 'string', Rule::in(['DONOR_STAFF', 'RECIPIENT_STAFF'])],
            'job_title' => ['nullable', 'string', 'max:255'],
            'license_sip' => ['nullable', 'string', 'max:255'],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'facility_id' => (int) $input['facility_id'],
            'role' => $input['role'],
            'job_title' => $input['job_title'] ?? null,
            'license_sip' => $input['license_sip'] ?? null,
        ]);
    }
}
