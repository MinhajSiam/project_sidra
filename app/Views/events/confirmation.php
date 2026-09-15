<div class="py-12 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <div class="text-center space-y-4">
        <?php if ($booking['status'] === 'confirmed'): ?>
            <div class="w-16 h-16 rounded-3xl bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center mx-auto shadow-2xl">
                <i data-lucide="check" class="w-8 h-8"></i>
            </div>
            <h1 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Booking Confirmed!</h1>
            <p class="text-sm text-slate-400">Your payment has been verified. Your official digital tickets are now ready.</p>
        <?php else: ?>
            <div class="w-16 h-16 rounded-3xl bg-amber-600/20 text-amber-400 border border-amber-500/30 flex items-center justify-center mx-auto shadow-2xl">
                <i data-lucide="clock" class="w-8 h-8"></i>
            </div>
            <h1 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Payment Under Review</h1>
            <p class="text-sm text-slate-400">We have received your transaction reference. Our finance staff is reviewing your payment.</p>
        <?php endif; ?>
    </div>

    <!-- Booking Details Card -->
    <div class="glass-card rounded-3xl p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Booking Reference</div>
                <div class="font-mono text-xl font-bold text-brand-400 mt-0.5"><?= e($booking['booking_reference']) ?></div>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase <?= $booking['status'] === 'confirmed' ? 'badge-confirmed' : 'badge-pending' ?>">
                    <span class="w-2 h-2 rounded-full <?= $booking['status'] === 'confirmed' ? 'bg-emerald-400' : 'bg-amber-400 animate-pulse' ?>"></span>
                    <?= str_replace('_', ' ', $booking['status']) ?>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <div class="text-slate-500 uppercase font-semibold">Event</div>
                <div class="font-bold text-white text-sm mt-0.5"><?= e($booking['event_title']) ?></div>
                <div class="text-slate-400 mt-0.5"><?= format_date($booking['event_date']) ?> • <?= e($booking['venue_name']) ?></div>
            </div>
            <div>
                <div class="text-slate-500 uppercase font-semibold">Attendee</div>
                <div class="font-bold text-white text-sm mt-0.5"><?= e($booking['customer_name']) ?></div>
                <div class="text-slate-400 mt-0.5"><?= e($booking['customer_phone']) ?> • <?= e($booking['customer_email']) ?></div>
            </div>
        </div>

        <!-- Ticket Items Breakdown -->
        <div class="pt-4 border-t border-slate-800 space-y-3">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Reserved Passes</div>
            <?php foreach ($booking['items'] as $item): ?>
                <div class="flex items-center justify-between text-xs py-1">
                    <span class="text-slate-300"><?= e($item['ticket_name']) ?> &times; <?= $item['quantity'] ?></span>
                    <span class="font-bold text-white"><?= format_currency($item['subtotal']) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between">
                <span class="text-xs font-bold text-white uppercase">Total Paid</span>
                <span class="font-heading font-extrabold text-xl text-white"><?= format_currency($booking['final_amount']) ?></span>
            </div>
        </div>

        <!-- If Digital Tickets are already issued -->
        <?php if (!empty($booking['tickets'])): ?>
            <div class="pt-6 border-t border-slate-800 space-y-4">
                <h3 class="font-heading font-bold text-lg text-white">Your Digital Admission Passes</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php foreach ($booking['tickets'] as $ticket): ?>
                        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
                            <div>
                                <div class="font-mono text-xs font-bold text-brand-400"><?= e($ticket['ticket_code']) ?></div>
                                <div class="text-[11px] text-slate-400 mt-0.5"><?= e($ticket['ticket_type_name']) ?></div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="<?= url("customer/tickets/{$ticket['ticket_code']}") ?>" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-brand-400 hover:text-white transition-colors" title="View Pass">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="<?= url("customer/tickets/{$ticket['ticket_code']}/print") ?>" target="_blank" class="p-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white transition-colors" title="Print PDF">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
        <a href="<?= url('customer/dashboard') ?>" class="w-full sm:w-auto px-6 py-3.5 rounded-xl gradient-brand text-white font-bold text-xs shadow-lg text-center">
            Go to Customer Dashboard
        </a>
        <a href="<?= url('events') ?>" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white font-bold text-xs text-center transition-colors">
            Browse More Events
        </a>
    </div>
</div>
