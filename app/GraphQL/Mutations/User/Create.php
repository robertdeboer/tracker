<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\User;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final readonly class Create
{
    /**
     * @param null  $_
     * @param array $args
     *
     * @return User
     * @throws Exception
     */
    public function __invoke(null $_, array $args): User
    {
        $user = new User(
            [
                User::FIRST_NAME     => $args[User::FIRST_NAME],
                User::LAST_NAME      => $args[User::LAST_NAME],
                User::EMAIL          => $args[User::EMAIL],
                User::PASSWORD       => Hash::make(
                    bin2hex(
                        random_bytes(15)
                    )
                ),
                User::REMEMBER_TOKEN => Str::random(10),
            ]
        );
        $user->save();
        return $user;
    }
}
