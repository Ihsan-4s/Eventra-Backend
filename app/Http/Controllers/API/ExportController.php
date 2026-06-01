<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegistrationsExport;
use App\Models\Payment;
use App\Exports\PaymentsExport;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function registrationsPdf(Request $request, Event $event)
    {
        // 1. Cek kepemilikan
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // 2. Ambil data peserta
        $registrations = Registration::with(['payment', 'attendance'])
            ->where('event_id', $event->id)
            ->orderBy('created_at')
            ->get();

        // 3. Generate PDF
        $pdf = Pdf::loadView('exports.registrations_pdf', [
            'event'         => $event,
            'registrations' => $registrations,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Peserta-' . $event->slug . '.pdf');
    }

    public function registrationsXlsx(Request $request, Event $event)
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return Excel::download(
            new RegistrationsExport($event->id),
            'Peserta-' . $event->slug . '.xlsx'
        );
    }

    public function paymentsPdf(Request $request, Event $event)
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $payments = Payment::with('registration')
            ->whereHas('registration', fn($q) => $q->where('event_id', $event->id))
            ->orderBy('created_at')
            ->get();

        $pdf = Pdf::loadView('exports.payments_pdf', [
            'event'    => $event,
            'payments' => $payments,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Transaksi-' . $event->slug . '.pdf');
    }

    public function paymentsXlsx(Request $request, Event $event)
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return Excel::download(
            new PaymentsExport($event->id),
            'Transaksi-' . $event->slug . '.xlsx'
        );
    }
}
