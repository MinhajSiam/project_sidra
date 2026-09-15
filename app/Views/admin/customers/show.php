<?php $pageTitle = 'Customer: ' . e($customer['name']); ?>

<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="/admin/customers" class="p-2 text-slate-400 hover:text-white hover:bg-dark-800 rounded-xl transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold font-heading text-white"><?= e($customer['name']) ?></h1>
                <p class="text-xs text-slate-400 mt-0.5">Account Member since <?= date('M d, Y', strtotime($customer['created_at'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Stats & Details -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-4 shadow backdrop-blur-sm">
            <div class="text-xs text-slate-400 mb-1">Email Address</div>
            <div class="text-sm font-semibold text-white truncate" title="<?= e($customer['email']) ?>"><?= e($customer['email']) ?></div>
        </div>
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-4 shadow backdrop-blur-sm">
            <div class="text-xs text-slate-400 mb-1">Phone Number</div>
            <div class="text-sm font-mono font-semibold text-white"><?= e($customer['phone'] ?: 'None specified') ?></div>
        </div>
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-4 shadow backdrop-blur-sm">
            <div class="text-xs text-slate-400 mb-1">Total Orders</div>
            <div class="text-xl font-bold font-mono text-brand-400"><?= count($bookings) ?></div>
        </div>
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-4 shadow backdrop-blur-sm">
            <div class="text-xs text-slate-400 mb-1">Active Passes</div>
            <div class="text-xl font-bold font-mono text-emerald-400"><?= count(array_filter($tickets, fn($t) => $t['status'] === 'valid')) ?></div>
        </div>
    </div>

    <!-- Bookings by Customer -->
    <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 border-b border-dark-700 pb-3">
            <i data-lucide="shopping-bag" class="w-4 h-4 text-brand-400"></i>
            Order History
        </h3>
        <?php if (empty($bookings)): ?>
            <div class="text-center py-6 text-slate-500 text-xs">This customer hasn't placed any bookings yet.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-dark-700 text-slate-400 uppercase font-semibold">
                            <th class="py-2.5 px-3">Reference</th>
                            <th class="py-2.5 px-3">Event</th>
                            <th class="py-2.5 px-3">Tickets</th>
                            <th class="py-2.5 px-3">Total</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3">Date</th>
                            <th class="py-2.5 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-700/60 text-slate-200">
                        <?php foreach ($bookings as $b): ?>
                            <tr class="hover:bg-dark-700/40 transition">
                                <td class="py-3 px-3 font-mono font-bold text-brand-400"><?= e($b['booking_reference']) ?></td>
                                <td class="py-3 px-3 text-white"><?= e($b['event_title']) ?></td>
                                <td class="py-3 px-3 font-bold"><?= $b['total_tickets'] ?></td>
                                <td class="py-3 px-3 font-mono text-emerald-400 font-bold"><?= format_currency((float)$b['final_amount']) ?></td>
                                <td class="py-3 px-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold <?= $b['status'] === 'confirmed' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' ?>">
                                        <?= e($b['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-slate-400"><?= date('M d, Y', strtotime($b['created_at'])) ?></td>
                                <td class="py-3 px-3 text-right">
                                    <a href="/admin/bookings/<?= $b['id'] ?>" class="text-brand-400 hover:text-brand-300">View &rarr;</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Digital Passes Held -->
    <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm space-y-4">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 border-b border-dark-700 pb-3">
            <i data-lucide="ticket" class="w-4 h-4 text-brand-400"></i>
            Digital Passes Held
        </h3>
        <?php if (empty($tickets)): ?>
            <div class="text-center py-6 text-slate-500 text-xs">No tickets issued for this user.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-dark-700 text-slate-400 uppercase font-semibold">
                            <th class="py-2.5 px-3">Ticket Code</th>
                            <th class="py-2.5 px-3">Event</th>
                            <th class="py-2.5 px-3">Tier</th>
                            <th class="py-2.5 px-3">Attendee</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3 text-right">Pass Link</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-700/60 text-slate-200">
                        <?php foreach ($tickets as $t): ?>
                            <tr class="hover:bg-dark-700/40 transition">
                                <td class="py-3 px-3 font-mono font-bold text-white"><?= e($t['ticket_code']) ?></td>
                                <td class="py-3 px-3 text-slate-200"><?= e($t['event_title'] ?? 'Event') ?></td>
                                <td class="py-3 px-3 text-brand-300"><?= e($t['tier_name'] ?? 'Tier') ?></td>
                                <td class="py-3 px-3 text-white"><?= e($t['attendee_name'] ?: $customer['name']) ?></td>
                                <td class="py-3 px-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold <?= $t['status'] === 'valid' ? 'bg-emerald-500/10 text-emerald-400' : ($t['status'] === 'used' ? 'bg-amber-500/10 text-amber-400' : 'bg-rose-500/10 text-rose-400') ?>">
                                        <?= e($t['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="/tickets/<?= $t['ticket_code'] ?>" target="_blank" class="text-brand-400 hover:text-brand-300">Open Pass &rarr;</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
