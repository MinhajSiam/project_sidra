<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Core/Helpers.php';

// Bootstrap minimal application environment
$_ENV['APP_ENV'] = 'testing';
$_ENV['APP_DEBUG'] = 'true';
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = dirname(__DIR__) . '/storage/database/sidra.sqlite';

use App\Core\Database;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\User;
use App\Models\Customer;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\TicketCheckin;
use App\Services\QrCodeService;

class TestRunner {
    private int $passed = 0;
    private int $failed = 0;
    private array $failures = [];

    public function assert(bool $condition, string $description): void {
        if ($condition) {
            $this->passed++;
            echo "  \033[32m✔ PASS\033[0m: {$description}\n";
        } else {
            $this->failed++;
            $this->failures[] = $description;
            echo "  \033[31m✖ FAIL\033[0m: {$description}\n";
        }
    }

    public function summary(): void {
        echo "\n" . str_repeat('=', 65) . "\n";
        echo "TEST SUITE SUMMARY:\n";
        echo "Total: " . ($this->passed + $this->failed) . " | Passed: \033[32m{$this->passed}\033[0m | Failed: \033[31m{$this->failed}\033[0m\n";
        if (!empty($this->failures)) {
            echo "\nFailed Assertions:\n";
            foreach ($this->failures as $f) {
                echo " - {$f}\n";
            }
            exit(1);
        } else {
            echo "\n\033[32mALL TESTS COMPLETED WITH 100% SUCCESS!\033[0m\n";
            echo str_repeat('=', 65) . "\n";
            exit(0);
        }
    }
}

$t = new TestRunner();

echo "\n" . str_repeat('=', 65) . "\n";
echo "SIDRA EVENT TICKETING PLATFORM — AUTOMATED TEST SUITE\n";
echo str_repeat('=', 65) . "\n\n";

// ============================================================
// 1. Database & Infrastructure
// ============================================================
echo "1. Database & Infrastructure Tests:\n";
$pdo = Database::getConnection();
$t->assert($pdo !== null, "Database connection established successfully");

$tables = Database::fetchAll("SELECT name FROM sqlite_master WHERE type='table'");
$tableNames = array_column($tables, 'name');
$requiredTables = ['users', 'roles', 'customers', 'events', 'venues', 'ticket_types', 'bookings', 'payments', 'tickets', 'audit_logs', 'system_settings'];
foreach ($requiredTables as $rt) {
    $t->assert(in_array($rt, $tableNames), "Table `{$rt}` exists in schema");
}

// ============================================================
// 2. Authentication, Users & Roles (RBAC)
// ============================================================
echo "\n2. Authentication & RBAC Tests:\n";
$admin = Database::fetch("SELECT u.*, r.name as role_name FROM `users` u JOIN `roles` r ON u.role_id = r.id WHERE u.email = 'admin@sidra.test'");
$t->assert($admin !== null, "Super Admin user exists in database");
$t->assert(password_verify('Password123!', $admin['password_hash']), "Super Admin password verified via bcrypt");
$t->assert($admin['role_name'] === 'super_admin', "Super Admin has role `super_admin`");

$financeStaff = Database::fetch("SELECT u.*, r.name as role_name FROM `users` u JOIN `roles` r ON u.role_id = r.id WHERE u.email = 'finance@sidra.test'");
$t->assert($financeStaff !== null && $financeStaff['role_name'] === 'finance_manager', "Finance Manager exists with `finance_manager` role");

$gateStaff = Database::fetch("SELECT u.*, r.name as role_name FROM `users` u JOIN `roles` r ON u.role_id = r.id WHERE u.email = 'gate@sidra.test'");
$t->assert($gateStaff !== null && $gateStaff['role_name'] === 'gate_staff', "Gate staff exists with `gate_staff` role");

// Customer Auth
$customer = Customer::findByEmail('customer@sidra.test');
$t->assert($customer !== null, "Default customer `customer@sidra.test` exists");
$t->assert(password_verify('Password123!', $customer['password_hash']), "Customer password verified via bcrypt");

