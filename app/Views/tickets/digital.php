<?php
$qrSvg = \App\Services\QrCodeService::generateSvg(url("verify/{$ticket['verification_token']}"));
?>
<div class="py-12 max-w-lg mx-auto px-4 sm:px-6 space-y-6">
    <div class="flex items-center justify-between">
        <?php if (\App\Core\Auth::customer()): ?>
        <a href="<?= url('customer/tickets') ?>" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white font-semibold">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Tickets
        </a>
        <?php else: ?>
        <a href="<?= url('events') ?>" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white font-semibold">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Browse Events
        </a>
        <?php endif; ?>
        <a href="<?= url("tickets/{$ticket['ticket_code']}/print") ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white transition-colors">
            <i data-lucide="printer" class="w-4 h-4"></i> Print Pass
        </a>
    </div>

    <!-- Digital Ticket Card -->
    <div class="glass-card rounded-3xl overflow-hidden shadow-2xl border border-brand-500/30">
        <!-- Event Header Banner -->
        <div class="p-6 bg-gradient-to-r from-brand-900 via-dark-800 to-dark-900 border-b border-slate-800">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-extrabold uppercase tracking-widest text-brand-400">SIDRA PASS</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase <?= $ticket['status'] === 'valid' ? 'badge-valid' : 'badge-used' ?>">
                    <?= $ticket['status'] === 'valid' ? 'Valid Admission' : 'Used' ?>
                </span>
            </div>
            <h2 class="font-heading font-extrabold text-2xl text-white leading-tight"><?= e($ticket['event_title']) ?></h2>
            <div class="text-xs text-slate-300 font-semibold mt-1"><?= e($ticket['ticket_type_name']) ?> Pass</div>
        </div>

        <!-- QR Code Center Container -->
        <div class="p-8 flex flex-col items-center justify-center bg-white text-dark-900">
            <div class="w-56 h-56 p-2 rounded-2xl bg-white shadow-inner flex items-center justify-center">
                <?= $qrSvg ?>
            </div>
            <div class="font-mono font-extrabold text-lg tracking-widest text-slate-900 mt-4"><?= e($ticket['ticket_code']) ?></div>
            <div class="text-[10px] text-slate-500 font-mono tracking-tight mt-0.5">SEC-TOKEN: <?= substr($ticket['verification_token'], 0, 16) ?>...</div>
        </div>

        <!-- Ticket Body Details -->
        <div class="p-6 bg-dark-800 space-y-4 border-t-2 border-dashed border-slate-700">
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <div class="text-slate-400 uppercase font-semibold text-[10px]">Attendee</div>
                    <div class="font-bold text-white text-sm mt-0.5"><?= e($ticket['attendee_name']) ?></div>
                </div>
                <div>
                    <div class="text-slate-400 uppercase font-semibold text-[10px]">Booking Ref</div>
                    <div class="font-mono font-bold text-brand-400 text-sm mt-0.5"><?= e($ticket['booking_reference']) ?></div>
                </div>
                <div>
                    <div class="text-slate-400 uppercase font-semibold text-[10px]">Date & Time</div>
                    <div class="font-bold text-white mt-0.5"><?= format_date($ticket['event_date']) ?></div>
                    <div class="text-slate-400"><?= format_time($ticket['start_time']) ?></div>
                </div>
                <div>
                    <div class="text-slate-400 uppercase font-semibold text-[10px]">Venue</div>
                    <div class="font-bold text-white mt-0.5 truncate"><?= e($ticket['venue_name']) ?></div>
                    <div class="text-slate-400 truncate"><?= e($ticket['venue_city']) ?></div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 text-center">
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Present this encrypted QR code at the entrance turnstile. Gate staff will scan and record your admission.
                </p>
            </div>
        </div>
    </div>
</div>
