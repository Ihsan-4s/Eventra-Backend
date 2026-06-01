<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private int $eventId) {}

    public function query()
    {
        return Payment::with('registration')
            ->whereHas('registration', fn($q) => $q->where('event_id', $this->eventId))
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'No', 'Transaction ID', 'Nama Peserta',
            'Email', 'Jumlah', 'Status Pembayaran', 'Tanggal Transaksi',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->transaction_id,
            $row->registration?->full_name ?? '-',
            $row->registration?->email ?? '-',
            $row->amount,
            $row->payment_status,
            $row->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
