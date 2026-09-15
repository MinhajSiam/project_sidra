<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

class ReportController {
    public function index(Request $request): Response {
        // 1. Sales by Event
        $eventSales = Database::fetchAll(
            "SELECT e.id, e.title, e.event_date,
                    COUNT(DISTINCT b.id) as total_bookings,
                    COUNT(t.id) as tickets_sold,
                    COALESCE(SUM(p.amount), 0) as total_revenue
             FROM `events` e
             LEFT JOIN `bookings` b ON e.id = b.event_id AND b.status = 'confirmed'
             LEFT JOIN `payments` p ON b.id = p.booking_id AND p.status = 'approved'
             LEFT JOIN `tickets` t ON b.id = t.booking_id AND t.status IN ('valid', 'used')
             GROUP BY e.id
             ORDER BY total_revenue DESC"
        );

        // 2. Revenue by Payment Method
        $paymentBreakdown = Database::fetchAll(
            "SELECT payment_method, 
                    COUNT(*) as total_count,
                    COALESCE(SUM(amount), 0) as total_amount
             FROM `payments`
             WHERE status = 'approved'
             GROUP BY payment_method"
        );

        // 3. Attendance Rate
        $attendanceData = Database::fetchAll(
            "SELECT e.title,
                    COUNT(t.id) as total_tickets,
                    SUM(CASE WHEN t.status = 'used' THEN 1 ELSE 0 END) as total_checked_in
             FROM `events` e
             JOIN `bookings` b ON e.id = b.event_id AND b.status = 'confirmed'
             JOIN `tickets` t ON b.id = t.booking_id AND t.status IN ('valid', 'used')
             GROUP BY e.id"
        );

        return (new Response())->setContent(
            View::render('admin.reports.index', [
                'eventSales' => $eventSales,
                'paymentBreakdown' => $paymentBreakdown,
                'attendanceData' => $attendanceData,
                'layout' => 'layouts.admin',
            ])
        );
    }

    public function exportCsv(Request $request): Response {
        $type = $request->get('type', 'sales');
        $filename = "sidra_{$type}_report_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM

        if ($type === 'bookings') {
            fputcsv($output, ['Booking Ref', 'Event', 'Customer Name', 'Customer Phone', 'Amount', 'Status', 'Date']);
            $rows = Database::fetchAll(
                "SELECT b.booking_reference, e.title, c.name, c.phone, b.final_amount, b.status, b.created_at 
                 FROM `bookings` b 
                 JOIN `events` e ON b.event_id = e.id 
                 JOIN `customers` c ON b.customer_id = c.id 
                 ORDER BY b.id DESC"
            );
            foreach ($rows as $r) {
                fputcsv($output, [$r['booking_reference'], $r['title'], $r['name'], $r['phone'], $r['final_amount'], $r['status'], $r['created_at']]);
            }
        } elseif ($type === 'attendees') {
            fputcsv($output, ['Ticket Code', 'Event', 'Tier', 'Attendee Name', 'Attendee Email', 'Attendee Phone', 'Status', 'Check-in Time']);
            $rows = Database::fetchAll(
                "SELECT t.ticket_code, e.title as event_title, tt.name as tier_name, 
                        t.attendee_name, t.attendee_email, t.attendee_phone, t.status,
                        (SELECT scanned_at FROM ticket_checkins tc WHERE tc.ticket_id = t.id AND tc.result = 'valid' ORDER BY id DESC LIMIT 1) as checked_in_at
                 FROM `tickets` t
                 JOIN `ticket_types` tt ON t.ticket_type_id = tt.id
                 JOIN `bookings` b ON t.booking_id = b.id
                 JOIN `events` e ON b.event_id = e.id
                 ORDER BY t.id DESC"
            );
            foreach ($rows as $r) {
                fputcsv($output, [$r['ticket_code'], $r['event_title'], $r['tier_name'], $r['attendee_name'], $r['attendee_email'] ?: 'N/A', $r['attendee_phone'] ?: 'N/A', $r['status'], $r['checked_in_at'] ?? 'Not Admitted']);
            }
        } else {
            fputcsv($output, ['Event ID', 'Event Title', 'Event Date', 'Confirmed Bookings', 'Tickets Sold', 'Total Revenue (BDT)']);
            $rows = Database::fetchAll(
                "SELECT e.id, e.title, e.event_date,
                        COUNT(DISTINCT b.id) as total_bookings,
                        COUNT(t.id) as tickets_sold,
                        COALESCE(SUM(p.amount), 0) as total_revenue
                 FROM `events` e
                 LEFT JOIN `bookings` b ON e.id = b.event_id AND b.status = 'confirmed'
                 LEFT JOIN `payments` p ON b.id = p.booking_id AND p.status = 'approved'
                 LEFT JOIN `tickets` t ON b.id = t.booking_id AND t.status IN ('valid', 'used')
                 GROUP BY e.id
                 ORDER BY total_revenue DESC"
            );
            foreach ($rows as $r) {
                fputcsv($output, [$r['id'], $r['title'], $r['event_date'], $r['total_bookings'], $r['tickets_sold'], $r['total_revenue']]);
            }
        }

        fclose($output);
        exit;
    }
}
