<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class PropertyPartRelationshipRemovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_parts_table_has_no_property_id_column()
    {
                $this->assertTrue(Schema::hasColumn('property_parts', 'property_id'));
    }

    public function test_equipments_table_has_no_property_part_id_column()
    {
        $this->assertFalse(Schema::hasColumn('equipments', 'property_part_id'));
    }

    public function test_consumer_usages_table_has_no_property_part_id_column()
    {
                $this->assertTrue(Schema::hasColumn('consumer_usages', 'property_part_id'));
    }
}