// ============================================================
// 3. Events & Ticket Tier Management
// ============================================================
echo "\n3. Events & Ticket Tier Tests:\n";
$event = Event::findBySlug('sidra-tech-ai-summit-2026');
$t->assert($event !== null, "Sidra Tech & AI Summit 2026 event exists");
$t->assert($event['status'] === 'published', "Event status is `published`");

$ticketTypes = TicketType::getByEventId((int)$event['id']);
$t->assert(!empty($ticketTypes), "Event has active ticket tiers");

$testTier = $ticketTypes[0];
$initialRemaining = (int)$testTier['remaining_quantity'];
$t->assert($initialRemaining > 0, "Test tier has initial stock available ({$initialRemaining} available)");

// ============================================================
// 4. Booking & Inventory Decrement
// ============================================================
echo "\n4. Booking Creation & Atomic Inventory Tests:\n";
$qtyToBook = 2;
$bookingTotal = (float)$testTier['price'] * $qtyToBook;

// Atomically decrement stock
$decremented = TicketType::decrementStock((int)$testTier['id'], $qtyToBook);
$t->assert($decremented === true, "Ticket tier stock atomically decremented by {$qtyToBook}");

$updatedTier = TicketType::find((int)$testTier['id']);
$t->assert((int)$updatedTier['remaining_quantity'] === ($initialRemaining - $qtyToBook), "Ticket tier stock correctly reflected in database");

$bookingId = Booking::create([
    'customer_id' => $customer['id'],
    'event_id' => $event['id'],
    'total_amount' => $bookingTotal,
    'discount_amount' => 0.00,
    'final_amount' => $bookingTotal,
    'status' => 'pending_payment',
], [
    [
        'ticket_type_id' => $testTier['id'],
        'quantity' => $qtyToBook,
        'unit_price' => $testTier['price'],
        'subtotal' => $bookingTotal,
    ]
]);

$t->assert($bookingId > 0, "Booking created with ID #{$bookingId}");
$freshBooking = Booking::find($bookingId);
$t->assert(!empty($freshBooking['booking_reference']), "Booking reference generated: {$freshBooking['booking_reference']}");
$t->assert($freshBooking['status'] === 'pending_payment', "Booking initialized in `pending_payment` status");

// ============================================================
// 5. Overselling Prevention Test
// ============================================================
echo "\n5. Overselling Prevention Tests:\n";
$availNow = (int)$updatedTier['remaining_quantity'];
$oversellResult = TicketType::decrementStock((int)$testTier['id'], $availNow + 50);
$t->assert($oversellResult === false, "Attempt to oversell beyond remaining quantity returned FALSE and was blocked");

// ============================================================
// 6. Payment Submission & Duplicate TrxID Prevention
// ============================================================
echo "\n6. Manual Payment Submission & Duplicate TrxID Tests:\n";
$testTrxId = 'TEST' . strtoupper(bin2hex(random_bytes(4)));

$paymentId = Payment::create([
    'booking_id' => $bookingId,
    'customer_id' => $customer['id'],
    'payment_method' => 'bkash',
    'transaction_id' => $testTrxId,
    'sender_number' => '01711998877',
    'amount' => $bookingTotal,
    'currency' => 'BDT',
    'proof_screenshot' => null,
    'status' => 'pending',
]);

$t->assert($paymentId > 0, "bKash manual payment submitted with TrxID `{$testTrxId}`");

$freshBookingAfterPayment = Booking::find($bookingId);
$t->assert($freshBookingAfterPayment['status'] === 'payment_submitted', "Booking automatically transitioned to `payment_submitted`");

// Duplicate check
$isDup = Payment::existsTrxId('bkash', $testTrxId);
$t->assert($isDup === true, "TrxID duplicate detector identifies existing `{$testTrxId}`");

