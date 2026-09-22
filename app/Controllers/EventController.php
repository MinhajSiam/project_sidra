<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\TicketType;

class EventController
{
    public function index(Request $request): Response
    {
        $categorySlug = $request->get('category');
        $categoryId = null;
        if ($categorySlug) {
            $cat = EventCategory::findBySlug($categorySlug);
            $categoryId = $cat ? (int)$cat['id'] : null;
        }

        $page = max(1, (int)$request->get('page', 1));
        $perPage = 9;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'status' => 'published',
            'category_id' => $categoryId,
            'search' => $request->get('q'),
            'location' => $request->get('location'),
            'date_from' => $request->get('date_from'),
            'sort' => $request->get('sort', 'upcoming'),
            'limit' => $perPage,
            'offset' => $offset,
        ];

        $events = Event::all($filters);
        $totalEvents = Event::count($filters);
        $totalPages = (int)ceil($totalEvents / $perPage);
        $categories = EventCategory::all(true);

        return (new Response())->setContent(
            View::render('events.index', [
                'events' => $events,
                'categories' => $categories,
                'currentCategory' => $categorySlug,
                'searchQuery' => $request->get('q', ''),
                'currentSort' => $filters['sort'],
                'page' => $page,
                'totalPages' => $totalPages,
                'totalEvents' => $totalEvents,
                'layout' => 'layouts.main',
            ])
        );
    }

    public function show(Request $request, string $slug): Response
    {
        $event = Event::findBySlug($slug);
        if (!$event || $event['status'] !== 'published') {
            return (new Response())
                ->setStatusCode(404)
                ->setContent(View::render('errors.404', ['layout' => 'layouts.main']));
        }

        // Active ticket types for booking
        $ticketTypes = TicketType::getByEventId((int)$event['id'], true);

        return (new Response())->setContent(
            View::render('events.detail', [
                'event' => $event,
                'ticketTypes' => $ticketTypes,
                'layout' => 'layouts.main',
            ])
        );
    }

    public function checkout(Request $request, string $slug): Response
    {
        $event = Event::findBySlug($slug);
        if (!$event || $event['status'] !== 'published') {
            return (new Response())
                ->setStatusCode(404)
                ->setContent(View::render('errors.404', ['layout' => 'layouts.main']));
        }

        // Ticket selections from query or POST
        $selectedTickets = $request->input('tickets', []);
        $items = [];
        $totalAmount = 0.0;

        foreach ($selectedTickets as $ticketTypeId => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) {
                continue;
            }

            $ticketType = TicketType::find((int)$ticketTypeId);
            if (!$ticketType || (int)$ticketType['event_id'] !== (int)$event['id']) {
                continue;
            }

            if ($ticketType['status'] !== 'active' || (int)$ticketType['remaining_quantity'] < $qty) {
                flash('error', "Sorry, '{$ticketType['name']}' only has {$ticketType['remaining_quantity']} tickets remaining.");
                redirect("/events/{$event['slug']}");
            }

            $maxPerUser = (int)$ticketType['max_per_user'];
            if ($qty > $maxPerUser) {
                flash('error', "Maximum limit for '{$ticketType['name']}' is {$maxPerUser} tickets per order.");
                redirect("/events/{$event['slug']}");
            }

            $subtotal = (float)$ticketType['price'] * $qty;
            $totalAmount += $subtotal;

            $items[] = [
                'ticket_type' => $ticketType,
                'quantity' => $qty,
                'unit_price' => (float)$ticketType['price'],
                'subtotal' => $subtotal,
            ];
        }

        if (empty($items)) {
            flash('error', 'Please select at least one ticket to proceed to checkout.');
            redirect("/events/{$event['slug']}");
        }

        return (new Response())->setContent(
            View::render('events.checkout', [
                'event' => $event,
                'items' => $items,
                'totalAmount' => $totalAmount,
                'customer' => Auth::customer(),
                'layout' => 'layouts.main',
            ])
        );
    }
}
