<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Models\Organization;
use App\Models\Division;
use App\Models\PermissionLevel;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'organization_id',
        'division_id',
        'div_code',
        'permission_level_id',
        'usercode',
    ];

    public function username()
    {
        return 'usercode';   // ← энд usercode байх ёстой
    }


    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Хэрэглэгчийн байгууллагын үндсэн станц
     */
    public function mainPowerPlant()
    {
        return $this->hasOneThrough(
            PowerPlant::class,
            Organization::class,
            'id',              // organizations table-н foreign key
            'organization_id', // power_plants table-н foreign key
            'organization_id', // users table-н local key
            'id'               // organizations table-н local key
        )->whereNull('power_plants.parent_id'); // Зөвхөн үндсэн станцууд
    }

    public function permissionLevel()
    {
        return $this->belongsTo(PermissionLevel::class, 'permission_level_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
