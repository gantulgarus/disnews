<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationThermoData extends Model
{
    protected $table = 'station_thermo_data';

    // сул - сүлжээний усны зарцуулалт
    // ну - нэмэлт усны зарцуулалт

    protected $fillable = [
        'infodate', // date
        'infotime', // time

        'pp2p1', // P1 ДЦС-2
        'pp2p2', // P2 ДЦС-2
        'pp2t1', // T1 ДЦС-2
        'pp2t2', // T2 ДЦС-2
        'pp2g1', // Gсул ДЦС-2
        'pp2g2',
        'pp2gn', // Gну ДЦС-2

        'pp3hp1', // P1 ДЦС-3 ӨДХ
        'pp3hp2', // P2 ДЦС-3 ӨДХ
        'pp3ht1', // T1 ДЦС-3 ӨДХ
        'pp3ht2', // T2 ДЦС-3 ӨДХ
        'pp3hg1', // Gсул ДЦС-3 ӨДХ
        'pp3hg2',
        'pp3hgn', // Gну ДЦС-3 ӨДХ

        'pp3lp1', // P1 ДЦС-3 ДДХ
        'pp3lp2', // P2 ДЦС-3 ДДХ
        'pp3lt1', // T1 ДЦС-3 ДДХ
        'pp3lt2', // T2 ДЦС-3 ДДХ
        'pp3lg1', // Gсул ДЦС-3 ДДХ
        'pp3lg2',
        'pp3lgn', // Gну ДЦС-3 ДДХ

        'pp4700g1', // 9a Gсул ДЦС-4
        'pp41000g1', // 10a Gсул ДЦС-4
        'pp41200g1', // 11а Gсул ДЦС-4
        'pp4y700g1', // 15 Gсул ДЦС-4
        'pp4210g1', // 16a Gсул ДЦС-4
        'pp4g', // Нийт Gсул ДЦС-4
        'pp4gn', // Gну ДЦС-4
        'pp4p1', // P1 ДЦС-4
        'pp4p2', // P2 ДЦС-4
        'pp4t1', // T1 ДЦС-4
        'pp4700t2', // 9a T2 ДЦС-4
        'pp41000t2', // 10а T2 ДЦС-4
        'pp41200t2', // 11а T2 ДЦС-4
        'pp4y700t2', // 15 T2 ДЦС-4
        'pp4210t2', // 16 T2 ДЦС-4
        'pp4700g2',
        'pp41000g2',
        'pp41200g2',
        'pp4y700g2',
        'pp4210g2',

        'amp1', // P1 Амгалан ДС
        'amp2', // P2 Амгалан ДС
        'amt1', // T1 Амгалан ДС
        'amt2', // T2 Амгалан ДС
        'amt2_2', // T2_2 Амгалан ДС
        'amg1', // Gсул Амгалан ДС
        'amg2', // Gсул2 Амгалан ДС
        'amgn' // Gну Амгалан ДС
    ];
}
