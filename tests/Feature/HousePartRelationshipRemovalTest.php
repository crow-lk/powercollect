<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class HousePartRelationshipRemovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_house_parts_table_has_no_house_id_column()
    {
        $this->assertFalse(Schema::hasColumn('house_parts', 'house_id'));
    }

    public function test_equipments_table_has_no_house_part_id_column()
    {
        $this->assertFalse(Schema::hasColumn('equipments', 'house_part_id'));
    }

    public function test_customer_usages_table_has_no_house_part_id_column()
    {
        $this->assertFalse(Schema::hasColumn('customer_usages', 'house_part_id'));
    }
}
