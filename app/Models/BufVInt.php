<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BufVInt extends Model
{
    protected $table = 'buf_v_int';

    protected $fillable = [
        'SYB_RNK',
        'N_OB',
        'N_FID',
        'N_GR_TY',
        'N_SH',
        'DD_MM_YYYY',
        'N_INTER_RAS',
        'KOL_DB',
        'KOL',
        'VAL',
        'STAT',
        'MIN_0',
        'MIN_1',
        'INTERV',
        'AK_SUM',
        'POK_START',
        'RASH_POLN',
        'IMPULSES'
    ];
}