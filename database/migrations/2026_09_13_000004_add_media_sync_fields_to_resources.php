<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            if (!Schema::hasColumn('resources', 'media_sync_status')) {
                $table->string('media_sync_status', 32)->default('pending')->after('gallery_status');
            }
            if (!Schema::hasColumn('resources', 'media_synced_at')) {
                $table->timestamp('media_synced_at')->nullable()->after('media_sync_status');
            }
            if (!Schema::hasColumn('resources', 'media_width')) {
                $table->unsignedSmallInteger('media_width')->nullable()->after('media_synced_at');
            }
            if (!Schema::hasColumn('resources', 'media_height')) {
                $table->unsignedSmallInteger('media_height')->nullable()->after('media_width');
            }
            if (!Schema::hasColumn('resources', 'media_source_meta')) {
                $table->json('media_source_meta')->nullable()->after('media_height');
            }
            if (!Schema::hasColumn('resources', 'media_sync_message')) {
                $table->text('media_sync_message')->nullable()->after('media_source_meta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            foreach (['media_sync_status','media_synced_at','media_width','media_height','media_source_meta','media_sync_message'] as $column) {
                if (Schema::hasColumn('resources', $column)) $table->dropColumn($column);
            }
        });
    }
};
