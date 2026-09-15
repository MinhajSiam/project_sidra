<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Ticket;
use App\Services\TicketPdfService;

class CustomerDashboardController {
    public function dashboard(Request $request): Response {
        $customerId = Auth::customerId();
        $tickets = Ticket::getByCustomerId($customerId);
        $bookings = Booking::all(['customer_id' => $customerId, 'limit' => 5]);

        return (new Response())->setContent(
            View::render('customer.dashboard', [
                'tickets' => $tickets,
                'bookings' => $bookings,
                'customer' => Auth::customer(),
                'layout' => 'layouts.main',
            ])
        );
    }

    public function bookings(Request $request): Response {
        $customerId = Auth::customerId();
        $bookings = Booking::all(['customer_id' => $customerId]);

        return (new Response())->setContent(
            View::render('customer.bookings', [
                'bookings' => $bookings,
                'customer' => Auth::customer(),
                'layout' => 'layouts.main',
            ])
        );
    }

    public function tickets(Request $request): Response {
        $customerId = Auth::customerId();
        $tickets = Ticket::getByCustomerId($customerId);

        return (new Response())->setContent(
            View::render('customer.tickets', [
                'tickets' => $tickets,
                'customer' => Auth::customer(),
                'layout' => 'layouts.main',
            ])
        );
    }

    public function showTicket(Request $request, string $code): Response {
        $ticket = Ticket::findByCode($code) ?? Ticket::findByToken($code);
        $customerId = Auth::customerId();

        // Allow ticket owner OR logged in staff to view
        if (!$ticket || (!Auth::check() && (int)$ticket['customer_id'] !== $customerId)) {
            return (new Response())
                ->setStatusCode(404)
                ->setContent(View::render('errors.404', ['layout' => 'layouts.main']));
        }

        return (new Response())->setContent(
            View::render('tickets.digital', [
                'ticket' => $ticket,
                'layout' => 'layouts.main',
            ])
        );
    }

    public function showTicketPublic(Request $request, string $code): Response {
        $ticket = Ticket::findByCode($code) ?? Ticket::findByToken($code);
        if (!$ticket) {
            return (new Response())
                ->setStatusCode(404)
                ->setContent(View::render('errors.404', ['layout' => 'layouts.main']));
        }

        return (new Response())->setContent(
            View::render('tickets.digital', [
                'ticket' => $ticket,
                'layout' => 'layouts.main',
            ])
        );
    }

    public function printTicket(Request $request, string $code): Response {
        $ticket = Ticket::findByCode($code) ?? Ticket::findByToken($code);
        $customerId = Auth::customerId();

        // Allow ticket owner OR logged in staff to print
        if (!$ticket || (!Auth::check() && (int)$ticket['customer_id'] !== $customerId)) {
            return (new Response())
                ->setStatusCode(404)
                ->setContent(View::render('errors.404', ['layout' => 'layouts.main']));
        }

        $html = TicketPdfService::renderPrintableTicket($ticket);
        return (new Response())->setContent($html);
    }

    public function printTicketPublic(Request $request, string $code): Response {
        $ticket = Ticket::findByCode($code) ?? Ticket::findByToken($code);
        if (!$ticket) {
            return (new Response())
                ->setStatusCode(404)
                ->setContent(View::render('errors.404', ['layout' => 'layouts.main']));
        }

        $html = TicketPdfService::renderPrintableTicket($ticket);
        return (new Response())->setContent($html);
    }

    public function profile(Request $request): Response {
        $customer = Auth::customer();

        if ($request->isPost()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2|max:100',
                'phone' => 'required|phone',
            ]);

            if ($validator->fails()) {
                flash('error', $validator->firstError());
                redirect('/customer/profile');
            }

            $updateData = [
                'name' => trim($request->input('name')),
                'phone' => trim($request->input('phone')),
            ];

            if ($request->input('password')) {
                if (strlen($request->input('password')) < 8) {
                    flash('error', 'New password must be at least 8 characters.');
                    redirect('/customer/profile');
                }
                $updateData['password'] = $request->input('password');
            }

            Customer::updateProfile((int)$customer['id'], $updateData);
            flash('success', 'Profile updated successfully.');
            redirect('/customer/profile');
        }

        return (new Response())->setContent(
            View::render('customer.profile', [
                'customer' => $customer,
                'layout' => 'layouts.main',
            ])
        );
    }
}
