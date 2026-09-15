<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Payment;

class PaymentController {
    public function index(Request $request): Response {
        $status = $request->get('status', 'pending');
        $filters = [
            'status' => $status,
            'payment_method' => $request->get('method'),
            'search' => $request->get('search'),
        ];

        $payments = Payment::all($filters);

        return (new Response())->setContent(
            View::render('admin.payments.index', [
                'payments' => $payments,
                'currentStatus' => $status,
                'filters' => $filters,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function approve(Request $request, string $id): Response {
        $paymentId = (int)$id;
        $reviewerId = Auth::id() ?? 1;

        if (Payment::approve($paymentId, $reviewerId)) {
            flash('success', "Payment #{$paymentId} approved! Booking confirmed and digital tickets issued.");
        } else {
            flash('error', "Could not approve payment #{$paymentId}. It may already be processed.");
        }

        redirect($_SERVER['HTTP_REFERER'] ?? '/admin/payments');
    }

    public function reject(Request $request, string $id): Response {
        $paymentId = (int)$id;
        $reviewerId = Auth::id() ?? 1;
        $reason = $request->input('rejection_reason', 'Payment could not be verified with the mobile financial provider.');

        if (Payment::reject($paymentId, $reviewerId, $reason)) {
            flash('warning', "Payment #{$paymentId} was rejected. Ticket inventory has been automatically restored.");
        } else {
            flash('error', "Could not reject payment #{$paymentId}. It may already be processed.");
        }

        redirect($_SERVER['HTTP_REFERER'] ?? '/admin/payments');
    }
}
