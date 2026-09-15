<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\TicketType;
use App\Models\Venue;

class EventController {
    public function index(Request $request): Response {
        $filters = [
            'status' => $request->get('status'),
            'category_id' => $request->get('category_id'),
            'search' => $request->get('search'),
        ];

        $events = Event::all($filters);
        $categories = EventCategory::all(false);

        return (new Response())->setContent(
            View::render('admin.events.index', [
                'events' => $events,
                'categories' => $categories,
                'filters' => $filters,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function create(Request $request): Response {
        $categories = EventCategory::all(true);
        $venues = Venue::all();

        return (new Response())->setContent(
            View::render('admin.events.create', [
                'categories' => $categories,
                'venues' => $venues,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function store(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:200',
            'category_id' => 'required|integer',
            'venue_id' => 'required|integer',
            'event_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'summary' => 'required|max:300',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect('/admin/events/create');
        }

        $bannerPath = null;
        try {
            $bannerFile = $request->saveUploadedFile('banner_image', dirname(__DIR__, 3) . '/public/uploads/events');
            if ($bannerFile) {
                $bannerPath = '/uploads/events/' . $bannerFile;
            }
        } catch (\Throwable $e) {
            flash('error', 'Image error: ' . $e->getMessage());
            redirect('/admin/events/create');
        }

        try {
            $eventId = Event::create([
                'title' => $request->input('title'),
                'category_id' => (int)$request->input('category_id'),
                'venue_id' => (int)$request->input('venue_id'),
                'created_by' => Auth::id(),
                'summary' => $request->input('summary'),
                'description' => $request->input('description'),
                'banner_image' => $bannerPath,
                'event_date' => $request->input('event_date'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'status' => $request->input('status', 'draft'),
                'is_featured' => $request->input('is_featured') ? 1 : 0,
            ]);

            // Create initial ticket tiers if provided
            $tierNames = $request->input('tier_name', []);
            $tierPrices = $request->input('tier_price', []);
            $tierQuantities = $request->input('tier_quantity', []);
            $tierLimits = $request->input('tier_max_per_user', []);

            if (is_array($tierNames)) {
                foreach ($tierNames as $idx => $name) {
                    if (trim((string)$name) === '') {
                        continue;
                    }
                    TicketType::create([
                        'event_id' => $eventId,
                        'name' => trim((string)$name),
                        'price' => (float)($tierPrices[$idx] ?? 0),
                        'total_quantity' => (int)($tierQuantities[$idx] ?? 100),
                        'max_per_user' => (int)($tierLimits[$idx] ?? 5),
                        'status' => 'active',
                    ]);
                }
            }

            AuditLog::record(Auth::id(), 'event.created', 'event', $eventId, null, ['title' => $request->input('title')]);

            flash('success', 'Event created successfully!');
            redirect('/admin/events');
        } catch (\Throwable $e) {
            flash('error', 'Failed to create event: ' . $e->getMessage());
            redirect('/admin/events/create');
        }
    }

    public function edit(Request $request, string $id): Response {
        $event = Event::find((int)$id);
        if (!$event) {
            flash('error', 'Event not found.');
            redirect('/admin/events');
        }

        $categories = EventCategory::all(false);
        $venues = Venue::all();
        $ticketTypes = TicketType::getByEventId((int)$id);

        return (new Response())->setContent(
            View::render('admin.events.edit', [
                'event' => $event,
                'categories' => $categories,
                'venues' => $venues,
                'ticketTypes' => $ticketTypes,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function update(Request $request, string $id): Response {
        $eventId = (int)$id;
        $event = Event::find($eventId);
        if (!$event) {
            flash('error', 'Event not found.');
            redirect('/admin/events');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:200',
            'category_id' => 'required|integer',
            'venue_id' => 'required|integer',
            'event_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'summary' => 'required|max:300',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect("/admin/events/{$eventId}/edit");
        }

        $updateData = [
            'title' => $request->input('title'),
            'category_id' => (int)$request->input('category_id'),
            'venue_id' => (int)$request->input('venue_id'),
            'summary' => $request->input('summary'),
            'description' => $request->input('description'),
            'event_date' => $request->input('event_date'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'status' => $request->input('status', 'draft'),
            'is_featured' => $request->input('is_featured') ? 1 : 0,
        ];

        try {
            $bannerFile = $request->saveUploadedFile('banner_image', dirname(__DIR__, 3) . '/public/uploads/events');
            if ($bannerFile) {
                $updateData['banner_image'] = '/uploads/events/' . $bannerFile;
            }
        } catch (\Throwable $e) {
            flash('error', 'Image error: ' . $e->getMessage());
            redirect("/admin/events/{$eventId}/edit");
        }

        Event::update($eventId, $updateData);
        AuditLog::record(Auth::id(), 'event.updated', 'event', $eventId, $event, $updateData);

        flash('success', 'Event updated successfully.');
        redirect("/admin/events/{$eventId}/edit");
    }

    public function toggleStatus(Request $request, string $id): Response {
        $eventId = (int)$id;
        $event = Event::find($eventId);
        if ($event) {
            $newStatus = $event['status'] === 'published' ? 'draft' : 'published';
            Database::update('events', ['status' => $newStatus], 'id = :id', ['id' => $eventId]);
            AuditLog::record(Auth::id(), 'event.status_toggled', 'event', $eventId, ['status' => $event['status']], ['status' => $newStatus]);
            flash('success', "Event status changed to '{$newStatus}'.");
        }
        redirect('/admin/events');
    }

    public function delete(Request $request, string $id): Response {
        $eventId = (int)$id;
        // Check if bookings exist
        $bookingsCount = Database::fetch("SELECT COUNT(*) as total FROM `bookings` WHERE event_id = :id", ['id' => $eventId]);
        if ((int)($bookingsCount['total'] ?? 0) > 0) {
            flash('error', 'Cannot delete this event because bookings have already been made. You may unpublish or archive it instead.');
            redirect('/admin/events');
        }

        Event::delete($eventId);
        AuditLog::record(Auth::id(), 'event.deleted', 'event', $eventId);
        flash('success', 'Event deleted successfully.');
        redirect('/admin/events');
    }
}
