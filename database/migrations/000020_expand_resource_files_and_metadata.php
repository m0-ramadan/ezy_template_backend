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
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'main_category_id')) {
                $table->foreignId('main_category_id')->nullable()->after('id')->constrained('main_categories')->onDelete('set null');
            }
        });

        Schema::table('resources', function (Blueprint $table) {
            if (!Schema::hasColumn('resources', 'main_category_id')) {
                $table->foreignId('main_category_id')->nullable()->after('category_id')->constrained('main_categories')->onDelete('set null');
            }
            if (!Schema::hasColumn('resources', 'subcategory_id')) {
                $table->foreignId('subcategory_id')->nullable()->after('main_category_id')->constrained('subcategories')->onDelete('set null');
            }
            if (!Schema::hasColumn('resources', 'resource_type')) {
                $table->string('resource_type')->default('website')->after('type');
            }
            if (!Schema::hasColumn('resources', 'license_type')) {
                $table->string('license_type')->default('free')->after('is_free');
            }
        });

        Schema::table('resource_files', function (Blueprint $table) {
            if (!Schema::hasColumn('resource_files', 'quality')) {
                $table->string('quality')->nullable();
            }
            if (!Schema::hasColumn('resource_files', 'version')) {
                $table->string('version')->nullable();
            }
            if (!Schema::hasColumn('resource_files', 'is_primary')) {
                $table->boolean('is_primary')->default(false);
            }
            if (!Schema::hasColumn('resource_files', 'view_count')) {
                $table->unsignedBigInteger('view_count')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_files', function (Blueprint $table) {
            $table->dropColumn(['quality', 'version', 'is_primary', 'view_count']);
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['main_category_id']);
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn(['main_category_id', 'subcategory_id', 'resource_type', 'license_type']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['main_category_id']);
            $table->dropColumn(['main_category_id']);
        });
    }
};
