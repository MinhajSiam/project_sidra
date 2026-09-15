<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Booking;
use App\Models\Event;

class BookingController {
    public function index(Request $request): Response {
        $filters = [
            'status' => $request->get('status'),
            'event_id' => $request->get('event_id'),
            'search' => $request->get('search'),
        ];

        $bookings = Booking::all($filters);
        $events = Event::all();

        return (new Response())->setContent(
            View::render('admin.bookings.index', [
                'bookings' => $bookings,
                'events' => $events,
                'filters' => $filters,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function show(Request $request, string $id): Response {
        $booking = Booking::find((int)$id);
        if (!$booking) {
            flash('error', 'Booking not found.');
            redirect('/admin/bookings');
        }

        return (new Response())->setContent(
            View::render('admin.bookings.show', [
                'booking' => $booking,
                'layout' => 'layouts.admin',
            ])
        );
    }
}
