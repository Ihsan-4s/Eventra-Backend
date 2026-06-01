<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function organizer(Request $request)
    {
        $userId = $request->user()->id;

        $totalEvents     = Event::where('user_id', $userId)->count();
        $publishedEvents = Event::where('user_id', $userId)->where('status', 'published')->count();

        $totalParticipants = Registration::whereHas('event', fn($q) => $q->where('user_id', $userId))
            ->where('status', 'confirmed')
            ->count();

        $totalRevenue = Payment::whereHas('registration.event', fn($q) => $q->where('user_id', $userId))
            ->where('payment_status', 'paid')
            ->sum('amount');

        $totalCheckIn = Attendance::whereHas('registration.event', fn($q) => $q->where('user_id', $userId))
            ->where('attendance_status', 'present')
            ->count();

        return response()->json([
            'summary' => [
                'total_events'       => $totalEvents,
                'published_events'   => $publishedEvents,
                'total_participants' => $totalParticipants,
                'total_revenue'      => $totalRevenue,
                'total_check_in'     => $totalCheckIn,
            ],
        ]);
    }

    public function eventAnalytics(Request $request, Event $event)
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $confirmed = $event->registrations()->where('status', 'confirmed')->count();
        $pending   = $event->registrations()->where('status', 'pending')->count();
        $cancelled = $event->registrations()->where('status', 'cancelled')->count();
        $checkIn   = Attendance::whereHas('registration', fn($q) => $q->where('event_id', $event->id))
            ->where('attendance_status', 'present')->count();
        $revenue   = Payment::whereHas('registration', fn($q) => $q->where('event_id', $event->id))
            ->where('payment_status', 'paid')->sum('amount');

        return response()->json([
            'event' => [
                'id'         => $event->id,
                'title'      => $event->title,
                'event_date' => $event->event_date,
                'quota'      => $event->quota,
                'price'      => $event->price,
            ],
            'summary' => [
                'confirmed'  => $confirmed,
                'pending'    => $pending,
                'cancelled'  => $cancelled,
                'check_in'   => $checkIn,
                'revenue'    => $revenue,
                'quota_left' => $event->quota - ($confirmed + $pending),
            ],
        ]);
    }

    public function admin()
    {
        $totalOrganizers = User::where('role', 'organizer')->count();
        $totalEvents     = Event::count();
        $totalRevenue    = Payment::where('payment_status', 'paid')->sum('amount');
        $totalTickets    = Ticket::count();

        return response()->json([
            'summary' => [
                'total_organizers' => $totalOrganizers,
                'total_events'     => $totalEvents,
                'total_revenue'    => $totalRevenue,
                'total_tickets'    => $totalTickets,
            ],
        ]);
    }
}
