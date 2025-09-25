<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationThermoData extends Model
{
    protected $table = 'station_thermo_data';

    protected $fillable = [
        'infodate',
        'infotime',
        'pp2p1',
        'pp2p2',
        'pp2t1',
        'pp2t2',
        'pp2g1',
        'pp2g2',
        'pp2gn',
        'pp3hp1',
        'pp3hp2',
        'pp3ht1',
        'pp3ht2',
        'pp3hg1',
        'pp3hg2',
        'pp3hgn',
        'pp3lp1',
        'pp3lp2',
        'pp3lt1',
        'pp3lt2',
        'pp3lg1',
        'pp3lg2',
        'pp3lgn',
        'pp4p1',
        'pp4p2',
        'pp4t1',
        'pp4700t2',
        'pp41000t2',
        'pp41200t2',
        'pp4y700t2',
        'pp4700g1',
        'pp41000g1',
        'pp41200g1',
        'pp4y700g1',
        'pp4700g2',
        'pp41000g2',
        'pp41200g2',
        'pp4y700g2',
        'pp4gn',
        'pp4g',
        'pp4210t2',
        'pp4210g1',
        'pp4210g2',
        'amp1',
        'amp2',
        'amt1',
        'amt2',
        'amt2_2',
        'amg1',
        'amg2',
        'amgn'
    ];
}