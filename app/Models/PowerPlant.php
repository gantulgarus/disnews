<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerPlant extends Model
{
    protected $fillable = [
        'short_name',
        'name',
        'power_plant_type_id',
        'region',
        'z',
        't',
        'Order',
        'organization_id',
        'parent_id',
    ];

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function powerInfos()
    {
        return $this->hasMany(StationPowerInfo::class);
    }
    public function equipmentStatuses()
    {
        return $this->hasMany(EquipmentStatus::class);
    }
    public function powerPlantType()
    {
        return $this->belongsTo(PowerPlantType::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function parent()
    {
        return $this->belongsTo(PowerPlant::class, 'parent_id');
    }

    public function subStations()
    {
        return $this->hasMany(PowerPlant::class, 'parent_id');
    }

    // Scope-ууд

    /**
     * Зөвхөн үндсэн станцууд (parent_id = null)
     * Хоногийн тооцоо, Ачааллын график, Нүүрсний мэдээнд хэрэглэнэ
     */
    public function scopeMainStations($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Хоногийн мэдээ, Горимд зориулсан станцууд:
     * - Дэд станцтай үндсэн станцын хувьд: дэд станцуудыг авна
     * - Дэд станцгүй үндсэн станцын хувьд: өөрийгөө авна
     */
    public function scopeForDailyReport($query)
    {
        return $query->where(function ($q) {
            // Дэд станцууд
            $q->whereNotNull('parent_id')
                // Эсвэл дэд станцгүй үндсэн станцууд
                ->orWhere(function ($subQuery) {
                    $subQuery->whereNull('parent_id')
                        ->whereDoesntHave('subStations');
                });
        });
    }

    /**
     * Станц нь дэд станцтай эсэхийг шалгах
     */
    public function hasSubStations()
    {
        return $this->subStations()->exists();
    }

    /**
     * Дэд станц эсэхийг шалгах
     */
    public function isSubStation()
    {
        return !is_null($this->parent_id);
    }

    /**
     * Үндсэн станцыг авах (өөрөө үндсэн бол өөрийгөө буцаана)
     */
    public function getMainStation()
    {
        return $this->parent ?? $this;
    }

    /**
     * Харуулах нэрийг авах (дэд станц бол parent-ийн нэртэй хамт)
     */
    public function getDisplayNameAttribute()
    {
        if ($this->isSubStation() && $this->parent) {
            return $this->parent->name . ' - ' . $this->name;
        }
        return $this->name;
    }
}