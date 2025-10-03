<?php

namespace Database\Seeders;

use App\Models\PropertyPart;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyPartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parts = [
            ['name' => 'Kitchen'],
            ['name' => 'Living Room'],
            ['name' => 'Bedroom'],
            ['name' => 'Bathroom'],
            ['name' => 'Garage'],
        ];

        foreach ($parts as $part) {
            PropertyPart::create($part);
        }
    }
}
