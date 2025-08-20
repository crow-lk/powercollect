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
        // Get the actual foreign key constraint name
        $foreignKeys = $this->getForeignKeys('consumer_usages', 'customer_id');
        
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
            $table->renameColumn('customer_id', 'consumer_id');
        });

        // Add new foreign key constraint
        Schema::table('consumer_usages', function (Blueprint $table) {
            $table->foreign('consumer_id')->references('id')->on('consumers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the actual foreign key constraint name
        $foreignKeys = $this->getForeignKeys('consumer_usages', 'consumer_id');
        
        if (!empty($foreignKeys)) {
            // Drop new foreign key constraint
            foreach ($foreignKeys as $foreignKey) {
                Schema::table('consumer_usages', function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign($foreignKey);
                });
            }
        }

        // Rename the column back
        Schema::table('consumer_usages', function (Blueprint $table) {
            $table->renameColumn('consumer_id', 'customer_id');
        });

        // Add old foreign key constraint back
        Schema::table('consumer_usages', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
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
