<?php

namespace Tests\Feature;

use App\Filament\Resources\PropertyResource\Pages\CreateProperty;
use App\Filament\Resources\PropertyResource\Pages\EditProperty;
use App\Filament\Resources\PropertyResource\Pages\ListProperties;
use App\Models\Consumer;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_render_list_properties_page(): void
    {
        $this->get(ListProperties::getUrl())->assertSuccessful();
    }

    public function test_can_create_property(): void
    {
        $consumer = Consumer::factory()->create();
        $newData = Property::factory()->make(['consumer_id' => $consumer->id]);

        Livewire::test(CreateProperty::class)
            ->set('data.consumer_id', $newData->consumer_id)
            ->set('data.account_no', $newData->account_no)
            ->set('data.address', $newData->address)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Property::class, [
            'consumer_id' => $newData->consumer_id,
            'account_no' => $newData->account_no,
            'address' => $newData->address,
        ]);
    }

    public function test_can_render_edit_property_page(): void
    {
        $property = Property::factory()->create();
        $this->get(EditProperty::getUrl(['record' => $property]))->assertSuccessful();
    }

    public function test_can_retrieve_property_data_on_edit_page(): void
    {
        $property = Property::factory()->create();

        Livewire::test(EditProperty::class, ['record' => $property->id])
            ->assertFormSet([
                'consumer_id' => $property->consumer_id,
                'account_no' => $property->account_no,
                'address' => $property->address,
            ]);
    }

    public function test_can_update_property(): void
    {
        $property = Property::factory()->create();
        $newData = Property::factory()->make();

        Livewire::test(EditProperty::class, ['record' => $property->id])
            ->set('data.account_no', $newData->account_no)
            ->set('data.address', $newData->address)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals($newData->account_no, $property->refresh()->account_no);
        $this->assertEquals($newData->address, $property->refresh()->address);
    }

    public function test_can_delete_property(): void
    {
        $property = Property::factory()->create();

        Livewire::test(EditProperty::class, ['record' => $property->id])
            ->callAction('delete');

        $this->assertModelMissing($property);
    }
}
