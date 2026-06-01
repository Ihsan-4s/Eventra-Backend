<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


class PaymentController extends Controller
{
    public function show(string $transactionId)
    {
        $payment = Payment::with(['registration.event'])
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        return response()->json(['payment' => $payment]);
    }

    public function simulatePay(string $transactionId)
    {
        $payment = Payment::with('registration')
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        if ($payment->payment_status === 'paid') {
            return response()->json(['message' => 'Pembayaran sudah berhasil sebelumnya.'], 422);
        }

        DB::transaction(function () use ($payment) {
            // 1. Update status payment
            $payment->update(['payment_status' => 'paid']);

            // 2. Update status registrasi
            $payment->registration->update(['status' => 'confirmed']);

            // 3. Generate tiket otomatis
            Ticket::create([
                'registration_id' => $payment->registration->id,
                'ticket_code'     => 'TIX-' . strtoupper(Str::random(12)),
                'qr_code'         => 'https://eventra.com/verify/' . Str::random(12),
            ]);
        });

        $ticket = $payment->registration->fresh()->ticket;

        return response()->json([
            'message' => 'Pembayaran berhasil. Tiket telah dibuat.',
            'ticket'  => $ticket,
        ]);
    }
}
