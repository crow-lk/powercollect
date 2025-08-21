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
        // Check if the house_part_id column exists in consumer_usages table
        if (Schema::hasColumn('consumer_usages', 'house_part_id')) {
            // Get the actual foreign key constraint name
            $foreignKeys = $this->getForeignKeys('consumer_usages', 'house_part_id');
            
            if (!empty($foreignKeys)) {
                // Drop existing foreign key constraints
                foreach ($foreignKeys as $foreignKey) {
                    Schema::table('consumer_usages', function (Blueprint $table) use ($foreignKey) {
                        $table->dropForeign($foreignKey);
                    });
                }
            }

            // Rename the column
            Schema::table('consumer_usages', function (Blueprint $table) {
                $table->renameColumn('house_part_id', 'property_part_id');
            });
        } else {
            // If house_part_id doesn't exist, check if property_part_id already exists
            if (!Schema::hasColumn('consumer_usages', 'property_part_id')) {
                // Add the property_part_id column
                Schema::table('consumer_usages', function (Blueprint $table) {
                    $table->unsignedBigInteger('property_part_id')->nullable()->after('equipment_id');
                });
            }
        }

        // Add new foreign key constraint
        Schema::table('consumer_usages', function (Blueprint $table) {
            $table->foreign('property_part_id')->references('id')->on('property_parts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the actual foreign key constraint name
        $foreignKeys = $this->getForeignKeys('consumer_usages', 'property_part_id');
        
        if (!empty($foreignKeys)) {
            // Drop new foreign key constraint
            foreach ($foreignKeys as $foreignKey) {
                Schema::table('consumer_usages', function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign($foreignKey);
                });
            }
        }

        // Check if property_part_id column exists
        if (Schema::hasColumn('consumer_usages', 'property_part_id')) {
            // Rename the column back to house_part_id
            Schema::table('consumer_usages', function (Blueprint $table) {
                $table->renameColumn('property_part_id', 'house_part_id');
            });
        }
    }

    /**
     * Get foreign key constraints for a specific column
     */
    private function getForeignKeys(string $table, string $column): array
    {
        $foreignKeys = [];
        
        try {
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND COLUMN_NAME = ? 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$table, $column]);
            
            foreach ($constraints as $constraint) {
                $foreignKeys[] = $constraint->CONSTRAINT_NAME;
            }
        } catch (\Exception $e) {
            // If we can't get the constraints, return empty array
        }
        
        return $foreignKeys;
    }
};
