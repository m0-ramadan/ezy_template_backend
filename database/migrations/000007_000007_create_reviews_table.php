<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('reviews',function(Blueprint $t){$t->id();$t->foreignId('resource_id')->constrained()->cascadeOnDelete();$t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();$t->string('name');$t->string('email')->nullable();$t->unsignedTinyInteger('rating');$t->text('body');$t->enum('status',['pending','approved','rejected'])->default('pending');$t->timestamps();}); } public function down(): void { Schema::dropIfExists('reviews'); } };
