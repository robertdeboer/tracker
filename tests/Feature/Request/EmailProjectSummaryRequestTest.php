<?php

declare(strict_types=1);

namespace Tests\Feature\Request;

use App\Http\Requests\EmailProjectSummaryRequest;
use Tests\Fixture\Request\EmailProjectSummaryRequestFixture;
use Tests\TestCase;

class EmailProjectSummaryRequestTest extends TestCase
{
    /**
     * Validate a valid request passes
     *
     * @test
     * @return void
     */
    public function validData(): void
    {
        $request   = new EmailProjectSummaryRequest();
        $validator = \Validator::make(
            EmailProjectSummaryRequestFixture::VALID_REQUEST,
            $request->rules()
        );
        $this->assertTrue($validator->passes());
    }

    /**
     * Validate an invalid request
     *
     * @param string $node
     * @param mixed  $value
     *
     * @test
     * @dataProvider invalidDataDataProvider
     * @return void
     */
    public function invalidData(string $node, mixed $value): void
    {
        $request   = new EmailProjectSummaryRequest();
        $validator = \Validator::make(
            [
                ...EmailProjectSummaryRequestFixture::VALID_REQUEST,
                $node => $value
            ],
            $request->rules()
        );
        $this->assertFalse($validator->passes());
    }

    /**
     * Data provider for invalidData
     *
     * @return array{string, array<string, mixed>}
     */
    public static function invalidDataDataProvider(): array
    {
        return [
            'No id'                 => ['id', null],
            'Invalid id'            => ['id', 'dog'],
            'No start date'         => ['start', null],
            'Invalid start date'    => ['start', 'dog'],
            'No end date'           => ['end', null],
            'Invalid end date'      => ['end', 'dog'],
            'Invalid email in list' => ['email', ['test@test.com', 'dog']]
        ];
    }
}
