<?php

namespace App\Exports;

use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RegistrationsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private int $eventId) {}

    public function query()
    {
        return Registration::with(['payment', 'attendance'])
            ->where('event_id', $this->eventId)
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'No', 'Kode Registrasi', 'Nama Lengkap', 'Email',
            'No. HP', 'Tanggal Daftar', 'Status Registrasi',
            'Status Pembayaran', 'Jumlah Bayar', 'Status Kehadiran',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->registration_code,
            $row->full_name,
            $row->email,
            $row->phone_number,
            $row->registration_date,
            $row->status,
            $row->payment?->payment_status ?? '-',
            $row->payment?->amount ?? 0,
            $row->attendance?->attendance_status ?? 'absent',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
