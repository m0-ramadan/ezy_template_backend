<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('tags',function(Blueprint $t){$t->id();$t->string('name');$t->string('slug')->unique();$t->timestamps();});Schema::create('resource_tag',function(Blueprint $t){$t->foreignId('resource_id')->constrained()->cascadeOnDelete();$t->foreignId('tag_id')->constrained()->cascadeOnDelete();$t->primary(['resource_id','tag_id']);}); } public function down(): void { Schema::dropIfExists('resource_tag');Schema::dropIfExists('tags'); } };
