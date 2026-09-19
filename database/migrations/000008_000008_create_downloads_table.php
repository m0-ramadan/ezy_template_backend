<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('downloads',function(Blueprint $t){$t->id();$t->foreignId('resource_id')->constrained()->cascadeOnDelete();$t->foreignId('resource_file_id')->constrained()->cascadeOnDelete();$t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();$t->string('ip_address',45)->nullable();$t->text('user_agent')->nullable();$t->string('quality',40)->nullable();$t->uuid('download_token')->unique();$t->timestamps();$t->index(['resource_id','created_at']);}); } public function down(): void { Schema::dropIfExists('downloads'); } };
