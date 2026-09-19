<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visitor_sessions', function (Blueprint $t) {
            $t->id();
            $t->string('visitor_hash', 64);
            $t->string('session_id', 100)->nullable();
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->string('device_type', 30)->nullable();
            $t->string('browser', 80)->nullable();
            $t->string('os', 80)->nullable();
            $t->string('country', 100)->nullable();
            $t->string('city', 100)->nullable();
            $t->timestamp('first_seen')->useCurrent();
            $t->timestamp('last_seen')->useCurrent();
            $t->unsignedInteger('page_views')->default(0);
            $t->unsignedInteger('download_count')->default(0);
            $t->timestamps();
            $t->index(['visitor_hash', 'last_seen']);
            $t->index('first_seen');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('visitor_sessions');
    }
};
