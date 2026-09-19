<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subcategories', function (Blueprint $table) {
            if (!Schema::hasColumn('subcategories', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('description');
            }
        });

        Schema::table('resources', function (Blueprint $table) {
            if (!Schema::hasColumn('resources', 'seo_title')) {
                $table->string('seo_title', 180)->nullable()->after('demo_url');
            }
            if (!Schema::hasColumn('resources', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }
            if (!Schema::hasColumn('resources', 'seo_title_ar')) {
                $table->string('seo_title_ar', 180)->nullable()->after('seo_description');
            }
            if (!Schema::hasColumn('resources', 'seo_description_ar')) {
                $table->text('seo_description_ar')->nullable()->after('seo_title_ar');
            }
            if (!Schema::hasColumn('resources', 'seo_keywords')) {
                $table->json('seo_keywords')->nullable()->after('seo_description_ar');
            }
            if (!Schema::hasColumn('resources', 'image_alt')) {
                $table->string('image_alt', 255)->nullable()->after('seo_keywords');
            }
            if (!Schema::hasColumn('resources', 'image_alt_ar')) {
                $table->string('image_alt_ar', 255)->nullable()->after('image_alt');
            }
            if (!Schema::hasColumn('resources', 'canva_design_id')) {
                $table->string('canva_design_id', 100)->nullable()->index()->after('image_alt_ar');
            }
            if (!Schema::hasColumn('resources', 'canva_url')) {
                $table->text('canva_url')->nullable()->after('canva_design_id');
            }
            if (!Schema::hasColumn('resources', 'source_url')) {
                $table->text('source_url')->nullable()->after('canva_url');
            }
            if (!Schema::hasColumn('resources', 'external_url')) {
                $table->text('external_url')->nullable()->after('source_url');
            }
            if (!Schema::hasColumn('resources', 'gallery_status')) {
                $table->string('gallery_status', 30)->default('placeholder')->after('external_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            foreach ([
                'seo_title', 'seo_description', 'seo_title_ar', 'seo_description_ar', 'seo_keywords',
                'image_alt', 'image_alt_ar', 'canva_design_id', 'canva_url', 'source_url',
                'external_url', 'gallery_status'
            ] as $column) {
                if (Schema::hasColumn('resources', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('subcategories', function (Blueprint $table) {
            if (Schema::hasColumn('subcategories', 'description_ar')) {
                $table->dropColumn('description_ar');
            }
        });
    }
};
