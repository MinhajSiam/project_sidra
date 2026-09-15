<?php $pageTitle = 'Bookings & Orders'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-white flex items-center gap-2.5">
                <i data-lucide="shopping-bag" class="w-7 h-7 text-brand-400"></i>
                Bookings Management
            </h1>
            <p class="text-sm text-slate-400 mt-1">Track all orders, customer reservations, and ticket issuance statuses across events.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-dark-800 text-slate-300 border border-dark-600">
                Total Orders: <?= count($bookings) ?>
            </span>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-4 shadow-xl backdrop-blur-sm">
        <form method="GET" action="/admin/bookings" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Payment</option>
                    <option value="confirmed" <?= ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Event</label>
                <select name="event_id" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                    <option value="">All Events</option>
                    <?php foreach ($events as $ev): ?>
                        <option value="<?= $ev['id'] ?>" <?= ($filters['event_id'] ?? '') == $ev['id'] ? 'selected' : '' ?>>
                            <?= e($ev['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Search</label>
                <div class="relative">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Ref, customer, phone, TrxID..." class="w-full bg-dark-900 border border-dark-600 rounded-xl pl-8 pr-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i> Apply Filter
                </button>
                <?php if (!empty($filters['status']) || !empty($filters['event_id']) || !empty($filters['search'])): ?>
                    <a href="/admin/bookings" class="p-2 text-slate-400 hover:text-white hover:bg-dark-700 rounded-xl transition" title="Clear Filters">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <?php if (empty($bookings)): ?>
        <div class="bg-dark-800/60 border border-dark-700/60 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-dark-700 flex items-center justify-center mx-auto mb-4 text-slate-500">
                <i data-lucide="inbox" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-white mb-1">No bookings found</h3>
            <p class="text-sm text-slate-400">There are no orders matching your current filter criteria.</p>
        </div>
    <?php else: ?>
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-dark-700 bg-dark-900/60 text-slate-400 uppercase tracking-wider font-semibold">
                            <th class="py-3.5 px-4">Booking Ref & Date</th>
                            <th class="py-3.5 px-4">Customer</th>
                            <th class="py-3.5 px-4">Event</th>
                            <th class="py-3.5 px-4">Tickets</th>
                            <th class="py-3.5 px-4">Total Amount</th>
                            <th class="py-3.5 px-4">Payment Info</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-700/60 text-slate-200">
                        <?php foreach ($bookings as $b): ?>
                            <tr class="hover:bg-dark-700/40 transition">
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1">
                                        <a href="/admin/bookings/<?= $b['id'] ?>" class="font-mono font-bold text-brand-400 hover:text-brand-300 hover:underline">
                                            <?= e($b['booking_reference']) ?>
                                        </a>
                                        <div class="text-[11px] text-slate-400">
                                            <?= date('M d, Y', strtotime($b['created_at'])) ?>
                                        </div>
                                        <div class="text-[10px] text-slate-500">
                                            <?= date('h:i A', strtotime($b['created_at'])) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-0.5">
                                        <div class="font-semibold text-white"><?= e($b['customer_name']) ?></div>
                                        <div class="text-[11px] text-slate-400"><?= e($b['customer_email']) ?></div>
                                        <div class="text-[11px] text-slate-400 font-mono"><?= e($b['customer_phone'] ?: 'No phone') ?></div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-0.5 max-w-[200px]">
                                        <div class="font-medium text-white line-clamp-1" title="<?= e($b['event_title']) ?>">
                                            <?= e($b['event_title']) ?>
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            <?= date('M d, Y', strtotime($b['event_date'])) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-dark-900 border border-dark-600 font-bold text-white">
                                        <?= $b['total_tickets'] ?> Pass<?= $b['total_tickets'] > 1 ? 'es' : '' ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="font-mono font-bold text-sm text-emerald-400">
                                        <?= format_currency((float)$b['final_amount']) ?>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1">
                                        <?php if (!empty($b['payment_method'])): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-dark-700 text-slate-300 border border-dark-600">
                                                <?= e($b['payment_method']) ?>
                                            </span>
                                            <?php if (!empty($b['transaction_id'])): ?>
                                                <div class="font-mono text-[11px] text-brand-300 select-all">
                                                    <?= e($b['transaction_id']) ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-slate-500 italic text-[11px]">Unsubmitted</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <?php if ($b['status'] === 'confirmed'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Confirmed
                                        </span>
                                    <?php elseif ($b['status'] === 'pending'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Pending Review
                                        </span>
                                    <?php elseif ($b['status'] === 'cancelled'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i> Cancelled
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 align-top text-right">
                                    <a href="/admin/bookings/<?= $b['id'] ?>" class="px-3 py-1.5 bg-dark-700 hover:bg-dark-600 text-white rounded-lg text-xs font-semibold transition inline-flex items-center gap-1">
                                        Details <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
