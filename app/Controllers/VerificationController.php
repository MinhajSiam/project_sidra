<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketCheckin;

class VerificationController {
    /**
     * Gate Staff QR Camera Scanner View
     */
    public function scan(Request $request): Response {
        $events = Event::all(['status' => 'published']);
        $selectedEventId = (int)$request->get('event_id', $events[0]['id'] ?? 0);
        $stats = $selectedEventId > 0 ? TicketCheckin::statsByEvent($selectedEventId) : null;
        $recentScans = TicketCheckin::all(['event_id' => $selectedEventId, 'limit' => 10]);

        return (new Response())->setContent(
            View::render('tickets.scan', [
                'events' => $events,
                'selectedEventId' => $selectedEventId,
                'stats' => $stats,
                'recentScans' => $recentScans,
                'staffUser' => Auth::user(),
                'layout' => 'layouts.gate',
            ])
        );
    }

    /**
     * Gate Staff Check-in API (Processed via camera or manual lookup)
     */
    public function verify(Request $request): Response {
        $identifier = $request->input('identifier');
        $gateName = $request->input('gate_name', 'Main Gate');
        $staffUserId = Auth::id() ?? 1;

        if (!$identifier) {
            return (new Response())->json([
                'success' => false,
                'status' => 'invalid',
                'title' => 'NO CODE PROVIDED',
                'message' => 'Please scan a valid QR code or enter a ticket code.',
            ], 400);
        }

        // Process atomic checkin
        $result = Ticket::processCheckin($identifier, $staffUserId, $gateName);

        $httpCode = 200;
        if ($result['status'] === 'invalid') {
            $httpCode = 404;
        } elseif ($result['status'] === 'duplicate') {
            $httpCode = 409;
        }

        return (new Response())->json([
            'success' => $result['status'] === 'valid',
            'status' => $result['status'],
            'title' => $result['title'],
            'message' => $result['message'],
            'ticket' => $result['ticket'] ? [
                'ticket_code' => $result['ticket']['ticket_code'],
                'attendee_name' => $result['ticket']['attendee_name'],
                'event_title' => $result['ticket']['event_title'],
                'ticket_type_name' => $result['ticket']['ticket_type_name'],
                'price' => $result['ticket']['price'],
                'venue_name' => $result['ticket']['venue_name'],
            ] : null,
            'timestamp' => date('h:i:s A'),
        ], $httpCode);
    }

    /**
     * Public QR verification destination (when scanned by any smartphone camera)
     */
    public function publicVerify(Request $request, string $token): Response {
        $ticket = Ticket::findByToken($token);

        return (new Response())->setContent(
            View::render('tickets.verify', [
                'ticket' => $ticket,
                'token' => $token,
                'layout' => 'layouts.main',
            ])
        );
    }
}
