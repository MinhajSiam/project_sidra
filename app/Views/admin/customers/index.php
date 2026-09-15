<?php $pageTitle = 'Customers Directory'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-white flex items-center gap-2.5">
                <i data-lucide="users" class="w-7 h-7 text-brand-400"></i>
                Customer Directory
            </h1>
            <p class="text-sm text-slate-400 mt-1">Platform attendees, ticket buyers, order histories, and contact records.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-dark-800 text-slate-300 border border-dark-600">
                Total Registered: <?= count($customers) ?>
            </span>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-dark-700 bg-dark-900/60 text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-4">Customer Name & Contact</th>
                        <th class="py-3.5 px-4">Email Address</th>
                        <th class="py-3.5 px-4">Phone Number</th>
                        <th class="py-3.5 px-4">Orders Placed</th>
                        <th class="py-3.5 px-4">Tickets Held</th>
                        <th class="py-3.5 px-4">Joined Date</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700/60 text-slate-200">
                    <?php foreach ($customers as $c): ?>
                        <tr class="hover:bg-dark-700/40 transition">
                            <td class="py-4 px-4 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-600/20 text-brand-300 border border-brand-500/30 flex items-center justify-center font-bold text-xs uppercase">
                                        <?= strtoupper(substr($c['name'], 0, 2)) ?>
                                    </div>
                                    <a href="/admin/customers/<?= $c['id'] ?>" class="font-semibold text-white hover:text-brand-400 hover:underline">
                                        <?= e($c['name']) ?>
                                    </a>
                                </div>
                            </td>
                            <td class="py-4 px-4 align-top text-slate-300">
                                <?= e($c['email']) ?>
                            </td>
                            <td class="py-4 px-4 align-top font-mono text-slate-300">
                                <?= e($c['phone'] ?: '—') ?>
                            </td>
                            <td class="py-4 px-4 align-top">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-dark-900 border border-dark-600 font-mono font-bold text-slate-200">
                                    <?= $c['total_bookings'] ?> order<?= $c['total_bookings'] != 1 ? 's' : '' ?>
                                </span>
                            </td>
                            <td class="py-4 px-4 align-top">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-dark-900 border border-dark-600 font-mono font-bold text-emerald-400">
                                    <?= $c['total_tickets'] ?> ticket<?= $c['total_tickets'] != 1 ? 's' : '' ?>
                                </span>
                            </td>
                            <td class="py-4 px-4 align-top text-slate-400 text-[11px]">
                                <?= date('M d, Y', strtotime($c['created_at'])) ?>
                            </td>
                            <td class="py-4 px-4 align-top text-right">
                                <a href="/admin/customers/<?= $c['id'] ?>" class="px-3 py-1.5 bg-dark-700 hover:bg-dark-600 text-slate-300 hover:text-white rounded-lg text-xs font-semibold transition inline-flex items-center gap-1">
                                    View History <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
