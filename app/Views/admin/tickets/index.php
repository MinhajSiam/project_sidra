<?php $pageTitle = 'Tickets Directory'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-white flex items-center gap-2.5">
                <i data-lucide="ticket" class="w-7 h-7 text-brand-400"></i>
                Issued Digital Tickets
            </h1>
            <p class="text-sm text-slate-400 mt-1">Platform-wide master directory of all generated digital passes, codes, and check-in statuses.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-dark-800 text-slate-300 border border-dark-600">
                Total Passes: <?= count($tickets) ?>
            </span>
            <a href="/gate/scan" target="_blank" class="px-3.5 py-1.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                <i data-lucide="scan-line" class="w-3.5 h-3.5"></i> Launch Gate Scanner
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-4 shadow-xl backdrop-blur-sm">
        <form method="GET" action="/admin/tickets" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Pass Status</label>
                <select name="status" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                    <option value="">All Statuses</option>
                    <option value="valid" <?= ($filters['status'] ?? '') === 'valid' ? 'selected' : '' ?>>Valid / Unused</option>
                    <option value="used" <?= ($filters['status'] ?? '') === 'used' ? 'selected' : '' ?>>Admitted / Used</option>
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
                    <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Ticket code, attendee, phone..." class="w-full bg-dark-900 border border-dark-600 rounded-xl pl-8 pr-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i> Apply Filter
                </button>
                <?php if (!empty($filters['status']) || !empty($filters['event_id']) || !empty($filters['search'])): ?>
                    <a href="/admin/tickets" class="p-2 text-slate-400 hover:text-white hover:bg-dark-700 rounded-xl transition" title="Clear Filters">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <?php if (empty($tickets)): ?>
        <div class="bg-dark-800/60 border border-dark-700/60 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-dark-700 flex items-center justify-center mx-auto mb-4 text-slate-500">
                <i data-lucide="ticket" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-white mb-1">No tickets match criteria</h3>
            <p class="text-sm text-slate-400">There are no passes found matching your current filter filters.</p>
        </div>
    <?php else: ?>
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-dark-700 bg-dark-900/60 text-slate-400 uppercase tracking-wider font-semibold">
                            <th class="py-3.5 px-4">Ticket Code & Tier</th>
                            <th class="py-3.5 px-4">Event & Venue</th>
                            <th class="py-3.5 px-4">Attendee Info</th>
                            <th class="py-3.5 px-4">Purchaser</th>
                            <th class="py-3.5 px-4">Price Paid</th>
                            <th class="py-3.5 px-4">Status & Gate Check-in</th>
                            <th class="py-3.5 px-4 text-right">View Pass</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-700/60 text-slate-200">
                        <?php foreach ($tickets as $t): ?>
                            <tr class="hover:bg-dark-700/40 transition">
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1">
                                        <div class="font-mono font-bold text-white bg-dark-900 px-2 py-1 rounded border border-dark-600 select-all inline-block">
                                            <?= e($t['ticket_code']) ?>
                                        </div>
                                        <div class="text-xs font-semibold text-brand-300">
                                            <?= e($t['ticket_type_name']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-0.5 max-w-[200px]">
                                        <div class="font-medium text-white line-clamp-1" title="<?= e($t['event_title']) ?>">
                                            <?= e($t['event_title']) ?>
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            <?= date('M d, Y', strtotime($t['event_date'])) ?>
                                        </div>
                                        <div class="text-[11px] text-slate-500">
                                            <?= e($t['venue_name']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-0.5">
                                        <div class="font-semibold text-white"><?= e($t['attendee_name'] ?: 'N/A') ?></div>
                                        <div class="text-[11px] text-slate-400"><?= e($t['attendee_email'] ?: '') ?></div>
                                        <div class="text-[11px] text-slate-400 font-mono"><?= e($t['attendee_phone'] ?: '') ?></div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-0.5">
                                        <div class="text-slate-300 font-medium"><?= e($t['customer_name']) ?></div>
                                        <div class="text-[11px] text-slate-500 font-mono"><?= e($t['customer_phone'] ?: '') ?></div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="font-mono font-bold text-emerald-400">
                                        <?= format_currency((float)$t['price_paid']) ?>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <?php if ($t['status'] === 'valid'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                            <i data-lucide="check" class="w-3 h-3"></i> Valid / Unused
                                        </span>
                                    <?php elseif ($t['status'] === 'used'): ?>
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                                <i data-lucide="user-check" class="w-3 h-3"></i> Checked In
                                            </span>
                                            <?php if (!empty($t['checked_in_at'])): ?>
                                                <div class="text-[10px] text-slate-500">
                                                    <?= date('M d, h:i A', strtotime($t['checked_in_at'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                            <?= e($t['status']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 align-top text-right">
                                    <a href="/tickets/<?= $t['ticket_code'] ?>" target="_blank" class="px-3 py-1.5 bg-dark-700 hover:bg-dark-600 text-white rounded-lg text-xs font-semibold transition inline-flex items-center gap-1">
                                        Pass <i data-lucide="external-link" class="w-3 h-3"></i>
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
