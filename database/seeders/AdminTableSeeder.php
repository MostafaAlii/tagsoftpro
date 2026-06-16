<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Enums\Admin\{AdminType,AdminStatus};
class AdminTableSeeder extends Seeder {
    public function run(): void {
        Admin::factory()->create([
            'name' => 'Super Admin',
            'email' => 'owner@app.com',
            'status' => AdminStatus::ACTIVE->value,
            'type' => AdminType::OWNER->value,
            'company_id' => null,
            'password' => bcrypt('123123'),
        ]);
        Admin::factory()->count(20)->create();
    }
}