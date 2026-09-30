<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Composite indexes for the public catalogue queries.
     *
     * The published scope filters on (status, published_at) and the listings
     * additionally filter by resource_type, main_category_id or subcategory_id
     * before ordering by published_at (default), downloads_count or rating.
     * These indexes match those exact access patterns.
     */
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->index(['status', 'published_at'], 'resources_status_published_at_index');
            $table->index(['status', 'resource_type', 'published_at'], 'resources_status_type_published_index');
            $table->index(['status', 'main_category_id', 'published_at'], 'resources_status_maincat_published_index');
            $table->index(['status', 'subcategory_id', 'published_at'], 'resources_status_subcat_published_index');
            $table->index(['status', 'downloads_count'], 'resources_status_downloads_index');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->index(['status', 'published_at'], 'articles_status_published_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropIndex('resources_status_published_at_index');
            $table->dropIndex('resources_status_type_published_index');
            $table->dropIndex('resources_status_maincat_published_index');
            $table->dropIndex('resources_status_subcat_published_index');
            $table->dropIndex('resources_status_downloads_index');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('articles_status_published_at_index');
        });
    }
};
