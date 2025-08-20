<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_usages', function (Blueprint $table) {
            $table->date('date')->nullable()->after('end_time');
        });
        
        // Update existing records to have a default date (today)
        DB::table('customer_usages')
            ->whereNull('date')
            ->update(['date' => now()->toDateString()]);
            
        // Make the column required after populating existing records
        Schema::table('customer_usages', function (Blueprint $table) {
            $table->date('date')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_usages', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};
