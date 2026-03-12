<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCollection extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'appointment_no',
        'collection_date',
        'collection_time',
        'passport_no',
        'email_sent_at',
    ];

    /**
     * Relationship: this collection belongs to an appointment
     * Linked via appointment_no (not id).
     */
    public function appointment()
    {
        return $this->belongsTo(
            Appointment::class,
            'appointment_no',   // FK on this table
            'appointment_no'    // unique key on appointments table
        );
    }

    
}
