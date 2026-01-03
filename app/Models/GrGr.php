<?php
// app/Models/GrGr.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrGr extends Model
{
    protected $table = 'gr_gr';

    protected $fillable = [
        'N_OB',
        'SYB_RNK',
        'N_GR_INTEGR',
        'TYP_POK',
        'INTERV',
        'N_OB_TY',
        'SYB_RNK_TY',
        'N_FID',
        'N_GR_TY',
        'ZNAK',
        'N_SH',
        'INTERV_TY',
        'SV'
    ];

    protected $casts = [
        'INTERV' => 'integer',
        'INTERV_TY' => 'integer',
    ];

    // Активтай фидерүүд
    public function scopeActive($query)
    {
        return $query->where('ZNAK', 1);
    }

    // Тодорхой фидерүүд
    public function scopeForFeeders($query, array $fids)
    {
        return $query->whereIn('N_FID', $fids);
    }
}
