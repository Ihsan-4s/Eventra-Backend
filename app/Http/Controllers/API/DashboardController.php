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
        $totalEvents= Event::where('user_id', $userId)->count();
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
                'total_events'=> $totalEvents,
                'published_events'=> $publishedEvents,
                'total_participants' => $totalParticipants,
                'total_revenue' => $totalRevenue,
                'total_check_in'=> $totalCheckIn,
            ],
        ]);
    }

    public function admin()
    {
        $totalOrganizers = User::where('role', 'organizer')->count();
        $totalEvents= Event::count();
        $totalRevenue= Payment::where('payment_status', 'paid')->sum('amount');
        $totalTickets= Ticket::count();
        return response()->json([
            'summary' => [
                'total_organizers'=> $totalOrganizers,
                'total_events'=> $totalEvents,
                'total_revenue'=> $totalRevenue,
                'total_tickets'=> $totalTickets,
            ],
        ]);
    }
}
