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
        Schema::table('atividades', function (Blueprint $table) {
            // Índice para user_id (muito usado em consultas)
            $table->index('user_id');
            
            // Índice para status (usado em filtros)
            $table->index('status');
            
            // Índice para data_inicio (usado em ordenação e filtros)
            $table->index('data_inicio');
            
            // Índice para data_fim (usado em filtros)
            $table->index('data_fim');
            
            // Índice composto para user_id + status (consultas comuns)
            $table->index(['user_id', 'status']);
            
            // Índice composto para user_id + data_inicio (ordenação por data)
            $table->index(['user_id', 'data_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atividades', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['data_inicio']);
            $table->dropIndex(['data_fim']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'data_inicio']);
        });
    }
}; 