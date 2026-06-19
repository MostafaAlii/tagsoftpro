<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Company;
use App\Models\Department;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $statuses = ['active', 'inactive', 'on_leave', 'terminated'];
        $types = ['full_time', 'part_time', 'contractor', 'intern', 'remote'];

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => $this->faker->optional(0.8)->dateTime(),
            'phone' => $this->faker->optional(0.9)->phoneNumber(),
            'status' => $this->faker->randomElement($statuses),
            'type' => $this->faker->randomElement($types),
            'password' => Hash::make('password'),
            'date' => $this->faker->optional(0.9)->dateTimeBetween('-5 years', 'now'),
            'company_id' => null, // هيتعبأ من الـ Seeder
            'department_id' => null, // هيتعبأ من الـ Seeder
            'created_by' => null, // هيتعبأ من الـ Seeder
            'updated_by' => null, // هيتعبأ من الـ Seeder
            'remember_token' => $this->faker->optional(0.5)->md5(),
            'deleted_at' => $this->faker->optional(0.1)->dateTimeBetween('-30 days', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // ─── States ──────────────────────────────────────────────────────────
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function onLeave(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'on_leave',
        ]);
    }

    public function terminated(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'terminated',
        ]);
    }

    public function trashed(): static
    {
        return $this->state(fn(array $attributes) => [
            'deleted_at' => now(),
        ]);
    }

    // ─── Relations ──────────────────────────────────────────────────────
    public function withCompany(): static
    {
        return $this->state(fn(array $attributes) => [
            'company_id' => Company::inRandomOrder()->first()?->id,
        ]);
    }

    public function withDepartment(): static
    {
        return $this->state(fn(array $attributes) => [
            'department_id' => Department::inRandomOrder()->first()?->id,
        ]);
    }

    public function withAdmin(): static
    {
        return $this->state(fn(array $attributes) => [
            'created_by' => Admin::inRandomOrder()->first()?->id,
            'updated_by' => Admin::inRandomOrder()->first()?->id,
        ]);
    }
}