$dupBlocked = false;
try {
    Payment::create([
        'booking_id' => $bookingId,
        'customer_id' => $customer['id'],
        'payment_method' => 'bkash',
        'transaction_id' => $testTrxId,
        'sender_number' => '01711998877',
        'amount' => $bookingTotal,
        'currency' => 'BDT',
        'proof_screenshot' => null,
        'status' => 'pending',
    ]);
} catch (\Throwable $e) {
    $dupBlocked = true;
}
$t->assert($dupBlocked === true, "Database unique constraint blocks duplicate transaction ID insert");

// ============================================================
// 7. Finance Payment Approval & Digital Ticket Issuance
// ============================================================
echo "\n7. Payment Approval & Digital Ticket Issuance Tests:\n";
$approved = Payment::approve($paymentId, (int)$admin['id']);
$t->assert($approved === true, "Payment #{$paymentId} approved by admin");

// Booking must be confirmed
$confirmedBooking = Booking::find($bookingId);
$t->assert($confirmedBooking['status'] === 'confirmed', "Booking status transitioned to `confirmed`");

// Digital tickets must be generated
$issuedTickets = Ticket::getByBookingId($bookingId);
$t->assert(count($issuedTickets) === $qtyToBook, "Exactly {$qtyToBook} digital passes generated for order");

$firstTicket = $issuedTickets[0];
$t->assert(!empty($firstTicket['ticket_code']), "Ticket code issued: {$firstTicket['ticket_code']}");
$t->assert(!empty($firstTicket['verification_token']), "Cryptographic HMAC token attached to ticket");
$t->assert($firstTicket['status'] === 'valid', "Ticket status is `valid`");

// QR Code generation test
$svgQr = QrCodeService::generateSvg($firstTicket['verification_token']);
$t->assert(!empty($svgQr) && str_contains($svgQr, '<svg'), "Vector SVG QR code generated with valid XML markup");

// ============================================================
// 8. Gate Check-in & Duplicate Scan Prevention Tests
// ============================================================
echo "\n8. Gate Check-in & Duplicate Entry Prevention Tests:\n";
$checkin1 = Ticket::processCheckin($firstTicket['verification_token'], (int)$gateStaff['id'], 'North Gate Lane 1');
$t->assert($checkin1['success'] === true, "First scan of pass admitted successfully");
$t->assert($checkin1['status'] === 'valid', "Check-in response status is `valid`");

// Immediate 2nd scan of same pass
$checkin2 = Ticket::processCheckin($firstTicket['verification_token'], (int)$gateStaff['id'], 'North Gate Lane 1');
$t->assert($checkin2['success'] === false, "Second scan of same pass blocked as duplicate");
$t->assert($checkin2['status'] === 'duplicate', "Check-in response status indicates `duplicate`");
$t->assert(!empty($checkin2['first_checkin']), "Check-in response provides previous admission record");

// Invalid token scan
$checkinInvalid = Ticket::processCheckin('INVALID_NONEXISTENT_TOKEN', (int)$gateStaff['id'], 'Gate 1');
$t->assert($checkinInvalid['success'] === false && $checkinInvalid['status'] === 'invalid', "Invalid/counterfeit token rejected immediately with `invalid`");

// ============================================================
// 9. Payment Rejection & Inventory Restoration Tests
// ============================================================
echo "\n9. Payment Rejection & Inventory Restoration Tests:\n";
$tierBeforeRejection = TicketType::find((int)$testTier['id']);
$stockBeforeBooking = (int)$tierBeforeRejection['remaining_quantity'];

// Reserve 1 ticket
TicketType::decrementStock((int)$testTier['id'], 1);

$rejectBookingId = Booking::create([
    'customer_id' => $customer['id'],
    'event_id' => $event['id'],
    'total_amount' => (float)$testTier['price'],
    'discount_amount' => 0.00,
    'final_amount' => (float)$testTier['price'],
    'status' => 'pending_payment',
], [
    [
        'ticket_type_id' => $testTier['id'],
        'quantity' => 1,
        'unit_price' => $testTier['price'],
        'subtotal' => (float)$testTier['price'],
    ]
]);

