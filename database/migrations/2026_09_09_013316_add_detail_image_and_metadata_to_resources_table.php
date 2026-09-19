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
            $table->string('detail_image', 500)->nullable()->after('preview_image');
            $table->json('screenshots')->nullable()->after('detail_image');
            $table->string('tech_stack', 255)->nullable()->after('resource_type');
            $table->json('features')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['detail_image', 'screenshots', 'tech_stack', 'features']);
        });
    }
};
