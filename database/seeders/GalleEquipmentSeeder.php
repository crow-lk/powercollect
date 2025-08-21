<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;

class GalleEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipmentData = [
            ['type' => 'Refrigerator', 'brand' => 'LG', 'model' => 'GR-B202SLCL', 'kVA' => 0.15],
            ['type' => 'Television', 'brand' => 'Samsung', 'model' => 'UA55RU7100', 'kVA' => 0.1],
            ['type' => 'Washing Machine', 'brand' => 'Singer', 'model' => 'WM-800', 'kVA' => 2.0],
            ['type' => 'Air Conditioner', 'brand' => 'Daikin', 'model' => 'FTKM35PV2V', 'kVA' => 1.5],
            ['type' => 'Water Heater', 'brand' => 'Ariston', 'model' => 'PRO1 R 80', 'kVA' => 3.0],
            ['type' => 'Microwave Oven', 'brand' => 'Panasonic', 'model' => 'NN-ST25JW', 'kVA' => 1.0],
        ];

        foreach ($equipmentData as $data) {
            Equipment::create($data);
        }

        $this->command->info('Galle equipment created successfully.');
    }
}
