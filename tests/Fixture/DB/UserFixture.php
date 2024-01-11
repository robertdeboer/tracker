<?php

declare(strict_types=1);

namespace Tests\Fixture\DB;

use App\Models\User;

class UserFixture
{
    public const VALID_DATA = [
        User::FIRST_NAME        => 'John',
        User::LAST_NAME         => 'Doe',
        User::EMAIL             => 'j.doe@test.com',
        User::PASSWORD          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ];
}
