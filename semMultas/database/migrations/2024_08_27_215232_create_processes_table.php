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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreignId('client_id')->constrained();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->string('plate')->nullable();
            $table->string('chassis')->nullable();
            $table->string('renavam')->nullable();
            $table->string('state_plate')->nullable();
            $table->string('infraction_code')->nullable();
            $table->string('agency')->nullable();
            $table->string('ait')->nullable();
            $table->decimal('process_value', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->text('observation')->nullable();
            $table->string('process_number')->nullable();
            $table->date('deadline_date')->nullable();
            $table->string('slug');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('seller_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};
