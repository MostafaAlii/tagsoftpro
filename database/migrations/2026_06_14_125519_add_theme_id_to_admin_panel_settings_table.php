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
        Schema::table('admin_panel_settings', function (Blueprint $table) {
            $table->foreignId('theme_id')
                ->nullable()
                ->after('company_id')
                ->constrained('themes')
                ->nullOnDelete()
                ->comment('Active dashboard theme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_panel_settings', function (Blueprint $table) {
            $table->dropForeign(['theme_id']);
            $table->dropColumn('theme_id');
        });
    }
};
