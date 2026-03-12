<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    /* =========================
     | STATUS CONSTANTS
     |========================= */

    const STATUS_ACCEPTED               = 'accepted';
    const STATUS_ASSIGNED               = 'assigned';
    const STATUS_PROCESSING             = 'processing';
    const STATUS_RETURNED_TO_SUPERVISOR = 'returned_to_supervisor';
    const STATUS_PROCESS_IN_PROGRESS    = 'underprocess';
    const STATUS_PROCESS_COMPLETED      = 'processcompleted';
    //const STATUS_COMPLETED              = 'countercompleted';
    const STATUS_PENDING                = 'pending';
    const STATUS_CANCELLED              = 'cancelled';
   // const STATUS_CLARIFICATION          = 'clarification';
   const STATUS_SENT_TO_ATTACHE = 'sent_to_attache';
   const STATUS_EMAIL_FAILED = 'email_failed';
 


    /* =========================
     | DELIVERY STATUS
     |========================= */

    const STATUS_MAIL       = 'readytomail';
    const STATUS_EMAILSENT  = 'emailsent';
    const STATUS_COLLECTED  = 'collected';
    const STATUS_DISPATCHED = 'dispatched';
    const STATUS_RETURNED = 'post_returned';

    /* =========================
     | LABELS
     |========================= */

    public const STATUS_LABELS = [
        self::STATUS_ASSIGNED              => 'Assigned for Processing',
        self::STATUS_ACCEPTED              => 'Accepted at Counter',
        self::STATUS_PENDING               => 'Pending from Counter',
        //self::STATUS_PROCESSING            => 'Processing',
        self::STATUS_PROCESS_IN_PROGRESS   => 'Under Process-Backend',
        self::STATUS_PROCESS_COMPLETED     => 'Process Completed',
        self::STATUS_RETURNED_TO_SUPERVISOR=> 'Returned from Officer',
       // self::STATUS_COMPLETED             => 'Completed',
        self::STATUS_CANCELLED             => 'Cancelled',
        //self::STATUS_CLARIFICATION         => 'Clarification',
        self::STATUS_SENT_TO_ATTACHE => 'Sent to Consular Attaché',
    ];

    public const STATUS_COLORS = [
        self::STATUS_ASSIGNED              => 'primary',
        self::STATUS_ACCEPTED              => 'info',
        self::STATUS_PENDING               => 'warning',
        self::STATUS_PROCESSING            => 'secondary',
        self::STATUS_PROCESS_IN_PROGRESS   => 'secondary',
        self::STATUS_PROCESS_COMPLETED     => 'success',
        self::STATUS_RETURNED_TO_SUPERVISOR=> 'danger',
       // self::STATUS_COMPLETED             => 'success',
        self::STATUS_CANCELLED             => 'dark',
       // self::STATUS_CLARIFICATION         => 'warning',
        self::STATUS_SENT_TO_ATTACHE => 'info',
    ];

    public const DELIVERY_LABELS = [
    self::STATUS_MAIL        => 'Ready to Mail',
    self::STATUS_EMAILSENT   => 'Email Sent',
    self::STATUS_EMAIL_FAILED => 'Email Failed',
    self::STATUS_COLLECTED   => 'Collected',
    self::STATUS_DISPATCHED  => 'Dispatched',
    self::STATUS_RETURNED    => 'Post Returned',
];

    public const DELIVERY_COLORS = [
        self::STATUS_MAIL       => 'info',
        self::STATUS_EMAILSENT  => 'success',
        self::STATUS_COLLECTED  => 'dark',
        self::STATUS_DISPATCHED => 'secondary',
         self::STATUS_RETURNED   => 'danger',
         self::STATUS_EMAIL_FAILED => 'danger',
    ];

    public const DELIVERY_ELIGIBLE_STATUSES = [
        self::STATUS_PROCESS_COMPLETED,
    ];

    /* =========================
     | MASS ASSIGNMENT
     |========================= */

    protected $fillable = [
        'appointment_no',
        'ReferenceNr',
        'service',
        'appointment_date',
        'appointment_start_time',
        'appointment_end_time',
        'window_no',
        'processed_by',
        'applicant_name',
        'gender',
        'nationality',
        'mobile_number',
        'email',
        'date_of_birth',
        'address',
        'status',
        'current_assignee_id', 
        'status_changed_at',
        'delivery_status',
        'delivery_status_changed_at',
        'passportno',
        
    ];

    /* =========================
     | CASTS
     |========================= */


    protected $casts = [
    'called_at'        => 'datetime',
    'completed_at'     => 'datetime',
    'appointment_date' => 'date',
    'start_time'       => 'datetime',
    'status_changed_at'=> 'datetime',
    'delivery_status_changed_at'=> 'datetime',
];
    /* =========================
     | RELATIONSHIPS
     |========================= */

    public function window()
    {
        return $this->belongsTo(Window::class);
    }

    public function documentCollection()
    {
        return $this->hasOne(
            \App\Models\DocumentCollection::class,
            'appointment_no',
            'appointment_no'
        );
    }

    public function currentAssignee()
    {
        return $this->belongsTo(User::class, 'current_assignee_id');
    }

    public function trackings()
    {
        return $this->hasMany(AppointmentTracking::class)->latest();
    }

    /**
     * ✅ LATEST ASSIGNMENT (CORRECT VERSION)
     * Filters assignment rows first,
     * then selects the most recent one.
     */
    public function latestAssignmentTracking()
{
    return $this->hasOne(AppointmentTracking::class)
        ->whereIn('action', [
            AppointmentTracking::ACTION_ASSIGNED,
            AppointmentTracking::ACTION_REASSIGNED,
        ])
        ->orderByDesc('created_at');
}

    /* =========================
     | HELPERS
     |========================= */

    public static function userFullFlow()
    {
        return [
            self::STATUS_PROCESS_IN_PROGRESS,
            self::STATUS_PROCESS_COMPLETED,
            self::STATUS_MAIL,
            self::STATUS_EMAILSENT,
            self::STATUS_DISPATCHED,
            self::STATUS_COLLECTED,
        ];
    }

   public function canHaveDeliveryStatus(): bool
{
    // First: must be in eligible main statuses
    if (!in_array($this->status, self::DELIVERY_ELIGIBLE_STATUSES, true)) {
        return false;
    }

    // Second: once dispatched or collected, lock permanently
    if (in_array($this->delivery_status, [
        self::STATUS_DISPATCHED,
        self::STATUS_COLLECTED,

    ], true)) {
        return false;
    }

    return true;
}
protected static function booted()
{
    static::creating(function ($a) {
        if (empty($a->status_changed_at)) {
            $a->status_changed_at = now();
        }
    });

    static::updating(function ($a) {
        if ($a->isDirty('status')) {
            $a->status_changed_at = now();
        }

        if ($a->isDirty('delivery_status')) {
            $a->delivery_status_changed_at = now();
        }
    });
}
public const STATUS_TARGET_DAYS = [
    self::STATUS_ASSIGNED               => 2,
    self::STATUS_ACCEPTED               => 2,
    self::STATUS_PENDING                => 3,
    self::STATUS_PROCESSING             => 5,
    self::STATUS_PROCESS_IN_PROGRESS    => 20,
    self::STATUS_PROCESS_COMPLETED      => 2,
    self::STATUS_RETURNED_TO_SUPERVISOR => 3,
   // self::STATUS_CLARIFICATION          => 3,
    // completed/cancelled usually not tracked for SLA, but you can add if you want
];

