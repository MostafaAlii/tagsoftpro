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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->enum('status', ['active', 'inactive', 'published', 'draft'])->default('draft');
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete()->comment('الشركة');
            $table->foreignId('created_by')->nullable()->constrained('admins')->cascadeOnDelete()->comment('أنشئ بواسطة');
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete()->comment('عدل بواسطة');
            $table->timestamps();
        });
        Schema::create('project_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['project_id', 'locale']);
        });
        Schema::create('project_project_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_type_id')->constrained()->cascadeOnDelete();
            $table->unique(['project_id', 'project_type_id']);
            $table->timestamps();
        });
        Schema::create('project_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->unique(['project_id', 'module_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_modules');
        Schema::dropIfExists('project_project_types');
        Schema::dropIfExists('project_translations');
        Schema::dropIfExists('projects');
    }
};