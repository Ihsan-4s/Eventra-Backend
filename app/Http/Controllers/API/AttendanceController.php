<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkIn(Request $request, Event $event)
    {
        // cek eo ny dulu
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'ticket_code' => 'required|string',
        ]);
        //ambil tiket
        $ticket = Ticket::with('registration')
            ->where('ticket_code', $request->ticket_code)
            ->first();
        //kalo gada
        if   (!$ticket) {
            return response()->json(['message' => 'Tiket tidak ditemukan.'], 404);
        }

        $registration = $ticket->registration;
        // tiket punya event mana
        if ($registration->event_id !== $event->id) {
            return response()->json(['message' => 'Tiket bukan milik event ini.'], 422);
        }
        // cek dah bayar belom
        if ($registration->status !== 'confirmed') {
            return response()->json(['message' => 'Pembayaran belum selesai.'], 422);
        }
        // cek dah cekin blm
        if (Attendance::where('registration_id', $registration->id)->exists()) {
            return response()->json(['message' => 'Peserta sudah check-in sebelumnya.'], 422);
        }
        //buat kehadirannya kalo dah lewat pengecekan
        $attendance = Attendance::create([
            'registration_id'=> $registration->id,
            'check_in_time'=> now(),
            'attendance_status' => 'present',
        ]);

        return response()->json([
            'message'=> 'Check-in berhasil.',
            'full_name'=> $registration->full_name,
            'check_in_time' => $attendance->check_in_time,
        ]);
    }

    public function index(Request $request, Event $event)
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $attendances = Attendance::with('registration')
            // cari ini event mana buat sorting data make select nanti
            ->whereHas('registration', fn($q) => $q->where('event_id', $event->id))
            ->orderBy('check_in_time', 'desc')
            ->get();

        return response()->json(['attendances' => $attendances]);
    }
}
