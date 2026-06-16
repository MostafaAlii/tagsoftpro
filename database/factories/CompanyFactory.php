<?php

namespace Database\Factories;

use App\Models\Company;
use App\Enums\Company\CompanyStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory {
    protected $model = Company::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'status' => $this->faker->randomElement([
                CompanyStatus::ACTIVE->value,
                CompanyStatus::IN_ACTIVE->value,
                CompanyStatus::BLOCKED->value,
            ]),
            'password' => bcrypt('123123'),
            'date' => $this->faker->date(),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}