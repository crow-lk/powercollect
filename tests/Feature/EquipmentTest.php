<?php

namespace Tests\Feature;

use App\Filament\Resources\EquipmentsResource\Pages\CreateEquipment;
use App\Filament\Resources\EquipmentsResource\Pages\EditEquipment;
use App\Filament\Resources\EquipmentsResource\Pages\ListEquipments;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_render_list_equipments_page(): void
    {
        $this->get(ListEquipments::getUrl())->assertSuccessful();
    }

    public function test_can_create_equipment(): void
    {
        $newData = Equipment::factory()->make();

        Livewire::test(CreateEquipment::class)
            ->set('data.type', $newData->type)
            ->set('data.brand', $newData->brand)
            ->set('data.model', $newData->model)
            ->set('data.watt', $newData->watt)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Equipment::class, [
            'type' => $newData->type,
            'brand' => $newData->brand,
            'model' => $newData->model,
            'watt' => $newData->watt,
        ]);
    }

    public function test_can_render_edit_equipment_page(): void
    {
        $equipment = Equipment::factory()->create();
        $this->get(EditEquipment::getUrl(['record' => $equipment]))->assertSuccessful();
    }

    public function test_can_retrieve_equipment_data_on_edit_page(): void
    {
        $equipment = Equipment::factory()->create();

        Livewire::test(EditEquipment::class, ['record' => $equipment->id])
            ->assertFormSet([
                'type' => $equipment->type,
                'brand' => $equipment->brand,
                'model' => $equipment->model,
                'watt' => (string) $equipment->watt, // Cast to string for comparison
            ]);
    }

    public function test_can_update_equipment(): void
    {
        $equipment = Equipment::factory()->create();
        $newData = Equipment::factory()->make();

        Livewire::test(EditEquipment::class, ['record' => $equipment->id])
            ->set('data.type', $newData->type)
            ->set('data.brand', $newData->brand)
            ->set('data.model', $newData->model)
            ->set('data.watt', $newData->watt)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals($newData->type, $equipment->refresh()->type);
    }

    public function test_can_delete_equipment(): void
    {
        $equipment = Equipment::factory()->create();

        Livewire::test(EditEquipment::class, ['record' => $equipment->id])
            ->callAction('delete');

        $this->assertModelMissing($equipment);
    }
}
