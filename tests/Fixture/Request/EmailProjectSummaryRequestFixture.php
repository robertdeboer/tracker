<?php

declare(strict_types=1);

namespace Tests\Fixture\Request;

class EmailProjectSummaryRequestFixture
{
    public const VALID_REQUEST = [
        'id'    => 1,
        'start' => '2023-12-01',
        'end'   => '2023-01-01',
        'email' => [
            'test@test.com'
        ]
    ];
}
