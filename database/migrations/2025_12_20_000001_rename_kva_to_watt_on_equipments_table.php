<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('equipments', 'kVA')) {
            Schema::table('equipments', function (Blueprint $table) {
                $table->renameColumn('kVA', 'watt');
            });

           
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('equipments', 'watt')) {
            
            Schema::table('equipments', function (Blueprint $table) {
                $table->renameColumn('watt', 'kVA');
            });
        }
    }
};


