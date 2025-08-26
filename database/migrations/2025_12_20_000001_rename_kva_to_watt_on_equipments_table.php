<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('equipments', 'kVA')) {
            Schema::table('equipments', function (Blueprint $table) {
                $table->renameColumn('kVA', 'watt');
            });

            // Update the column definition to properly handle decimal values
            Schema::table('equipments', function (Blueprint $table) {
                $table->decimal('watt', 10, 2)->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('equipments', 'watt')) {
            // First change back to the original decimal definition
            Schema::table('equipments', function (Blueprint $table) {
                $table->decimal('watt', 8, 0)->change();
            });

            Schema::table('equipments', function (Blueprint $table) {
                $table->renameColumn('watt', 'kVA');
            });
        }
    }
};
