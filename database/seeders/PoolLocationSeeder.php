<?php

namespace Database\Seeders;

use App\Models\PoolLocation;
use Illuminate\Database\Seeder;

class PoolLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Bumi singgah/tirta marta tongkeng',
                'coach_fee' => 50000,
                'cash_percentage' => 13.0,
                'private_ticket_price' => 0,
                'semi_private_ticket_price' => null,
            ],
            [
                'name' => 'Saraga',
                'coach_fee' => 65000,
                'cash_percentage' => 5.0,
                'private_ticket_price' => 80000,
                'semi_private_ticket_price' => 40000,
            ],
            [
                'name' => 'Kytos',
                'coach_fee' => 65000,
                'cash_percentage' => 8.0,
                'private_ticket_price' => 0,
                'semi_private_ticket_price' => null,
            ],
            [
                'name' => 'Cipaku',
                'coach_fee' => 65000,
                'cash_percentage' => 8.0,
                'private_ticket_price' => 70000,
                'semi_private_ticket_price' => null,
            ],
            [
                'name' => 'HV',
                'coach_fee' => 75000,
                'cash_percentage' => 10.0,
                'private_ticket_price' => 0,
                'semi_private_ticket_price' => null,
            ],
        ];

        foreach ($locations as $loc) {
            PoolLocation::create($loc);
        }
    }
}
