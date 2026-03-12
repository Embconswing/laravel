<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const TYPE_NORMAL     = 0;
    const TYPE_OFFICER    = 1;
    const TYPE_SUPERVISOR = 2;
    const TYPE_ADMIN      = 5;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
    ];

    /* =========================
       Relationships
       ========================= */

    public function assignedAppointments()
    {
        return $this->hasMany(
            Appointment::class,
            'current_assignee_id'
        );
    }

    /* =========================
       Helper methods
       ========================= */

    public function isNormal()
    {
        return $this->user_type === self::TYPE_NORMAL;
    }

    public function isOfficer()
    {
        return $this->user_type === self::TYPE_OFFICER;
    }

    public function isSupervisor()
    {
        return $this->user_type === self::TYPE_SUPERVISOR;
    }

    public function isAdmin()
    {
        return $this->user_type === self::TYPE_ADMIN;
    }
}
