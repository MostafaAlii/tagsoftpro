<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_panel_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('system_status')->default(\App\Enums\MainSetting\MainSettingSystemStatus::SYSTEM_STATUS_ACTIVE);
            $table->string('company_name')->nullable();
            $table->longText('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->foreignId('added_by_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_panel_settings');
    }
};
