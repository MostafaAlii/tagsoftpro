<?php
namespace Database\Factories;
use App\Models\{Admin,Company};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Enums\Admin\{AdminType,AdminStatus};
use App\Enums\Company\CompanyStatus;
class AdminFactory extends Factory {
    protected $model = Admin::class;
    public function definition(): array {
        $activeCompanyIds = Company::whereStatus(CompanyStatus::ACTIVE->value)->pluck('id')->toArray();
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'status' => $this->faker->randomElement([
                AdminStatus::ACTIVE->value,
                AdminStatus::IN_ACTIVE->value,
                AdminStatus::BLOCKED->value,
            ]),
            'type' => $this->faker->randomElement([
                AdminType::COMPANY_ADMIN->value,
                AdminType::BRANCH_ADMIN->value,
            ]),
            'password' => bcrypt('123123'),
            'date' => $this->faker->date(),
            'company_id' => !empty($activeCompanyIds)
                ? $this->faker->randomElement($activeCompanyIds)
                : null,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}