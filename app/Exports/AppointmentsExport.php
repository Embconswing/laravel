<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class AppointmentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from;
    protected $to;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    public function collection()
    {
       $query = Appointment::select(
    'id',
    'appointment_no',
    'ReferenceNr',
    'service',
    'appointment_date',
    'applicant_name',
     'date_of_birth', 
     'nationality',
    'email',
    'status',
    'created_at'
)->where('status', 'completed');

        if ($this->from) {
            $query->whereDate('appointment_date', '>=', $this->from);
        }

        if ($this->to) {
            $query->whereDate('appointment_date', '<=', $this->to);
        }

        return $query->get();
    }

    public function map($appointment): array
    {
        return [
            $appointment->id,
            $appointment->appointment_no,
            $appointment->ReferenceNr,
            $appointment->service,

            // ✅ formatted date
            Carbon::parse($appointment->appointment_date)->format('d-m-Y'),

            $appointment->applicant_name,
             $appointment->date_of_birth
                ? Carbon::parse($appointment->date_of_birth)->format('d-m-Y')
                : '',
            $appointment->nationality,    
            $appointment->email,
            $appointment->status,

            // optional: formatted created_at
            Carbon::parse($appointment->created_at)->format('d-m-Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Appointment No',
            'Reference No',
            'Service',
            'Appointment Date',
            'Applicant Name',
            'Date of Birth',
            'Nationality',
            'Email',
            'Status',
            'Created At',
        ];
    }
}
