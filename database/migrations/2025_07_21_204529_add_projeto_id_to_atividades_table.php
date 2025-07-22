<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
        public function up()
    {
        Schema::table('atividades', function (Blueprint $table) {
            $table->unsignedBigInteger('projeto_id')->nullable()->after('categoria_id');
            $table->foreign('projeto_id')->references('id')->on('projects');
        });
    }

    public function down()
    {
        Schema::table('atividades', function (Blueprint $table) {
            $table->dropForeign(['projeto_id']);
            $table->dropColumn('projeto_id');
        });
    }
};
