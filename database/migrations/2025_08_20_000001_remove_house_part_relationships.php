<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('house_parts', function (Blueprint $table) {
            $table->dropForeign(['house_id']);
            $table->dropColumn('house_id');
        });
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropForeign(['house_part_id']);
            $table->dropColumn('house_part_id');
        });
        Schema::table('customer_usages', function (Blueprint $table) {
            $table->dropForeign(['house_part_id']);
            $table->dropColumn('house_part_id');
        });
    }

    public function down(): void
    {
        Schema::table('house_parts', function (Blueprint $table) {
            $table->unsignedBigInteger('house_id')->nullable();
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');
        });
        Schema::table('equipments', function (Blueprint $table) {
            $table->unsignedBigInteger('house_part_id')->nullable();
            $table->foreign('house_part_id')->references('id')->on('house_parts')->onDelete('set null');
        });
        Schema::table('customer_usages', function (Blueprint $table) {
            $table->unsignedBigInteger('house_part_id')->nullable();
            $table->foreign('house_part_id')->references('id')->on('house_parts')->onDelete('set null');
        });
    }
};
