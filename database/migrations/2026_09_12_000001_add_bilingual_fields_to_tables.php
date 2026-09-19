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
        Schema::table('resources', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title');
            $table->text('short_description_ar')->nullable()->after('short_description');
            $table->longText('description_ar')->nullable()->after('description');
            $table->json('features_ar')->nullable()->after('features');
            $table->string('tech_stack_ar')->nullable()->after('tech_stack');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
            $table->text('description_ar')->nullable()->after('description');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title');
            $table->text('excerpt_ar')->nullable()->after('excerpt');
            $table->longText('content_ar')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn([
                'title_ar',
                'short_description_ar',
                'description_ar',
                'features_ar',
                'tech_stack_ar',
            ]);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'description_ar']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'excerpt_ar', 'content_ar']);
        });
    }
};
