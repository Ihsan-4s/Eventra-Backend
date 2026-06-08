<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function index(Event $event, Request $request)
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $registrations = $event->registrations()
            ->with('payment')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'registrations' => $registrations,
        ]);
    }

    public function store(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string|max:20',
        ]);

        $confirmedCount = $event->registrations()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($confirmedCount >= $event->quota) {
            return response()->json(['message' => 'Kuota event sudah penuh.'], 422);
        }

        $result = DB::transaction(function () use ($request, $event) {
            $registration = Registration::create([
                'event_id' => $event->id,
                'registration_code' => 'REG-'.strtoupper(Str::random(10)),
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'registration_date' => now(),
                'status' => 'pending',
            ]);

            $payment = Payment::create([
                'registration_id' => $registration->id,
                'amount' => $event->price,
                'transaction_id' => 'TRX-'.strtoupper(Str::random(12)),
                'payment_status' => 'unpaid',
            ]);

            return ['registration' => $registration, 'payment' => $payment];
        });

        return response()->json([
            'message' => 'Registrasi berhasil. Silakan lakukan pembayaran.',
            'registration' => $result['registration'],
            'payment' => $result['payment'],
        ], 201);
    }
}
