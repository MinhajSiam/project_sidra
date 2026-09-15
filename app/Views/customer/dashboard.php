<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <!-- Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-white">Attendee Dashboard</h1>
            <p class="text-xs text-slate-400 mt-1">Manage your active digital passes, order receipts, and account credentials.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= url('customer/tickets') ?>" class="px-4 py-2.5 rounded-xl gradient-brand text-white font-bold text-xs shadow-lg flex items-center gap-2">
                <i data-lucide="qr-code" class="w-4 h-4"></i> My Digital Tickets
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="glass-card rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-600/20 text-brand-400 flex items-center justify-center flex-shrink-0">
                <i data-lucide="ticket" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-400">Issued Tickets</div>
                <div class="font-heading font-extrabold text-2xl text-white"><?= count($tickets) ?></div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-600/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                <i data-lucide="receipt" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-400">Total Orders</div>
                <div class="font-heading font-extrabold text-2xl text-white"><?= count($bookings) ?></div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-600/20 text-purple-400 flex items-center justify-center flex-shrink-0">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-400">Next Event</div>
                <div class="font-heading font-bold text-sm text-white truncate max-w-[160px]">
                    <?= !empty($tickets[0]['event_title']) ? e($tickets[0]['event_title']) : 'No upcoming events' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Tickets Shelf -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-heading font-bold text-xl text-white">My Active Passes</h2>
            <a href="<?= url('customer/tickets') ?>" class="text-xs text-brand-400 font-bold hover:underline">View All Tickets &rarr;</a>
        </div>

        <?php if (empty($tickets)): ?>
            <div class="glass-card rounded-2xl p-8 text-center text-slate-400 text-xs">
                You don't have any active digital tickets yet. 
                <a href="<?= url('events') ?>" class="text-brand-400 font-bold underline ml-1">Browse events to book passes.</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach (array_slice($tickets, 0, 3) as $ticket): ?>
                    <div class="glass-card rounded-2xl p-5 border border-slate-800 space-y-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase <?= $ticket['status'] === 'valid' ? 'badge-valid' : 'badge-used' ?>">
                                    <?= $ticket['status'] === 'valid' ? 'Valid Pass' : 'Used' ?>
                                </span>
                                <span class="font-mono text-xs text-slate-400"><?= e($ticket['ticket_code']) ?></span>
                            </div>
                            <h3 class="font-heading font-bold text-base text-white mt-2 line-clamp-1"><?= e($ticket['event_title']) ?></h3>
                            <div class="text-xs text-brand-400 font-semibold mt-0.5"><?= e($ticket['ticket_type_name']) ?></div>
                            <div class="text-[11px] text-slate-400 mt-1">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1"></i>
                                <?= format_date($ticket['event_date']) ?>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex items-center justify-between gap-2">
                            <a href="<?= url("customer/tickets/{$ticket['ticket_code']}") ?>" class="flex-1 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-center text-xs font-bold text-white transition-colors">
                                View Pass
                            </a>
                            <a href="<?= url("customer/tickets/{$ticket['ticket_code']}/print") ?>" target="_blank" class="px-3 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold transition-colors" title="Print PDF">
                                <i data-lucide="printer" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Bookings Table -->
    <div class="space-y-4">
        <h2 class="font-heading font-bold text-xl text-white">Recent Orders</h2>
        <div class="glass-card rounded-2xl overflow-hidden border border-slate-800">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 uppercase font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Reference</th>
                        <th class="px-6 py-4">Event</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">No orders placed yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-brand-400"><?= e($b['booking_reference']) ?></td>
                                <td class="px-6 py-4 font-semibold text-white"><?= e($b['event_title']) ?></td>
                                <td class="px-6 py-4 font-bold text-white"><?= format_currency($b['final_amount']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full font-bold uppercase text-[10px] <?= $b['status'] === 'confirmed' ? 'badge-confirmed' : ($b['status'] === 'pending_payment' ? 'badge-pending' : 'badge-rejected') ?>">
                                        <?= str_replace('_', ' ', $b['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-400"><?= format_date($b['created_at']) ?></td>
                                <td class="px-6 py-4 text-right">
                                    <?php if ($b['status'] === 'pending_payment'): ?>
                                        <a href="<?= url("booking/{$b['booking_reference']}/payment") ?>" class="px-3 py-1.5 rounded-lg gradient-brand text-white font-bold text-xs">
                                            Pay Now
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= url("booking/{$b['booking_reference']}/confirmation") ?>" class="text-brand-400 hover:underline font-bold">
                                            View Order
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
