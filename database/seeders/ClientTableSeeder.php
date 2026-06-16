<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientTableSeeder extends Seeder {
    public function run(): void {
        Client::factory()->count(10)->create();
        Client::factory()->create([
            'name' => 'Test Client',
            'email' => 'test@client.com',
            'status' => 'active',
        ]);
    }
}