$tierAfterBooking = TicketType::find((int)$testTier['id']);
$t->assert((int)$tierAfterBooking['remaining_quantity'] === ($stockBeforeBooking - 1), "Stock temporarily reserved for second booking");

$rejectTrxId = 'REJ' . strtoupper(bin2hex(random_bytes(4)));
$rejectPaymentId = Payment::create([
    'booking_id' => $rejectBookingId,
    'customer_id' => $customer['id'],
    'payment_method' => 'nagad',
    'transaction_id' => $rejectTrxId,
    'sender_number' => '01811998877',
    'amount' => (float)$testTier['price'],
    'currency' => 'BDT',
    'proof_screenshot' => null,
    'status' => 'pending',
]);

// Reject payment
$rejected = Payment::reject($rejectPaymentId, (int)$admin['id'], 'TrxID not matching Nagad merchant statement');
$t->assert($rejected === true, "Payment #{$rejectPaymentId} rejected");

$rejectedBooking = Booking::find($rejectBookingId);
$t->assert($rejectedBooking['status'] === 'cancelled', "Booking status transitioned to `cancelled`");

// Check stock restored
$tierAfterRejection = TicketType::find((int)$testTier['id']);
$t->assert((int)$tierAfterRejection['remaining_quantity'] === $stockBeforeBooking, "Ticket inventory atomically restored to original balance upon rejection");

// ============================================================
// 10. Security Utilities & CSRF Tests
// ============================================================
echo "\n10. Security Utilities & CSRF Tests:\n";
$csrfToken = Csrf::token();
$t->assert(!empty($csrfToken) && strlen($csrfToken) === 64, "CSRF token generated with 64-char cryptographic hex");
$t->assert(Csrf::validate($csrfToken) === true, "CSRF token successfully verified");
$t->assert(Csrf::validate('tampered_token') === false, "Tampered CSRF token rejected");

$escaped = e("<script>alert('xss')</script>");
$t->assert($escaped === '&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;', "HTML escaping helper neutralizes XSS payloads");

// ============================================================
// 11. Public Digital Ticket & Printable Pass Rendering
// ============================================================
echo "\n11. Digital Ticket & Printable Pass Tests:\n";
$sampleTicket = Ticket::findByCode($firstTicket['ticket_code']);
$t->assert($sampleTicket !== null, "Ticket retrieved by code `{$firstTicket['ticket_code']}`");
$t->assert(!empty($sampleTicket['attendee_name']), "Ticket attendee name correctly resolved: `{$sampleTicket['attendee_name']}`");

$sampleByToken = Ticket::findByToken($sampleTicket['verification_token']);
$t->assert($sampleByToken !== null && (int)$sampleByToken['id'] === (int)$sampleTicket['id'], "Ticket retrieved by verification token");

$printableHtml = \App\Services\TicketPdfService::renderPrintableTicket($sampleTicket);
$t->assert(str_contains($printableHtml, $sampleTicket['ticket_code']), "Printable pass HTML includes ticket code");
$t->assert(str_contains($printableHtml, '<svg') && str_contains($printableHtml, '</svg>'), "Printable pass HTML embeds valid inline SVG QR code");
$t->assert(str_contains($printableHtml, e($sampleTicket['event_title'])), "Printable pass HTML includes event title");

// ============================================================
// 12. Attendee Manifest & Reporting Queries
// ============================================================
echo "\n12. Attendee Manifest & Reporting Tests:\n";
$attendeeRows = Database::fetchAll(
    "SELECT t.ticket_code, e.title as event_title, tt.name as tier_name, 
            t.attendee_name, t.attendee_email, t.attendee_phone, t.status,
            (SELECT scanned_at FROM ticket_checkins tc WHERE tc.ticket_id = t.id AND tc.result = 'valid' ORDER BY id DESC LIMIT 1) as checked_in_at
     FROM tickets t
     JOIN ticket_types tt ON t.ticket_type_id = tt.id
     JOIN bookings b ON t.booking_id = b.id
     JOIN events e ON b.event_id = e.id
     ORDER BY t.id DESC"
);
$t->assert(!empty($attendeeRows), "Attendee manifest records retrieved from joined database tables");
$firstAttendee = $attendeeRows[0];
$t->assert(isset($firstAttendee['ticket_code'], $firstAttendee['attendee_name'], $firstAttendee['event_title'], $firstAttendee['tier_name']), "Attendee record contains all required export columns");

