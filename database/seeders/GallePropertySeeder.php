<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consumer;
use App\Models\Property;

class GallePropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $consumers = Consumer::whereIn('nic', ['198512345678', '197823456789', '200534567890', '199045678901', '199856789012'])->get();

        foreach ($consumers as $consumer) {
            Property::create([
                'consumer_id' => $consumer->id,
                'account_no' => 'LECO-GL-' . str_pad((string)$consumer->id, 5, '0', STR_PAD_LEFT),
                'address' => $consumer->address, // Using the same address as the consumer for simplicity
            ]);

            // Create a second property for one of the consumers
            if ($consumer->nic === '198512345678') {
                Property::create([
                    'consumer_id' => $consumer->id,
                    'account_no' => 'LECO-GL-' . str_pad((string)$consumer->id, 5, '0', STR_PAD_LEFT) . '-B',
                    'address' => '123B, Galle Road, Unawatuna',
                ]);
            }
        }

        $this->command->info('Galle properties created successfully.');
    }
}
