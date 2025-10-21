<?php

namespace Database\Seeders;

use App\Models\PowerPlantType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PowerPlantTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'ДЦС'],
            ['name' => 'СЭХ'],
            ['name' => 'Баттерэй'],
        ];

        foreach ($types as $type) {
            PowerPlantType::firstOrCreate($type);
        }
    }
}
