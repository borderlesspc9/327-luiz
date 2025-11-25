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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('cpf')->nullable()->change();
            $table->string('rg')->nullable()->change();
            $table->string('slug')->nullable()->change();
            // Se profession existir na tabela, tornar nullable também
            if (Schema::hasColumn('clients', 'profession')) {
                $table->string('profession')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('cpf')->nullable(false)->change();
            $table->string('rg')->nullable(false)->change();
            $table->string('slug')->nullable(false)->change();
            // Se profession existir na tabela, tornar obrigatório novamente
            if (Schema::hasColumn('clients', 'profession')) {
                $table->string('profession')->nullable(false)->change();
            }
        });
    }
};
