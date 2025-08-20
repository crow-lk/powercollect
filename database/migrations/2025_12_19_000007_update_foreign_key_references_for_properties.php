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
        // Check if the house_id column exists in property_parts table
        if (Schema::hasColumn('property_parts', 'house_id')) {
            // Get the actual foreign key constraint name
            $foreignKeys = $this->getForeignKeys('property_parts', 'house_id');
            
            if (!empty($foreignKeys)) {
                // Drop existing foreign key constraints
                foreach ($foreignKeys as $foreignKey) {
                    Schema::table('property_parts', function (Blueprint $table) use ($foreignKey) {
                        $table->dropForeign($foreignKey);
                    });
                }
            }

            // Rename the column
            Schema::table('property_parts', function (Blueprint $table) {
                $table->renameColumn('house_id', 'property_id');
            });
        } else {
            // If house_id doesn't exist, check if property_id already exists
            if (!Schema::hasColumn('property_parts', 'property_id')) {
                // Add the property_id column
                Schema::table('property_parts', function (Blueprint $table) {
                    $table->unsignedBigInteger('property_id')->after('id');
                });
            }
        }

        // Add new foreign key constraint
        Schema::table('property_parts', function (Blueprint $table) {
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the actual foreign key constraint name
        $foreignKeys = $this->getForeignKeys('property_parts', 'property_id');
        
        if (!empty($foreignKeys)) {
            // Drop new foreign key constraint
            foreach ($foreignKeys as $foreignKey) {
                Schema::table('property_parts', function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign($foreignKey);
                });
            }
        }

        // Check if property_id column exists
        if (Schema::hasColumn('property_parts', 'property_id')) {
            // Rename the column back to house_id
            Schema::table('property_parts', function (Blueprint $table) {
                $table->renameColumn('property_id', 'house_id');
            });

            // Add old foreign key constraint back
            Schema::table('property_parts', function (Blueprint $table) {
                $table->foreign('house_id')->references('id')->on('houses')->onDelete('cascade');
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
