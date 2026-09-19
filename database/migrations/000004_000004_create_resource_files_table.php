<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('resource_files',function(Blueprint $t){$t->id();$t->foreignId('resource_id')->constrained()->cascadeOnDelete();$t->string('label');$t->string('path');$t->string('original_name');$t->string('extension',20)->nullable();$t->string('mime_type')->nullable();$t->unsignedBigInteger('size_bytes')->default(0);$t->string('quality',40)->nullable();$t->string('format',40)->nullable();$t->boolean('is_primary')->default(false);$t->unsignedInteger('sort_order')->default(0);$t->timestamps();}); } public function down(): void { Schema::dropIfExists('resource_files'); } };
