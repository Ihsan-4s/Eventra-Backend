<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    public function show(string $transactionId)
    {
        $payment = Payment::with(['registration.event'])
            ->where('transaction_id', $transactionId)
            ->firstOrFail();
        $qrCode = null;
        if ($payment->payment_status === 'unpaid') {
            $qrContent = 'QRIS-EVENTRA-'.$payment->transaction_id;
            $qrCode = base64_encode(
                QrCode::format('svg')->size(300)->generate($qrContent)
            );
        }

        return response()->json([
            'payment' => $payment,
            'qr_code' => $qrCode,
        ]);
    }

    public function simulatePay(string $transactionId)
    {
        $payment = Payment::with('registration')
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        if ($payment->payment_status === 'paid') {
            return response()->json(['message' => 'Pembayaran sudah berhasil sebelumnya.'], 422);
        }

        $ticket = DB::transaction(function () use ($payment) {
            $payment->update([
                'payment_status' => 'paid',
            ]);
            $payment->registration->update([
                'status' => 'confirmed',
            ]);
            return Ticket::create([
                'registration_id' => $payment->registration->id,
                'ticket_code' => 'TIX-'.strtoupper(Str::random(12)),
                'qr_code' => 'https://eventra.com/verify/'.Str::random(12),
            ]);
        });
        return response()->json([
            'message' => 'Pembayaran berhasil. Tiket telah dibuat.',
            'ticket' => $ticket,
        ]);
    }
}
