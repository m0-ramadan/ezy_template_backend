<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::table('downloads',function(Blueprint $t){$t->string('visitor_hash',64)->nullable()->after('user_id')->index();});} public function down():void{Schema::table('downloads',function(Blueprint $t){$t->dropColumn('visitor_hash');});}};
