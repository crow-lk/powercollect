<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\CustomerUsage;
use App\Models\HousePart;
use App\Models\House;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create sample customers from Galle area
        $customers = [
            [
                'name' => 'K.M. Perera',
                'account_no' => 'LECO-GL-001',
                'address' => 'No. 45, Main Street, Galle Fort, Galle',
                'nic' => '197534567890'
            ],
            [
                'name' => 'S.A. Fernando',
                'account_no' => 'LECO-GL-002', 
                'address' => 'No. 12, Wakwella Road, Galle',
                'nic' => '198245678901'
            ],
            [
                'name' => 'H.D. Silva',
                'account_no' => 'LECO-GL-003',
                'address' => 'No. 78, Matara Road, Akmeemana, Galle',
                'nic' => '199012345678'
            ],
            [
                'name' => 'Galle Heritage Hotel',
                'account_no' => 'LECO-GL-004',
                'address' => 'No. 28, Church Street, Galle Fort, Galle',
                'nic' => '200156789012'
            ],
            [
                'name' => 'M.A. Rajapaksha',
                'account_no' => 'LECO-GL-005',
                'address' => 'No. 156, Baddegama Road, Hikkaduwa, Galle',
                'nic' => '198867890123'
            ],
            [
                'name' => 'Ceylon Tea Factory',
                'account_no' => 'LECO-GL-006',
                'address' => 'No. 89, Industrial Zone, Bataduwa, Galle',
                'nic' => '199523456789'
            ],
            [
                'name' => 'R.P. Wickramasinghe',
                'account_no' => 'LECO-GL-007',
                'address' => 'No. 234, Colombo Road, Karapitiya, Galle',
                'nic' => '197678901234'
            ],
            [
                'name' => 'Galle Medical Center',
                'account_no' => 'LECO-GL-008',
                'address' => 'No. 67, Hospital Road, Galle',
                'nic' => '200234567890'
            ]
        ];

        foreach ($customers as $customer) {
            Customer::create([
                'name' => $customer['name'],
                'nic' => $customer['nic'],
                'address' => $customer['address'],
            ]);
        }

        // Create houses for each customer (using account_no and address)
        $houses = [];
        foreach ($customers as $customer) {
            $customerModel = Customer::where('name', $customer['name'])->first();
            $houses[] = House::create([
                'customer_id' => $customerModel->id,
                'account_no' => $customer['account_no'],
                'address' => $customer['address'],
            ]);
        }

        // Define common house parts
        $defaultParts = ['Living Room', 'Kitchen', 'Dining Room', 'Bedroom 1', 'Bedroom 2', 'Bathroom'];

        // Create house parts for each house
        $houseParts = [];
        foreach (House::all() as $house) {
            foreach ($defaultParts as $partName) {
                $houseParts[] = HousePart::create([
                    'house_id' => $house->id,
                    'name' => $partName,
                ]);
            }
        }

        // Create sample equipment types commonly used in Sri Lanka
        $equipment = [
            [
                'type' => 'Air Conditioning Unit',
                'brand' => 'Mitsubishi',
                'model' => 'MSY-JP25VF',
                'kVA' => 2.5
            ],
            [
                'type' => 'Industrial Chiller',
                'brand' => 'Carrier',
                'model' => '30HXC080',
                'kVA' => 80.0
            ],
            [
                'type' => 'Water Heater',
                'brand' => 'Abans',
                'model' => 'AWH-50L',
                'kVA' => 3.0
            ],
            [
                'type' => 'Ceiling Fan',
                'brand' => 'Singer',
                'model' => 'SF-1200',
                'kVA' => 0.075
            ],
            [
                'type' => 'Refrigerator',
                'brand' => 'Samsung',
                'model' => 'RT28M3022S8',
                'kVA' => 0.15
            ],
            [
                'type' => 'Industrial Motor',
                'brand' => 'ABB',
                'model' => 'M3BP160M',
                'kVA' => 15.0
            ],
            [
                'type' => 'LED Light Panel',
                'brand' => 'Philips',
                'model' => 'CoreLine Panel',
                'kVA' => 0.036
            ],
            [
                'type' => 'Washing Machine',
                'brand' => 'LG',
                'model' => 'WM3488HW',
                'kVA' => 2.2
            ],
            [
                'type' => 'Microwave Oven',
                'brand' => 'Panasonic',
                'model' => 'NN-ST34HM',
                'kVA' => 1.2
            ],
            [
                'type' => 'Industrial Pump',
                'brand' => 'Grundfos',
                'model' => 'CR3-17',
                'kVA' => 5.5
            ]
        ];

        // Assign each equipment to a random house part
        $housePartsCollection = collect($houseParts);
        foreach ($equipment as $eq) {
            $randomPart = $housePartsCollection->random();
            Equipment::create(array_merge($eq, [
                'house_part_id' => $randomPart->id
            ]));
        }

        // Create sample usage records for the past 30 days
        $customers_collection = Customer::all();
        $equipment_collection = Equipment::all();
        $house_parts_collection = HousePart::all();

        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            // Generate 3-8 random usage records per day
            $recordsPerDay = rand(3, 8);

            for ($j = 0; $j < $recordsPerDay; $j++) {
                $housePart = $house_parts_collection->random();
                $equipmentInPart = $equipment_collection->where('house_part_id', $housePart->id);
                if ($equipmentInPart->isEmpty()) continue;
                $equipment_item = $equipmentInPart->random();
                $house = $housePart->house;
                $customer_id = $house ? $house->customer_id : null;
                if (!$customer_id) continue;

                // Generate realistic usage times
                $startHour = rand(6, 20); // Between 6 AM and 8 PM
                $startMinute = rand(0, 59);
                $startTime = sprintf('%02d:%02d', $startHour, $startMinute);

                // Usage duration between 1 to 6 hours
                $durationHours = rand(1, 6);
                $endHour = ($startHour + $durationHours) % 24;
                $endMinute = rand(0, 59);
                $endTime = sprintf('%02d:%02d', $endHour, $endMinute);

                // Add some variation to kVA (±10% of equipment rating)
                $baseKva = $equipment_item->kVA;
                $variation = $baseKva * 0.1 * (rand(-100, 100) / 100);
                $actualKva = round($baseKva + $variation, 2);

                CustomerUsage::create([
                    'customer_id' => $customer_id,
                    'equipment_id' => $equipment_item->id,
                    'house_part_id' => $housePart->id,
                    'kVA' => max(0.01, $actualKva), // Ensure minimum positive value
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'date' => $date->toDateString(),
                ]);
            }
        }
        
        $this->command->info('Sample data created successfully!');
        $this->command->info('Created: ' . Customer::count() . ' customers');
        $this->command->info('Created: ' . Equipment::count() . ' equipment items');
        $this->command->info('Created: ' . CustomerUsage::count() . ' usage records');
    }
}
