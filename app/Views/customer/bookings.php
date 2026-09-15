<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between pb-6 border-b border-slate-800">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-white">Order History</h1>
            <p class="text-xs text-slate-400 mt-1">Review all your past and pending ticket purchases.</p>
        </div>
        <a href="<?= url('events') ?>" class="px-4 py-2 rounded-xl gradient-brand text-white font-bold text-xs shadow">
            Book More Events
        </a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-slate-800">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900/90 text-slate-400 uppercase font-semibold border-b border-slate-800">
                <tr>
                    <th class="px-6 py-4">Reference</th>
                    <th class="px-6 py-4">Event</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Payment TrxID</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            No bookings recorded yet.
                        </td>
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
                            <td class="px-6 py-4 font-mono text-slate-400">
                                <?= !empty($b['transaction_id']) ? strtoupper(e($b['payment_method'])) . ': ' . e($b['transaction_id']) : 'Pending Trx' ?>
                            </td>
                            <td class="px-6 py-4 text-slate-400"><?= format_date($b['created_at']) ?></td>
                            <td class="px-6 py-4 text-right">
                                <?php if ($b['status'] === 'pending_payment'): ?>
                                    <a href="<?= url("booking/{$b['booking_reference']}/payment") ?>" class="px-3 py-1.5 rounded-lg gradient-brand text-white font-bold text-xs">
                                        Complete Payment
                                    </a>
                                <?php else: ?>
                                    <a href="<?= url("booking/{$b['booking_reference']}/confirmation") ?>" class="text-brand-400 hover:underline font-bold">
                                        View Details
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
