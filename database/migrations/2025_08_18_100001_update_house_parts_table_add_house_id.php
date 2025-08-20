<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('house_parts', function (Blueprint $table) {
            $table->unsignedBigInteger('house_id')->after('id');
            $table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }

    public function down(): void
    {
        Schema::table('house_parts', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->after('id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->dropForeign(['house_id']);
            $table->dropColumn('house_id');
        });
    }
};
