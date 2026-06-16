<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanyTableSeeder extends Seeder {
    public function run(): void {
        Company::factory()->count(10)->create();
        Company::factory()->create([
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'status' => 'active',
        ]);
    }
}