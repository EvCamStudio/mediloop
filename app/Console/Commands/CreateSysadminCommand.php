<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSysadminCommand extends Command
{
    protected $signature = 'mediloop:create-sysadmin
                            {email : Email address for the platform administrator}
                            {--name= : Display name}
                            {--password= : Password (prompted when omitted)}';

    protected $description = 'Create or promote a MediLoop platform administrator';

    public function handle(): int
    {
        $emailArgument = $this->argument('email');

        if (! is_string($emailArgument) || $emailArgument === '') {
            $this->error('Email administrator wajib diisi.');

            return self::FAILURE;
        }

        $nameOption = $this->option('name');
        $name = is_string($nameOption) && $nameOption !== '' ? $nameOption : 'Administrator MediLoop';

        $passwordOption = $this->option('password');
        $password = is_string($passwordOption) && $passwordOption !== ''
            ? $passwordOption
            : ($this->secret('Password (min. 8 characters)') ?? '');

        $email = $emailArgument;

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->role = UserRole::Sysadmin->value;
        $user->status = UserStatus::Active->value;
        $user->facility_id = null;
        $user->email_verified_at = Carbon::now();
        $user->password = Hash::make($password);
        $user->save();

        $this->info("Platform administrator ready: {$user->email}");

        return self::SUCCESS;
    }
}
