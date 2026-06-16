<?php
namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Theme;
use App\Enums\Theme\{ThemeStatus,ThemeDefault};
class ThemeSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Theme::insert([
            [
                'uuid'        => (string) \Str::uuid(),
                'name'        => 'Default Theme',
                'code'        => 'default',
                'description' => 'System default theme',
                'is_active'   => ThemeStatus::ACTIVE,
                'is_default'  => ThemeDefault::YES,
                'company_id'  => null,
                'created_by'  => null,
                'updated_by'  => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
