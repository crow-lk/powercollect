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
        // Drop existing foreign key constraints
        Schema::table('houses', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        // Rename the column
        Schema::table('houses', function (Blueprint $table) {
            $table->renameColumn('customer_id', 'consumer_id');
        });

        // Add new foreign key constraint
        Schema::table('houses', function (Blueprint $table) {
            $table->foreign('consumer_id')->references('id')->on('consumers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new foreign key constraint
        Schema::table('houses', function (Blueprint $table) {
            $table->dropForeign(['consumer_id']);
        });

        // Rename the column back
        Schema::table('houses', function (Blueprint $table) {
            $table->renameColumn('consumer_id', 'customer_id');
        });
    }
};
