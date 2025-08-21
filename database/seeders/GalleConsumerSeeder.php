<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consumer;

class GalleConsumerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $consumers = [
            [
                'name' => 'Nimal Silva',
                'address' => '123, Galle Road, Unawatuna',
                'nic' => '198512345678',
            ],
            [
                'name' => 'Sunil Perera',
                'address' => '45, Lighthouse Street, Galle Fort',
                'nic' => '197823456789',
            ],
            [
                'name' => 'Amara Hotel',
                'address' => '78, Yaddehimulla Road, Unawatuna',
                'nic' => '200534567890',
            ],
            [
                'name' => 'Kamal Fernando',
                'address' => '210, Matara Road, Thalpe',
                'nic' => '199045678901',
            ],
            [
                'name' => 'Galle Rice Millers',
                'address' => '55, Industrial Estate, Habaraduwa',
                'nic' => '199856789012',
            ],
        ];

        foreach ($consumers as $consumer) {
            Consumer::create($consumer);
        }

        $this->command->info('Galle consumers created successfully.');
    }
}
