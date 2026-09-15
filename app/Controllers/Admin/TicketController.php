<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketType;

class TicketController {
    public function index(Request $request): Response {
        $filters = [
            'status' => $request->get('status'),
            'event_id' => $request->get('event_id'),
            'search' => $request->get('search'),
        ];

        $tickets = Ticket::all($filters);
        $events = Event::all();

        return (new Response())->setContent(
            View::render('admin.tickets.index', [
                'tickets' => $tickets,
                'events' => $events,
                'filters' => $filters,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function storeType(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|integer',
            'name' => 'required|min:2|max:100',
            'price' => 'required|numeric|min:0',
            'total_quantity' => 'required|integer|min:1',
            'max_per_user' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect($_SERVER['HTTP_REFERER'] ?? '/admin/events');
        }

        $eventId = (int)$request->input('event_id');
        TicketType::create([
            'event_id' => $eventId,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => (float)$request->input('price'),
            'total_quantity' => (int)$request->input('total_quantity'),
            'max_per_user' => (int)$request->input('max_per_user'),
            'sales_start' => $request->input('sales_start'),
            'sales_end' => $request->input('sales_end'),
            'status' => $request->input('status', 'active'),
        ]);

        AuditLog::record(Auth::id(), 'ticket_type.created', 'ticket_type', null, null, ['event_id' => $eventId, 'name' => $request->input('name')]);
        flash('success', 'Ticket tier added successfully.');
        redirect($_SERVER['HTTP_REFERER'] ?? "/admin/events/{$eventId}/edit");
    }

    public function updateType(Request $request, string $id): Response {
        $ticketTypeId = (int)$id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100',
            'price' => 'required|numeric|min:0',
            'total_quantity' => 'required|integer|min:1',
            'max_per_user' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect($_SERVER['HTTP_REFERER'] ?? '/admin/events');
        }

        TicketType::update($ticketTypeId, [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => (float)$request->input('price'),
            'total_quantity' => (int)$request->input('total_quantity'),
            'max_per_user' => (int)$request->input('max_per_user'),
            'sales_start' => $request->input('sales_start'),
            'sales_end' => $request->input('sales_end'),
            'status' => $request->input('status', 'active'),
        ]);

        AuditLog::record(Auth::id(), 'ticket_type.updated', 'ticket_type', $ticketTypeId);
        flash('success', 'Ticket tier updated successfully.');
        redirect($_SERVER['HTTP_REFERER'] ?? '/admin/events');
    }
}
