<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Core\View;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\TicketType;

class BookingController {
    public function store(Request $request): Response {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|integer',
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'phone' => 'required|phone',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $eventId = (int)$request->input('event_id');
        $event = Event::find($eventId);
        if (!$event || $event['status'] !== 'published') {
            flash('error', 'The requested event is not available for booking.');
            redirect('/events');
        }

        $ticketsData = $request->input('tickets', []);
        if (empty($ticketsData) || !is_array($ticketsData)) {
            flash('error', 'No tickets were selected.');
            redirect("/events/{$event['slug']}");
        }

        // 1. Authenticate or find/create customer
        $customer = Auth::customer();
        if (!$customer) {
            $email = strtolower(trim($request->input('email')));
            $phone = trim($request->input('phone'));
            $name = trim($request->input('name'));

            $existing = Database::fetch("SELECT * FROM `customers` WHERE email = :email OR phone = :phone LIMIT 1", [
                'email' => $email,
                'phone' => $phone,
            ]);

            if ($existing) {
                $customerId = (int)$existing['id'];
            } else {
                $customerId = Customer::create([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => bin2hex(random_bytes(6)), // Random temp password
                ]);
            }
            // Auto-login newly registered/found customer for this checkout session
            Auth::attemptCustomer($email, 'dummy'); // Or manual session set
            \App\Core\Session::set('customer_id', $customerId);
        } else {
            $customerId = (int)$customer['id'];
        }

        // 2. Process Booking Inside Database Transaction
        try {
            $bookingId = Database::transaction(function () use ($eventId, $customerId, $ticketsData) {
                $items = [];
                $totalAmount = 0.0;

                foreach ($ticketsData as $ticketTypeId => $qty) {
                    $qty = (int)$qty;
                    if ($qty <= 0) {
                        continue;
                    }

                    $ticketType = TicketType::find((int)$ticketTypeId);
                    if (!$ticketType || (int)$ticketType['event_id'] !== $eventId) {
                        throw new \RuntimeException("Invalid ticket tier requested.");
                    }

                    // Atomic decrement - ensures race conditions can NEVER oversell
                    $decremented = TicketType::decrementStock((int)$ticketType['id'], $qty);
                    if (!$decremented) {
                        throw new \RuntimeException("Sold out: Not enough tickets available for '{$ticketType['name']}'.");
                    }

                    $subtotal = (float)$ticketType['price'] * $qty;
                    $totalAmount += $subtotal;

                    $items[] = [
                        'ticket_type_id' => (int)$ticketType['id'],
                        'quantity' => $qty,
                        'unit_price' => (float)$ticketType['price'],
                        'subtotal' => $subtotal,
                    ];
                }

                if (empty($items)) {
                    throw new \RuntimeException("No valid ticket quantities selected.");
                }

                return Booking::create([
                    'customer_id' => $customerId,
                    'event_id' => $eventId,
                    'total_amount' => $totalAmount,
                    'final_amount' => $totalAmount,
                    'status' => 'pending_payment',
                ], $items);
            });

            $booking = Booking::find($bookingId);
            flash('success', 'Ticket reservation created! Please submit your payment to receive your digital tickets.');
            redirect("/booking/{$booking['booking_reference']}/payment");
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
            redirect("/events/{$event['slug']}");
        }
    }

    public function payment(Request $request, string $reference): Response {
        $booking = Booking::findByReference($reference);
        if (!$booking) {
            return (new Response())
                ->setStatusCode(404)
                ->setContent(View::render('errors.404', ['layout' => 'layouts.main']));
        }

        // If booking is already confirmed, redirect to digital tickets
        if ($booking['status'] === 'confirmed') {
            redirect("/booking/{$booking['booking_reference']}/confirmation");
        }

        $paymentMethods = config('payment_methods', []);
        
        // Dynamically override phone numbers from system settings if configured
        foreach ($paymentMethods as $key => &$method) {
            $configuredNumber = Setting::get("{$key}_number");
            $configuredType = Setting::get("{$key}_type");
            $configuredInstructions = Setting::get("{$key}_instructions");

            if ($configuredNumber) {
                $method['default_number'] = $configuredNumber;
            }
            if ($configuredType) {
                $method['default_type'] = $configuredType;
            }
            if ($configuredInstructions) {
                $method['instructions'] = $configuredInstructions;
            }
        }

        return (new Response())->setContent(
            View::render('events.payment', [
                'booking' => $booking,
                'paymentMethods' => $paymentMethods,
                'layout' => 'layouts.main',
            ])
        );
    }

    public function submitPayment(Request $request, string $reference): Response {
        $booking = Booking::findByReference($reference);
        if (!$booking) {
            return (new Response())
                ->setStatusCode(404)
                ->setContent(View::render('errors.404', ['layout' => 'layouts.main']));
        }

        if ($booking['status'] === 'confirmed') {
            flash('info', 'This booking has already been paid and confirmed.');
            redirect("/booking/{$booking['booking_reference']}/confirmation");
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:bkash,nagad,rocket',
            'sender_number' => 'required|phone',
            'transaction_id' => 'required|min:6|max:64',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->firstError());
            redirect("/booking/{$booking['booking_reference']}/payment");
        }

        $method = strtolower(trim($request->input('payment_method')));
        $trxId = strtoupper(trim($request->input('transaction_id')));

        // Duplicate TrxID verification across all payments
        if (Payment::existsTrxId($method, $trxId)) {
            flash('error', "Transaction ID '{$trxId}' has already been submitted for another payment. Please verify your transaction ID.");
            redirect("/booking/{$booking['booking_reference']}/payment");
        }

        // Optional screenshot proof upload
        $proofPath = null;
        try {
            $proofFilename = $request->saveUploadedFile('proof_image', dirname(__DIR__, 2) . '/public/uploads/payments');
            if ($proofFilename) {
                $proofPath = '/uploads/payments/' . $proofFilename;
            }
        } catch (\Throwable $e) {
            flash('error', 'Upload error: ' . $e->getMessage());
            redirect("/booking/{$booking['booking_reference']}/payment");
        }

        try {
            Payment::submit([
                'booking_id' => (int)$booking['id'],
                'customer_id' => (int)$booking['customer_id'],
                'payment_method' => $method,
                'sender_number' => trim($request->input('sender_number')),
                'transaction_id' => $trxId,
                'proof_image' => $proofPath,
                'amount' => (float)$booking['final_amount'],
            ]);

            flash('success', 'Payment submitted successfully! Our finance team is reviewing your transaction.');
            redirect("/booking/{$booking['booking_reference']}/confirmation");
        } catch (\Throwable $e) {
            flash('error', 'Payment submission failed: ' . $e->getMessage());
            redirect("/booking/{$booking['booking_reference']}/payment");
        }
    }

    public function confirmation(Request $request, string $reference): Response {
        $booking = Booking::findByReference($reference);
        if (!$booking) {
            return (new Response())
                ->setStatusCode(404)
                ->setContent(View::render('errors.404', ['layout' => 'layouts.main']));
        }

        return (new Response())->setContent(
            View::render('events.confirmation', [
                'booking' => $booking,
                'layout' => 'layouts.main',
            ])
        );
    }
}
