<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use App\Enums\Client\ClientStatus;
/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password' => '123123',
            'status' => $this->faker->randomElement([
                ClientStatus::ACTIVE->value,
                ClientStatus::IN_ACTIVE->value,
                ClientStatus::BLOCKED->value,
            ]),
        ];
    }
}