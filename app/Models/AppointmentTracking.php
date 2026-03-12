<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentTracking extends Model
{
    protected $fillable = [
        'appointment_id',
        'assigned_to',
        'assigned_by',
        'action',
        'remarks',
    ];

    public const ACTION_ASSIGNED         = 'assigned';
    public const ACTION_DELIVERY_UPDATED = 'delivery_status_updated';
public const ACTION_STATUS_UPDATED    = 'status_updated';
public const ACTION_REFERENCE_CHANGED = 'reference_changed';
  public const ACTION_EMAIL_CHANGED = 'email_changed';


public const ACTION_REASSIGNED       = 'reassigned';


    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // Who the application was sent TO
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Who performed the action
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }


}
