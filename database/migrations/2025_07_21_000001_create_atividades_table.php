<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atividades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('titulo');

            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')->references('id')->on('categories')->onDelete('set null');

            

            $table->text('descricao')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('data_inicio')->nullable();
            $table->dateTime('data_fim')->nullable();
            $table->string('prioridade')->nullable();
            $table->integer('tempo_estimado')->nullable();
            $table->integer('tempo_real')->nullable();
            $table->integer('progresso')->nullable();
            $table->string('local')->nullable();
            $table->string('url')->nullable();
            $table->text('notas')->nullable();
            $table->dateTime('lembrete')->nullable();
            $table->string('repeticao')->nullable();
            $table->boolean('privada')->default(false);
            $table->boolean('favorita')->default(false);
            $table->string('cor')->nullable();
            $table->string('icone')->nullable();
            $table->string('meta')->nullable();
            $table->integer('energia')->nullable();
            $table->integer('urgencia')->nullable();
            $table->integer('importancia')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atividades');
    }
};
