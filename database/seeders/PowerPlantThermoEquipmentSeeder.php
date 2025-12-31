<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PowerPlantThermoEquipment;

class PowerPlantThermoEquipmentSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * =========================
         * ДЦС-2 (power_plant_id = 19)
         * =========================
         */
        $pp2 = [
            ['code' => 'pp2p1', 'name' => 'P1 Сүлжээний шууд усны даралт', 'unit' => 'MPa'],
            ['code' => 'pp2p2', 'name' => 'P2 Сүлжээний буцах усны даралт', 'unit' => 'MPa'],
            ['code' => 'pp2t1', 'name' => 'T1 Сүлжээний шууд усны температур', 'unit' => '°C'],
            ['code' => 'pp2t2', 'name' => 'T2 Сүлжээний буцах усны температур', 'unit' => '°C'],
            ['code' => 'pp2g1', 'name' => 'Gсул Сүлжээний усны зарцуулалт 1', 'unit' => 'т/ц'],
            ['code' => 'pp2g2', 'name' => 'Gсул2 Сүлжээний усны зарцуулалт 2', 'unit' => 'т/ц'],
            ['code' => 'pp2gn', 'name' => 'Gну Нэмэлт усны зарцуулалт', 'unit' => 'т/ц'],
        ];

        foreach ($pp2 as $item) {
            PowerPlantThermoEquipment::create([
                'power_plant_id' => 19,
                ...$item,
            ]);
        }

        /**
         * =========================
         * ДЦС-3 ӨДХ (power_plant_id = 20)
         * =========================
         */
        $pp3h = [
            ['code' => 'pp3hp1', 'name' => 'P1 Сүлжээний шууд усны даралт (ӨДХ)', 'unit' => 'MPa'],
            ['code' => 'pp3hp2', 'name' => 'P2 Сүлжээний буцах усны даралт (ӨДХ)', 'unit' => 'MPa'],
            ['code' => 'pp3ht1', 'name' => 'T1 Сүлжээний шууд усны температур (ӨДХ)', 'unit' => '°C'],
            ['code' => 'pp3ht2', 'name' => 'T2 Сүлжээний буцах усны температур (ӨДХ)', 'unit' => '°C'],
            ['code' => 'pp3hg1', 'name' => 'Gсул Сүлжээний усны зарцуулалт 1 (ӨДХ)', 'unit' => 'т/ц'],
            ['code' => 'pp3hg2', 'name' => 'Gсул2 Сүлжээний усны зарцуулалт 2 (ӨДХ)', 'unit' => 'т/ц'],
            ['code' => 'pp3hgn', 'name' => 'Gну Нэмэлт усны зарцуулалт (ӨДХ)', 'unit' => 'т/ц'],
        ];

        foreach ($pp3h as $item) {
            PowerPlantThermoEquipment::create([
                'power_plant_id' => 20,
                ...$item,
            ]);
        }

        /**
         * =========================
         * ДЦС-3 ДДХ (power_plant_id = 21)
         * =========================
         */
        $pp3l = [
            ['code' => 'pp3lp1', 'name' => 'P1 Сүлжээний шууд усны даралт (ДДХ)', 'unit' => 'MPa'],
            ['code' => 'pp3lp2', 'name' => 'P2 Сүлжээний буцах усны даралт (ДДХ)', 'unit' => 'MPa'],
            ['code' => 'pp3lt1', 'name' => 'T1 Сүлжээний шууд усны температур (ДДХ)', 'unit' => '°C'],
            ['code' => 'pp3lt2', 'name' => 'T2 Сүлжээний буцах усны температур (ДДХ)', 'unit' => '°C'],
            ['code' => 'pp3lg1', 'name' => 'Gсул Сүлжээний усны зарцуулалт 1 (ДДХ)', 'unit' => 'т/ц'],
            ['code' => 'pp3lg2', 'name' => 'Gсул2 Сүлжээний усны зарцуулалт 2 (ДДХ)', 'unit' => 'т/ц'],
            ['code' => 'pp3lgn', 'name' => 'Gну Нэмэлт усны зарцуулалт (ДДХ)', 'unit' => 'т/ц'],
        ];

        foreach ($pp3l as $item) {
            PowerPlantThermoEquipment::create([
                'power_plant_id' => 21,
                ...$item,
            ]);
        }

        /**
         * =========================
         * ДЦС-4 (power_plant_id = 22)
         * =========================
         */
        $pp4 = [
            // Усны зарцуулалт (Gсул) - давхар утгууд
            ['code' => 'pp4700g1', 'name' => '9а Gсул', 'unit' => 'т/ц'],
            ['code' => 'pp4700g2', 'name' => '9а Gсул2', 'unit' => 'т/ц'],
            ['code' => 'pp41000g1', 'name' => '10а Gсул', 'unit' => 'т/ц'],
            ['code' => 'pp41000g2', 'name' => '10а Gсул2', 'unit' => 'т/ц'],
            ['code' => 'pp41200g1', 'name' => '11а Gсул', 'unit' => 'т/ц'],
            ['code' => 'pp41200g2', 'name' => '11а Gсул2', 'unit' => 'т/ц'],
            ['code' => 'pp4y700g1', 'name' => '15 Gсул', 'unit' => 'т/ц'],
            ['code' => 'pp4y700g2', 'name' => '15 Gсул2', 'unit' => 'т/ц'],
            ['code' => 'pp4210g1', 'name' => '16а Gсул', 'unit' => 'т/ц'],
            ['code' => 'pp4210g2', 'name' => '16 Gсул2', 'unit' => 'т/ц'],

            // Нийт болон нэмэлт
            ['code' => 'pp4g', 'name' => 'Нийт Gсул', 'unit' => 'т/ц'],
            ['code' => 'pp4gn', 'name' => 'Gну Нэмэлт усны зарцуулалт', 'unit' => 'т/ц'],

            // Даралт
            ['code' => 'pp4p1', 'name' => 'P1 Шууд усны даралт', 'unit' => 'MPa'],
            ['code' => 'pp4p2', 'name' => 'P2 Буцах усны даралт', 'unit' => 'MPa'],

            // Температур
            ['code' => 'pp4t1', 'name' => 'T1 Шууд усны температур', 'unit' => '°C'],
            ['code' => 'pp4700t2', 'name' => '9а T2 Буцах усны температур', 'unit' => '°C'],
            ['code' => 'pp41000t2', 'name' => '10а T2 Буцах усны температур', 'unit' => '°C'],
            ['code' => 'pp41200t2', 'name' => '11а T2 Буцах усны температур', 'unit' => '°C'],
            ['code' => 'pp4y700t2', 'name' => '15 T2 Буцах усны температур', 'unit' => '°C'],
            ['code' => 'pp4210t2', 'name' => '16 T2 Буцах усны температур', 'unit' => '°C'],
        ];

        foreach ($pp4 as $item) {
            PowerPlantThermoEquipment::create([
                'power_plant_id' => 22,
                ...$item,
            ]);
        }

        /**
         * =========================
         * Амгалан ДС (power_plant_id = 1)
         * =========================
         */
        $am = [
            ['code' => 'amp1', 'name' => 'P1 Даралт', 'unit' => 'MPa'],
            ['code' => 'amp2', 'name' => 'P2 Даралт', 'unit' => 'MPa'],
            ['code' => 'amt1', 'name' => 'T1 Температур', 'unit' => '°C'],
            ['code' => 'amt2', 'name' => 'T2 Температур', 'unit' => '°C'],
            ['code' => 'amt2_2', 'name' => 'T2_2 Температур', 'unit' => '°C'],
            ['code' => 'amg1', 'name' => 'Gсул Усны зарцуулалт', 'unit' => 'т/ц'],
            ['code' => 'amg2', 'name' => 'Gсул2 Усны зарцуулалт', 'unit' => 'т/ц'],
            ['code' => 'amgn', 'name' => 'Gну Нэмэлт усны зарцуулалт', 'unit' => 'т/ц'],
        ];

        foreach ($am as $item) {
            PowerPlantThermoEquipment::create([
                'power_plant_id' => 1,
                ...$item,
            ]);
        }
    }
}
