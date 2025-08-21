<?php

namespace Tests\Feature;

use App\Filament\Resources\ConsumersResource\Pages\CreateConsumer;
use App\Filament\Resources\ConsumersResource\Pages\EditConsumer;
use App\Filament\Resources\ConsumersResource\Pages\ListConsumers;
use App\Models\Consumer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ConsumerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_render_list_consumers_page(): void
    {
        $this->get(ListConsumers::getUrl())->assertSuccessful();
    }

    public function test_can_create_consumer(): void
    {
        $newData = Consumer::factory()->make();

        Livewire::test(CreateConsumer::class)
            ->set('data.name', $newData->name)
            ->set('data.address', $newData->address)
            ->set('data.nic', $newData->nic)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(Consumer::class, [
            'name' => $newData->name,
            'address' => $newData->address,
            'nic' => $newData->nic,
        ]);
    }

    public function test_can_render_edit_consumer_page(): void
    {
        $consumer = Consumer::factory()->create();
        $this->get(EditConsumer::getUrl(['record' => $consumer]))->assertSuccessful();
    }

    public function test_can_retrieve_consumer_data_on_edit_page(): void
    {
        $consumer = Consumer::factory()->create();

        Livewire::test(EditConsumer::class, ['record' => $consumer->id])
            ->assertFormSet([
                'name' => $consumer->name,
                'address' => $consumer->address,
                'nic' => $consumer->nic,
            ]);
    }

    public function test_can_update_consumer(): void
    {
        $consumer = Consumer::factory()->create();
        $newData = Consumer::factory()->make();

        Livewire::test(EditConsumer::class, ['record' => $consumer->id])
            ->set('data.name', $newData->name)
            ->set('data.address', $newData->address)
            ->set('data.nic', $newData->nic)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals($newData->name, $consumer->refresh()->name);
    }

    public function test_can_delete_consumer(): void
    {
        $consumer = Consumer::factory()->create();

        Livewire::test(EditConsumer::class, ['record' => $consumer->id])
            ->callAction('delete');

        $this->assertModelMissing($consumer);
    }
}