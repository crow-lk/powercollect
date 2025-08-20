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
        Schema::rename('house_parts', 'property_parts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('property_parts', 'house_parts');
    }
};
