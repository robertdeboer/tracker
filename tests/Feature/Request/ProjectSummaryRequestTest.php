<?php

declare(strict_types=1);

namespace Feature\Request;

use App\Http\Requests\ProjectSummaryRequest;
use Tests\Fixture\Request\ProjectSummaryRequestFixture;
use Tests\TestCase;

class ProjectSummaryRequestTest extends TestCase
{
    /**
     * Validate a valid request passes
     *
     * @test
     * @dataProvider validDataDataProvider
     *
     * @param array $data
     *
     * @return void
     */
    public function validData(array $data): void
    {
        $request   = new ProjectSummaryRequest();
        $validator = \Validator::make(
            $data,
            $request->rules()
        );
        $this->assertTrue($validator->passes());
    }

    public static function validDataDataProvider(): array
    {
        return [
            'ID only'                      => [ProjectSummaryRequestFixture::VALID_REQUEST],
            'ID and start date only'       => [
                [
                    ...ProjectSummaryRequestFixture::VALID_REQUEST,
                    'startDate' => '2023-12-01'
                ]
            ],
            'ID, start date, and end date' => [['id' => 1, 'startDate' => '2023-12-01', 'endDate' => '2023-01-01']],
        ];
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
        $request   = new ProjectSummaryRequest();
        $validator = \Validator::make(
            [
                ...ProjectSummaryRequestFixture::VALID_REQUEST,
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
            'Invalid start date'    => ['startDate', 'dog'],
            'Invalid end date'      => ['endDate', 'dog'],
        ];
    }
}
