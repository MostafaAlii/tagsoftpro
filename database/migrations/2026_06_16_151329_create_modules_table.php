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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->boolean('status')->default(true);
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete()->comment('الشركة');
            $table->foreignId('created_by')->nullable()->constrained('admins')->cascadeOnDelete()->comment('أنشئ بواسطة');
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete()->comment('عدل بواسطة');
            $table->timestamps();
        });

        Schema::create('module_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['module_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_translations');
        Schema::dropIfExists('modules');
    }
};