public function getStatusDaysAttribute(): int
{
    $since = $this->status_changed_at ?? $this->updated_at ?? $this->created_at;

    return $since
        ? $since->copy()->startOfDay()->diffInDays(now()->startOfDay())
        : 0;
}


public function getStatusPercentAttribute(): int
{
    $target = $this->status_target_days;
    if ($target <= 0) return 100;

    $percent = (int) min(100, round(($this->status_days / $target) * 100));

    // ✅ Ensure visible minimum
    return max($percent, 8);
}

public function getStatusTargetDaysAttribute(): int
{
    return self::STATUS_TARGET_DAYS[$this->status] ?? 3;
}


public function getStatusBarClassAttribute(): string
{
    $days = $this->status_days;
    $target = $this->status_target_days;
    $percent = $this->status_percent;

    if ($days > $target) return 'bg-danger';
    if ($percent >= 70) return 'bg-warning';
    return 'bg-success';
}

public function getStatusSinceLabelAttribute(): string
{
    $since = $this->status_changed_at ?? $this->updated_at ?? $this->created_at;
    return $since ? $since->format('d M Y') : '';
}

public function track($action, $assignedTo = null, $remarks = null)
{
    if (!$action) {
        return;
    }

    $this->trackings()->create([
        'assigned_by' => auth()->id(),
        'assigned_to' => $assignedTo,
        'action'      => $action,
        'remarks'     => $remarks,
    ]);
}



}
