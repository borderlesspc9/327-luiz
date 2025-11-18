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
        Schema::create('process_status_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id');
            $table->foreignId('status_id')->constrained('status');
            $table->boolean('is_active')->default(true);

            $table->foreign('process_id')->references('id')->on('processes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_status_pivot');
    }
};
