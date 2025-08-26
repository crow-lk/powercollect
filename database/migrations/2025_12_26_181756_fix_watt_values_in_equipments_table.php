<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix existing watt values that are stored as integers instead of proper decimals
        // Values like 150.00 should become 1.50, 100.00 should become 1.00, etc.
        DB::table('equipments')
            ->where('watt', '>=', 100)
            ->update([
                'watt' => DB::raw('watt / 100')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the conversion by multiplying back by 100
        DB::table('equipments')
            ->where('watt', '<', 100)
            ->update([
                'watt' => DB::raw('watt * 100')
            ]);
    }
};
