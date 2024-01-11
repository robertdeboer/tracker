<?php

namespace App\Actions\Fortify;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     */
    public function create(array $input): User
    {
        Validator::make(
            $input,
            [
                User::FIRST_NAME => ['required', 'string', 'max:125'],
                User::LAST_NAME  => ['required', 'string', 'max:125'],
                User::EMAIL      => ['required', 'string', 'email', 'max:255', 'unique:users'],
                User::PASSWORD   => $this->passwordRules(),
                'terms'          => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            ]
        )->validate();

        $user = User::create(
            [
                User::FIRST_NAME => $input[User::FIRST_NAME],
                User::LAST_NAME  => $input[User::LAST_NAME],
                User::EMAIL      => $input[User::EMAIL],
                User::PASSWORD   => Hash::make($input[User::PASSWORD]),
            ]
        );
        $user->assignRole(Roles::SUPER_ADMIN);
        return $user;
    }
}
