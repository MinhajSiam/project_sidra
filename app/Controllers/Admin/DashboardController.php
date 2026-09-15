<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Payment;
use App\Models\TicketCheckin;

class DashboardController {
    public function index(Request $request): Response {
        $user = Auth::user();
        if ($user && $user['role_name'] === 'gate_staff') {
            redirect('/gate/scan');
        }

        // Core KPIs
        $totalRevenue = Database::fetch("SELECT SUM(amount) as total FROM `payments` WHERE status = 'approved'");
        $totalBookings = Database::fetch("SELECT COUNT(*) as total FROM `bookings`");
        $pendingPayments = Database::fetch("SELECT COUNT(*) as total FROM `payments` WHERE status = 'pending'");
        $ticketsSold = Database::fetch("SELECT COUNT(*) as total FROM `tickets` WHERE status IN ('valid', 'used')");
        $checkedInTickets = Database::fetch("SELECT COUNT(*) as total FROM `tickets` WHERE status = 'used'");

        // Quick lists
        $pendingPaymentList = Payment::all(['status' => 'pending', 'limit' => 5]);
        $recentBookings = Booking::all(['limit' => 5]);
        $upcomingEvents = Event::all(['status' => 'published', 'limit' => 5]);
        $recentScans = TicketCheckin::all(['limit' => 5]);

        return (new Response())->setContent(
            View::render('admin.dashboard', [
                'totalRevenue' => (float)($totalRevenue['total'] ?? 0),
                'totalBookings' => (int)($totalBookings['total'] ?? 0),
                'pendingPaymentsCount' => (int)($pendingPayments['total'] ?? 0),
                'ticketsSoldCount' => (int)($ticketsSold['total'] ?? 0),
                'checkedInCount' => (int)($checkedInTickets['total'] ?? 0),
                'pendingPayments' => $pendingPaymentList,
                'recentBookings' => $recentBookings,
                'upcomingEvents' => $upcomingEvents,
                'recentScans' => $recentScans,
                'user' => Auth::user(),
                'layout' => 'layouts.admin',
            ])
        );
    }
}
