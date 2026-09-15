<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Ticket;

class CustomerController {
    public function index(Request $request): Response {
        $customers = Customer::all();

        return (new Response())->setContent(
            View::render('admin.customers.index', [
                'customers' => $customers,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function show(Request $request, string $id): Response {
        $customer = Customer::find((int)$id);
        if (!$customer) {
            flash('error', 'Customer not found.');
            redirect('/admin/customers');
        }

        $bookings = Booking::all(['customer_id' => (int)$id]);
        $tickets = Ticket::getByCustomerId((int)$id);

        return (new Response())->setContent(
            View::render('admin.customers.show', [
                'customer' => $customer,
                'bookings' => $bookings,
                'tickets' => $tickets,
                'layout' => 'layouts.admin',
            ])
        );
    }
}
