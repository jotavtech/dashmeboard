<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agenda_items', function (Blueprint $table) {
            if (!Schema::hasColumn('agenda_items', 'atividade_id')) {
                $table->unsignedBigInteger('atividade_id')->nullable()->after('user_id');
                $table->foreign('atividade_id')->references('id')->on('atividades')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda_items', function (Blueprint $table) {
            if (Schema::hasColumn('agenda_items', 'atividade_id')) {
                $table->dropForeign(['atividade_id']);
                $table->dropColumn('atividade_id');
            }
        });
    }
}; 