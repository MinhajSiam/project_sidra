<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Ticket;
use App\Models\Setting;

class TicketPdfService {
    /**
     * Renders a dedicated standalone printable HTML5 ticket pass document.
     */
    public static function renderPrintableTicket(array $ticket): string {
        $qrSvg = QrCodeService::generateSvg(url("verify/{$ticket['verification_token']}"));
        $terms = Setting::get('ticket_terms', "1. Present this QR code at the gate entrance.\n2. Non-transferable.\n3. Admission subject to venue rules.");
        $currencySymbol = config('app.locale.currency_symbol', '৳');

        ob_start();
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - <?= e($ticket['ticket_code']) ?> - <?= e($ticket['event_title']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            min-height: 100vh;
        }
        .action-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: #4f46e5;
            color: #ffffff;
        }
        .btn-primary:hover { background-color: #4338ca; }
        .btn-secondary {
            background-color: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-secondary:hover { background-color: #f8fafc; }

        /* Master Ticket Pass Layout */
        .ticket-card {
            width: 100%;
            max-width: 820px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.1);
            overflow: hidden;
            border: 1px solid #e2e8f0;
            display: flex;
            position: relative;
        }

        .ticket-main {
            flex: 1;
            padding: 36px;
            border-right: 2px dashed #cbd5e1;
            position: relative;
        }

        .ticket-stub {
            width: 270px;
            background: #f8fafc;
            padding: 32px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }

        /* Notches */
        .ticket-main::before, .ticket-main::after {
            content: '';
            position: absolute;
            right: -13px;
            width: 24px;
            height: 24px;
            background-color: #f1f5f9;
            border-radius: 50%;
            z-index: 10;
        }
        .ticket-main::before { top: -12px; }
        .ticket-main::after { bottom: -12px; }

        .brand-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .brand-logo {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .brand-dot {
            width: 10px;
            height: 10px;
            background: #4f46e5;
            border-radius: 50%;
        }
        .tier-badge {
            background: #eef2ff;
            color: #4338ca;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #c7d2fe;
        }

        .event-title {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
            margin-bottom: 20px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid #f1f5f9;
        }
        .meta-item .label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .meta-item .value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }

        .attendee-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .attendee-name {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }
        .attendee-phone {
            font-size: 12px;
            color: #64748b;
        }
        .booking-ref {
            font-family: monospace;
            font-size: 13px;
            font-weight: 700;
            color: #4f46e5;
            background: #eef2ff;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .qr-container {
            width: 170px;
            height: 170px;
            background: #ffffff;
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 16px;
        }
        .qr-container svg {
            width: 100%;
            height: 100%;
            display: block;
        }
        .ticket-code {
            font-family: monospace;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .security-hash {
            font-size: 9px;
            color: #94a3b8;
            word-break: break-all;
            margin-bottom: 14px;
            font-family: monospace;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 9999px;
            text-transform: uppercase;
        }
        .status-valid { background: #dcfce7; color: #15803d; }
        .status-used { background: #fee2e2; color: #b91c1c; }

        .terms-section {
            margin-top: 18px;
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .action-bar { display: none; }
            .ticket-card {
                box-shadow: none;
                border: 1px solid #94a3b8;
                page-break-inside: avoid;
            }
        }

        @media (max-width: 640px) {
            .ticket-card {
                flex-direction: column;
            }
            .ticket-main {
                border-right: none;
                border-bottom: 2px dashed #cbd5e1;
                padding: 24px;
            }
            .ticket-main::before, .ticket-main::after {
                display: none;
            }
            .ticket-stub {
                width: 100%;
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="action-bar">
        <button class="btn btn-primary" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print / Save as PDF
        </button>
        <?php if (\App\Core\Auth::customerCheck()): ?>
        <a href="<?= url('customer/tickets') ?>" class="btn btn-secondary">
            Back to My Tickets
        </a>
        <?php else: ?>
        <a href="<?= url("tickets/{$ticket['ticket_code']}") ?>" class="btn btn-secondary">
            Back to Digital Pass
        </a>
        <?php endif; ?>
    </div>

    <div class="ticket-card">
        <!-- Main Ticket Body -->
        <div class="ticket-main">
            <div class="brand-row">
                <div class="brand-logo">
                    <span class="brand-dot"></span>
                    SIDRA
                </div>
                <span class="tier-badge"><?= e($ticket['ticket_type_name']) ?></span>
            </div>

            <h1 class="event-title"><?= e($ticket['event_title']) ?></h1>

            <div class="meta-grid">
                <div class="meta-item">
                    <div class="label">Event Date</div>
                    <div class="value"><?= format_date($ticket['event_date'], 'l, d F Y') ?></div>
                </div>
                <div class="meta-item">
                    <div class="label">Event Time</div>
                    <div class="value"><?= format_time($ticket['start_time']) ?> - <?= format_time($ticket['end_time']) ?></div>
                </div>
                <div class="meta-item">
                    <div class="label">Venue & Location</div>
                    <div class="value"><?= e($ticket['venue_name']) ?></div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;"><?= e($ticket['venue_address']) ?>, <?= e($ticket['venue_city']) ?></div>
                </div>
                <div class="meta-item">
                    <div class="label">Ticket Price</div>
                    <div class="value"><?= $currencySymbol ?> <?= number_format((float)$ticket['price'], 2) ?></div>
                </div>
            </div>

            <div class="attendee-box">
                <div>
                    <div class="attendee-name"><?= e($ticket['attendee_name']) ?></div>
                    <div class="attendee-phone"><?= e($ticket['attendee_phone'] ?: 'N/A') ?></div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Booking Ref</div>
                    <div class="booking-ref"><?= e($ticket['booking_reference']) ?></div>
                </div>
            </div>

            <div class="terms-section">
                <strong>Notice:</strong> <?= nl2br(e($terms)) ?>
            </div>
        </div>

        <!-- Ticket Stub with Secure QR -->
        <div class="ticket-stub">
            <div class="qr-container">
                <?= $qrSvg ?>
            </div>

            <div class="ticket-code"><?= e($ticket['ticket_code']) ?></div>
            <div class="security-hash">SEC-TOKEN: <?= substr($ticket['verification_token'], 0, 16) ?>...</div>

            <?php if ($ticket['status'] === 'valid'): ?>
                <span class="status-pill status-valid">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    Valid for Entry
                </span>
            <?php else: ?>
                <span class="status-pill status-used">
                    Used / Checked-in
                </span>
            <?php endif; ?>

            <div style="margin-top: 20px; font-size: 10px; color: #94a3b8; line-height: 1.4;">
                Scan QR with Gate Staff camera for admission verification.
            </div>
        </div>
    </div>

    <script>
        // Auto print if requested via query param
        if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
            window.addEventListener('load', () => window.print());
        }
    </script>
</body>
</html>
        <?php
        return ob_get_clean();
    }
}
