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
        Schema::create('customer_usages', function (Blueprint $table) {
            $table->id();
            // relationship with customer id
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            // relationship with equipment id
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->integer('kVA');
            $table->dateTime('start_time');
            $table->dateTime('end_time');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_usages');
    }
};
