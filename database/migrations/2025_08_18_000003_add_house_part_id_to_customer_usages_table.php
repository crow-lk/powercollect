<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customer_usages', function (Blueprint $table) {
            $table->unsignedBigInteger('house_part_id')->nullable()->after('equipment_id');
            $table->foreign('house_part_id')->references('id')->on('house_parts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('customer_usages', function (Blueprint $table) {
            $table->dropForeign(['house_part_id']);
            $table->dropColumn('house_part_id');
        });
    }
};
