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
            ['type' => 'Refrigerator', 'brand' => 'LG', 'model' => 'GR-B202SLCL', 'watt' => 150],
            ['type' => 'Television', 'brand' => 'Samsung', 'model' => 'UA55RU7100', 'watt' => 100],
            ['type' => 'Washing Machine', 'brand' => 'Singer', 'model' => 'WM-800', 'watt' => 2000],
            ['type' => 'Air Conditioner', 'brand' => 'Daikin', 'model' => 'FTKM35PV2V', 'watt' => 1500],
            ['type' => 'Water Heater', 'brand' => 'Ariston', 'model' => 'PRO1 R 80', 'watt' => 3000],
            ['type' => 'Microwave Oven', 'brand' => 'Panasonic', 'model' => 'NN-ST25JW', 'watt' => 1000],
        ];

        foreach ($equipmentData as $data) {
            Equipment::create($data);
        }

        $this->command->info('Galle equipment created successfully.');
    }
}