// ============================================================
// 13. Cross-Database Password Reset Token & Expiration
// ============================================================
echo "\n13. Cross-Database Password Reset Token Tests:\n";
$testResetToken = bin2hex(random_bytes(24));
$validExpiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour in future
$expiredExpiresAt = date('Y-m-d H:i:s', time() - 3600); // 1 hour in past

// Insert valid token
Database::query(
    "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)",
    [':email' => 'customer@sidra.test', ':token' => $testResetToken, ':expires_at' => $validExpiresAt]
);

$now = date('Y-m-d H:i:s');
$validReset = Database::fetch(
    "SELECT * FROM password_resets WHERE token = :token AND expires_at > :now LIMIT 1",
    [':token' => $testResetToken, ':now' => $now]
);
$t->assert($validReset !== null, "Active password reset token located using cross-db `:now` parameter");

// Insert expired token
$expiredResetToken = bin2hex(random_bytes(24));
Database::query(
    "INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)",
    [':email' => 'customer@sidra.test', ':token' => $expiredResetToken, ':expires_at' => $expiredExpiresAt]
);

$expiredReset = Database::fetch(
    "SELECT * FROM password_resets WHERE token = :token AND expires_at > :now LIMIT 1",
    [':token' => $expiredResetToken, ':now' => $now]
);
$t->assert($expiredReset === null, "Expired password reset token rejected using cross-db `:now` parameter");

// ============================================================
// 14. Admin Models & System Settings Tests
// ============================================================
echo "\n14. Admin Models & System Settings Tests:\n";
$catName = 'Cybersecurity Expo ' . rand(100, 999);
$catId = \App\Models\EventCategory::create([
    'name' => $catName,
    'description' => 'Cybersecurity conventions and ethical hacking.',
    'icon' => 'shield',
    'is_active' => 1
]);
$t->assert($catId > 0, "Event category created successfully with ID #{$catId}");
$createdCat = \App\Models\EventCategory::find($catId);
$t->assert($createdCat['name'] === $catName && !empty($createdCat['slug']), "Event category slug generated: {$createdCat['slug']}");

\App\Models\EventCategory::update($catId, [
    'name' => $catName . ' Updated',
    'description' => 'Updated description.',
    'icon' => 'lock',
    'is_active' => 1
]);
$updatedCat = \App\Models\EventCategory::find($catId);
$t->assert(str_contains($updatedCat['name'], 'Updated'), "Event category updated successfully");

// Venue creation test
$venueName = 'Test Grand Convention Center ' . rand(100, 999);
$venueId = \App\Models\Venue::create([
    'name' => $venueName,
    'address' => 'Plot 45, Gulshan-2, Dhaka',
    'city' => 'Dhaka',
    'capacity' => 2500,
    'contact_phone' => '+880 1700 123456'
]);
$t->assert($venueId > 0, "Venue created successfully with ID #{$venueId}");
$createdVenue = \App\Models\Venue::find($venueId);
$t->assert($createdVenue['capacity'] == 2500, "Venue capacity correctly stored as 2500");

// Settings get & set test
\App\Models\Setting::set('test_platform_key', 'SidraTestValue123');
$retrievedSetting = \App\Models\Setting::get('test_platform_key');
$t->assert($retrievedSetting === 'SidraTestValue123', "Platform setting correctly updated and retrieved: {$retrievedSetting}");

// Cleanup test records
Database::query("DELETE FROM event_categories WHERE id = :id", [':id' => $catId]);
Database::query("DELETE FROM venues WHERE id = :id", [':id' => $venueId]);
Database::query("DELETE FROM system_settings WHERE setting_key = 'test_platform_key'");

$t->summary();
