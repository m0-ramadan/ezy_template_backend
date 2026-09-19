<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('service_requests',function(Blueprint $t){$t->id();$t->string('name');$t->string('email');$t->string('service');$t->text('message');$t->string('budget')->nullable();$t->enum('status',['new','in_progress','completed','cancelled'])->default('new');$t->text('admin_notes')->nullable();$t->timestamps();}); } public function down(): void { Schema::dropIfExists('service_requests'); } };
