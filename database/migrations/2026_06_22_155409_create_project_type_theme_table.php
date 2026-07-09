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
        Schema::create('project_type_theme', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_type_id')->constrained('project_types')->cascadeOnDelete();
                $table->foreignId('theme_id')->constrained('themes')->cascadeOnDelete();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false);
                $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
                $table->timestamps();
                $table->unique(['project_type_id', 'theme_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_type_theme');
    }
};
