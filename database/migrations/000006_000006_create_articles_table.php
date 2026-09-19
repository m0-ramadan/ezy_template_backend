<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('articles',function(Blueprint $t){$t->id();$t->string('title');$t->string('slug')->unique();$t->text('excerpt')->nullable();$t->longText('content')->nullable();$t->string('cover_image')->nullable();$t->string('category')->nullable();$t->string('author_name')->nullable();$t->unsignedSmallInteger('reading_time')->default(5);$t->enum('status',['draft','published','archived'])->default('draft');$t->timestamp('published_at')->nullable();$t->string('meta_title')->nullable();$t->text('meta_description')->nullable();$t->timestamps();}); } public function down(): void { Schema::dropIfExists('articles'); } };
