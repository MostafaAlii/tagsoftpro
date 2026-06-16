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
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->enum('type', ['ui', 'ordering', 'analytics']);
            $table->enum('scope', ['main', 'addon']);
            $table->boolean('status')->default(true);
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete()->comment('الشركة');
            $table->foreignId('created_by')->nullable()->constrained('admins')->cascadeOnDelete()->comment('أنشئ بواسطة');
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete()->comment('عدل بواسطة');
            $table->timestamps();
        });
        Schema::create('feature_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->string('key')->nullable();
            $table->unique(['feature_id', 'locale']);
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->decimal('price', 8, 2);
            $table->enum('billing_cycle', ['monthly', 'yearly']);
            $table->boolean('status')->default(true);
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete()->comment('الشركة');
            $table->foreignId('created_by')->nullable()->constrained('admins')->cascadeOnDelete()->comment('أنشئ بواسطة');
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete()->comment('عدل بواسطة');
            $table->timestamps();
        });

        Schema::create('plan_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['plan_id', 'locale']);
        });

        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_included')->default(true);
            $table->integer('limit')->nullable();
            $table->unique(['plan_id', 'feature_id']);
            $table->timestamps();
        });
        Schema::create('company_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->unique(['company_id', 'feature_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_translations');
        Schema::dropIfExists('features');
        Schema::dropIfExists('plan_translations');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('company_features');
    }
};