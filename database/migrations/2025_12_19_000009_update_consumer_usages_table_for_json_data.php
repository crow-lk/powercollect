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
        Schema::table('consumer_usages', function (Blueprint $table) {
            // Drop foreign key constraints first, if they exist
            $this->dropForeignKeyIfExists('consumer_usages', 'consumer_id');
            $this->dropForeignKeyIfExists('consumer_usages', 'equipment_id');
            $this->dropForeignKeyIfExists('consumer_usages', 'property_part_id');

            // Add new columns
            $table->foreignId('property_id')->nullable()->after('id'); // Make nullable temporarily
            $table->integer('period_number')->nullable()->after('date'); // Make nullable temporarily

            // Rename and change type of kVA
            $table->renameColumn('kVA', 'usage_data');
            $table->json('usage_data')->change();

            // Drop old columns
            $table->dropColumn(['consumer_id', 'equipment_id', 'property_part_id', 'start_time', 'end_time']);

            // Add foreign key constraints and make non-nullable after data migration if needed
            $table->foreign('property_id')->references('id')->on('properties');
            // If you have existing data, you'll need to populate property_id and period_number
            // before making them non-nullable.
            // $table->foreignId('property_id')->constrained('properties')->change();
            // $table->integer('period_number')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumer_usages', function (Blueprint $table) {
            // Re-add old columns
            $table->unsignedBigInteger('consumer_id')->nullable()->after('id');
            $table->unsignedBigInteger('equipment_id')->nullable()->after('consumer_id');
            $table->unsignedBigInteger('property_part_id')->nullable()->after('equipment_id');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();

            // Rename and change type back
            $table->renameColumn('usage_data', 'kVA');
            $table->decimal('kVA', 15, 6)->change(); // Assuming original was decimal(15,6)

            // Drop new columns
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
            $table->dropColumn('period_number');

            // Add foreign key constraints back
            if (Schema::hasTable('consumers')) {
                $table->foreign('consumer_id')->references('id')->on('consumers');
            }
            if (Schema::hasTable('equipments')) {
                $table->foreign('equipment_id')->references('id')->on('equipments');
            }
            if (Schema::hasTable('property_parts')) {
                $table->foreign('property_part_id')->references('id')->on('property_parts');
            }
        });
    }

    /**
     * Helper to drop foreign key if it exists
     */
    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND COLUMN_NAME = ? 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$table, $column]);

        foreach ($foreignKeys as $foreignKey) {
            Schema::table($table, function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            });
        }
    }
};
