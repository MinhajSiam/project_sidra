<?php $pageTitle = 'Booking ' . e($booking['booking_reference']); ?>

<div class="space-y-6">
    <!-- Top Breadcrumb & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="/admin/bookings" class="p-2 text-slate-400 hover:text-white hover:bg-dark-800 rounded-xl transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl font-bold font-heading text-white font-mono"><?= e($booking['booking_reference']) ?></h1>
                    <?php if ($booking['status'] === 'confirmed'): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Confirmed & Paid
                        </span>
                    <?php elseif ($booking['status'] === 'pending'): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Pending Review
                        </span>
                    <?php elseif ($booking['status'] === 'cancelled'): ?>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i> Cancelled
                        </span>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Placed on <?= date('l, F d, Y \a\t h:i A', strtotime($booking['created_at'])) ?></p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <?php if (!empty($booking['payment']) && $booking['payment']['status'] === 'pending'): ?>
                <form method="POST" action="<?= url("admin/payments/{$booking['payment']['id']}/approve") ?>" onsubmit="return confirm('Approve payment for <?= e($booking['booking_reference']) ?>?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-emerald-600/20">
                        <i data-lucide="check" class="w-4 h-4"></i> Approve Payment
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Order Items & Issued Tickets -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items Breakdown -->
            <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 border-b border-dark-700 pb-3">
                    <i data-lucide="shopping-cart" class="w-4 h-4 text-brand-400"></i>
                    Reserved Ticket Tiers
                </h3>
                <div class="divide-y divide-dark-700/60">
                    <?php foreach ($booking['items'] as $item): ?>
                        <div class="py-3.5 flex items-center justify-between text-xs">
                            <div>
                                <div class="font-bold text-white text-sm"><?= e($item['ticket_name']) ?></div>
                                <div class="text-slate-400 text-[11px]"><?= e($item['ticket_description'] ?: 'General admission tier') ?></div>
                                <div class="text-slate-500 font-mono text-[11px] mt-0.5">
                                    <?= format_currency((float)$item['unit_price']) ?> × <?= $item['quantity'] ?> pass<?= $item['quantity'] > 1 ? 'es' : '' ?>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-mono font-bold text-white text-sm">
                                    <?= format_currency((float)$item['subtotal']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Financial Totals -->
                <div class="border-t border-dark-700 pt-3 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-400">
                        <span>Subtotal</span>
                        <span class="font-mono text-slate-200"><?= format_currency((float)$booking['total_amount']) ?></span>
                    </div>
                    <?php if ((float)$booking['discount_amount'] > 0): ?>
                        <div class="flex justify-between text-emerald-400">
                            <span>Discount</span>
                            <span class="font-mono">-<?= format_currency((float)$booking['discount_amount']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex justify-between text-base font-bold text-white border-t border-dark-700/80 pt-2">
                        <span>Final Total</span>
                        <span class="font-mono text-emerald-400"><?= format_currency((float)$booking['final_amount']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Issued Digital Tickets -->
            <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm space-y-4">
                <div class="flex items-center justify-between border-b border-dark-700 pb-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                        <i data-lucide="qr-code" class="w-4 h-4 text-brand-400"></i>
                        Issued Gate Passes (<?= count($booking['tickets'] ?? []) ?>)
                    </h3>
                </div>

                <?php if (empty($booking['tickets'])): ?>
                    <div class="text-center py-8 text-slate-500 text-xs">
                        <i data-lucide="ticket" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                        No digital passes issued yet. Passes generate automatically upon payment approval.
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-dark-700/60">
                        <?php foreach ($booking['tickets'] as $t): ?>
                            <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-white bg-dark-900 px-2 py-0.5 rounded border border-dark-600 select-all">
                                            <?= e($t['ticket_number']) ?>
                                        </span>
                                        <span class="text-slate-300 font-semibold"><?= e($t['tier_name']) ?></span>
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        Attendee: <span class="text-white font-medium"><?= e($t['attendee_name'] ?: $booking['customer_name']) ?></span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <?php if ($t['status'] === 'valid'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                            <i data-lucide="check" class="w-3 h-3"></i> Valid Pass
                                        </span>
                                    <?php elseif ($t['status'] === 'used'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                            <i data-lucide="user-check" class="w-3 h-3"></i> Admitted / Used
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                            <?= e($t['status']) ?>
                                        </span>
                                    <?php endif; ?>

                                    <a href="/tickets/<?= $t['ticket_code'] ?>" target="_blank" class="px-3 py-1.5 bg-brand-600 hover:bg-brand-500 text-white rounded-lg text-xs font-semibold transition inline-flex items-center gap-1">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i> View Pass
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right 1 Col: Event & Customer & Payment details -->
        <div class="space-y-6">
            <!-- Event Card -->
            <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 border-b border-dark-700 pb-2">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-brand-400"></i> Event Details
                </h3>
                <div>
                    <h4 class="font-bold text-white text-sm"><?= e($booking['event_title']) ?></h4>
                    <p class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-500"></i>
                        <?= date('M d, Y', strtotime($booking['event_date'])) ?> &bull; <?= date('h:i A', strtotime($booking['start_time'])) ?>
                    </p>
                    <p class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-500"></i>
                        <?= e($booking['venue_name']) ?>, <?= e($booking['venue_city']) ?>
                    </p>
                </div>
                <div class="pt-2">
                    <a href="/events/<?= e($booking['event_slug']) ?>" target="_blank" class="text-xs text-brand-400 hover:text-brand-300 inline-flex items-center gap-1">
                        Public Event Page <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                    </a>
                </div>
            </div>

            <!-- Customer Card -->
            <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 border-b border-dark-700 pb-2">
                    <i data-lucide="user" class="w-3.5 h-3.5 text-brand-400"></i> Customer Profile
                </h3>
                <div class="space-y-1 text-xs">
                    <div class="font-semibold text-white text-sm"><?= e($booking['customer_name']) ?></div>
                    <div class="text-slate-400"><?= e($booking['customer_email']) ?></div>
                    <div class="text-slate-400 font-mono"><?= e($booking['customer_phone'] ?: 'No phone provided') ?></div>
                </div>
                <div class="pt-2">
                    <a href="/admin/customers/<?= $booking['customer_id'] ?>" class="text-xs text-brand-400 hover:text-brand-300 inline-flex items-center gap-1">
                        View Customer History <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </a>
                </div>
            </div>

            <!-- Payment Submission Card -->
            <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 border-b border-dark-700 pb-2">
                    <i data-lucide="credit-card" class="w-3.5 h-3.5 text-brand-400"></i> Payment Submission
                </h3>
                <?php if (!empty($booking['payment'])): ?>
                    <?php $pay = $booking['payment']; ?>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Provider:</span>
                            <span class="font-bold uppercase tracking-wider text-white"><?= e($pay['payment_method']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Transaction ID:</span>
                            <span class="font-mono font-bold text-white bg-dark-900 px-2 py-0.5 rounded border border-dark-600 select-all"><?= e($pay['transaction_id']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Sender Number:</span>
                            <span class="font-mono text-white"><?= e($pay['sender_number']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Submitted Amount:</span>
                            <span class="font-mono font-bold text-emerald-400"><?= format_currency((float)$pay['amount']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Status:</span>
                            <span class="font-semibold capitalize text-white"><?= e($pay['status']) ?></span>
                        </div>
                        <?php if (!empty($pay['proof_screenshot'])): ?>
                            <div class="pt-2">
                                <span class="block text-slate-400 text-[11px] mb-1.5">Proof Screenshot:</span>
                                <a href="<?= asset($pay['proof_screenshot']) ?>" target="_blank" class="block w-full h-32 rounded-xl overflow-hidden border border-dark-600 bg-dark-900 relative group">
                                    <img src="<?= asset($pay['proof_screenshot']) ?>" alt="Proof" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                        <i data-lucide="maximize-2" class="w-4 h-4"></i> View Full Image
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-xs text-slate-500 py-3 text-center">
                        No payment record submitted yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
