<?php

namespace Database\Seeders;

use App\Models\{Employee, Company, Department, Admin};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Schema};

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ─── 1. تعطيل قيود المفاتيح الأجنبية ──────────────────────
        Schema::disableForeignKeyConstraints();
        DB::table('employees')->delete();
        Schema::enableForeignKeyConstraints();

        // ─── 2. جلب البيانات الموجودة (لو موجودة) ──────────────────
        $companies = Company::pluck('id')->toArray();
        $departments = Department::pluck('id')->toArray();
        $admins = Admin::pluck('id')->toArray();

        // ─── 3. دالة مساعدة لجلب ID عشوائي أو null ────────────────
        $randomOrNull = function ($array) {
            return empty($array) ? null : $array[array_rand($array)];
        };

        // ─── 4. إنشاء 20 موظف ────────────────────────────────────────
        $this->command->info('🔄 جاري إنشاء 20 موظف...');

        Employee::factory(20)
            ->sequence(fn($sequence) => [
                'company_id' => $randomOrNull($companies),
                'department_id' => $randomOrNull($departments),
                'created_by' => $randomOrNull($admins),
                'updated_by' => $randomOrNull($admins),
            ])
            ->create();

        // ─── 5. عرض الإحصائيات ──────────────────────────────────────
        $total = Employee::count();
        $active = Employee::where('status', 'active')->count();
        $trashed = Employee::onlyTrashed()->count();

        $this->command->info('✅ تم إنشاء الموظفين بنجاح!');
        $this->command->info("📊 الإحصائيات:");
        $this->command->info("   ──────────────────────────");
        $this->command->info("   📌 إجمالي الموظفين    : {$total}");
        $this->command->info("   🟢 نشطين             : {$active}");
        $this->command->info("   🗑️  محذوفين           : {$trashed}");
        $this->command->info("   ──────────────────────────");
    }
}