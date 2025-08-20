<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Consumer;

class ConsumerTest extends TestCase
{
    use RefreshDatabase;

    public function test_consumer_can_be_created()
    {
        $consumer = Consumer::create([
            'name' => 'Test Consumer',
            'account_no' => 'ACC123',
            'address' => '123 Main St',
            'nic' => '123456789V',
        ]);

        $this->assertDatabaseHas('consumers', [
            'name' => 'Test Consumer',
            'account_no' => 'ACC123',
            'address' => '123 Main St',
            'nic' => '123456789V',
        ]);
    }

    public function test_consumer_can_be_updated()
    {
        $consumer = Consumer::factory()->create();
        $consumer->update(['name' => 'Updated Consumer']);
        $this->assertEquals('Updated Consumer', $consumer->fresh()->name);
    }

    public function test_consumer_can_be_deleted()
    {
        $consumer = Consumer::factory()->create();
        $consumer->delete();
        $this->assertModelMissing($consumer);
    }
}
