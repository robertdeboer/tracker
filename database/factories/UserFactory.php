<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Team;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            User::FIRST_NAME                => $this->faker->unique()->firstName(),
            User::LAST_NAME                 => $this->faker->unique()->lastName(),
            User::EMAIL                     => $this->faker->unique()->safeEmail(),
            User::EMAIL_VERIFIED_AT         => now(),
            User::PASSWORD                  => Hash::make('password'),
            User::TWO_FACTOR_SECRET         => null,
            User::TWO_FACTOR_RECOVERY_CODES => null,
            User::REMEMBER_TOKEN            => Str::random(10),
            User::PROFILE_PHOTO_PATH        => null,
            User::CURRENT_TEAM_ID           => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(
            function (array $attributes) {
                return [
                    User::EMAIL_VERIFIED_AT => null,
                ];
            }
        );
    }

    /**
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(callable $callback = null): static
    {
        if (!Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(
                    fn (array $attributes, User $user) => [
                        'name'          => $user->first_name . ' ' . $user->last_name . '\'s Team',
                        'user_id'       => $user->id,
                        'personal_team' => true,
                    ]
                )
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }
}
