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
        Schema::create('project_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->boolean('status')->default(true);
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete()->comment('الشركة');
            $table->foreignId('created_by')->nullable()->constrained('admins')->cascadeOnDelete()->comment('أنشئ بواسطة');
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete()->comment('عدل بواسطة');
            $table->timestamps();
        });

        Schema::create('project_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['project_type_id', 'locale']);
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->foreignId('project_type_id')->nullable()->after('status')->constrained('project_types')->nullOnDelete()->comment('فئه المشروع التابع');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropColumn('project_type_id');
        });
        Schema::dropIfExists('project_type_translations');
        Schema::dropIfExists('project_types');
    }
};