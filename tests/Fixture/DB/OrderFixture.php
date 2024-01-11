<?php

declare(strict_types=1);

namespace Tests\Fixture\DB;

use App\Models\Order;

class OrderFixture
{
    public const VALID_DATA = [
        Order::REFERENCE_NUMBER => '123456789',
        Order::DATE             => '2023-01-01 12:00:00',
        Order::EMAIL            => 'big.bird@sesame.str',
        Order::HOURS            => 75,
        Order::PROJECT_ID       => null
    ];
}
