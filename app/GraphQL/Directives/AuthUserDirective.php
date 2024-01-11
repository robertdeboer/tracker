<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use App\Models\User;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgDirective;
use Nuwave\Lighthouse\Support\Contracts\ArgTransformerDirective;

final class AuthUserDirective extends BaseDirective implements ArgDirective, ArgTransformerDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
If no ID is provided, then the currently authenticated user's ID is used
"""
directive @authUser on INPUT_FIELD_DEFINITION
GRAPHQL;
    }

    /**
     * Apply transformations on the value of an argument given to a field.
     *
     * @param mixed $argumentValue the client given value
     *
     * @return mixed the transformed value
     */
    public function transform(mixed $argumentValue): mixed
    {
        $user = auth()->user();
        if (empty($argumentValue) && $user instanceof User) {
            return $user->id;
        }
        return $argumentValue;
    }
}
