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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->enum('type', ['link', 'dropdown', 'header', 'divider'])->default('link');
            $table->string('icon')->nullable();
            $table->enum('link_type', ['route', 'url', 'none'])->nullable();
            $table->string('route_name')->nullable();
            $table->json('route_params')->nullable();
            $table->string('url')->nullable();
            $table->enum('target', ['_self', '_blank'])->default('_self');
            $table->boolean('is_owner_only')->default(false);
            $table->string('permission_name')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('badge_color')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('visible_from')->nullable();
            $table->timestamp('visible_until')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
            $table->index('company_id');
        });
        Schema::create('menu_item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained('menu_items')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('title');
            $table->string('description')->nullable();
            $table->unique(['menu_item_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_translations');
        Schema::dropIfExists('menu_items');
    }
};