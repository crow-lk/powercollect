<?php

namespace Tests\Feature\Feature;

use App\Filament\Pages\LogConsumerUsage;
use App\Models\ConsumerUsage;
use App\Models\Equipment;
use App\Models\Property;
use App\Models\PropertyPart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LogConsumerUsageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usage_data_can_be_logged_correctly(): void
    {
        // Create test data using factories
        $property = Property::factory()->create();
        $propertyPart = PropertyPart::factory()->create();
        $equipment1 = Equipment::factory()->create();
        $equipment2 = Equipment::factory()->create();

        $date = now()->format('Y-m-d');

        $usageData = [
            [
                'property_part_id' => $propertyPart->id,
                'equipment_id' => $equipment1->id,
                'kVA' => 10.5,
                'period_number' => 1,
            ],
            [
                'property_part_id' => $propertyPart->id,
                'equipment_id' => $equipment2->id,
                'kVA' => 20.0,
                'period_number' => 2,
            ],
        ];

        // Simulate Livewire component action
        Livewire::test(LogConsumerUsage::class)
            ->set('data', [
                'property_id' => $property->id,
                'date' => $date,
                'usage_data' => $usageData,
            ])
            ->call('create');

        // Assert that a ConsumerUsage record exists in the database with the correct data
        $this->assertDatabaseHas('consumer_usages', [
            'property_id' => $property->id,
            'date' => $date,
        ]);

        // Fetch the created record and assert the JSON content
        $record = ConsumerUsage::where('property_id', $property->id)
                               ->where('date', $date)
                               ->first();

        $this->assertNotNull($record);
        $this->assertEquals($usageData, $record->usage_data);

        // Optionally, assert the count of records
        $this->assertCount(1, ConsumerUsage::all());
    }
}