<?php 
$pageTitle = 'Reports & Financial Analytics'; 

$totalGross = array_sum(array_column($eventSales, 'total_revenue'));
$totalTicketsSold = array_sum(array_column($eventSales, 'tickets_sold'));
$totalCheckedIn = array_sum(array_column($attendanceData, 'total_checked_in'));
$attendanceRate = $totalTicketsSold > 0 ? round(($totalCheckedIn / $totalTicketsSold) * 100, 1) : 0;
?>

<div class="space-y-6">
    <!-- Header & CSV Exports -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-white flex items-center gap-2.5">
                <i data-lucide="bar-chart-3" class="w-7 h-7 text-brand-400"></i>
                Reports & Financial Analytics
            </h1>
            <p class="text-sm text-slate-400 mt-1">Real-time revenue metrics, payment channel distributions, and gate check-in statistics.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="/admin/reports/export?type=sales" class="px-3.5 py-2 bg-dark-800 hover:bg-dark-700 text-slate-200 border border-dark-600 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow">
                <i data-lucide="download" class="w-4 h-4 text-brand-400"></i> Export Sales CSV
            </a>
            <a href="/admin/reports/export?type=bookings" class="px-3.5 py-2 bg-dark-800 hover:bg-dark-700 text-slate-200 border border-dark-600 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-400"></i> Export Bookings CSV
            </a>
            <a href="/admin/reports/export?type=attendees" class="px-3.5 py-2 bg-dark-800 hover:bg-dark-700 text-slate-200 border border-dark-600 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow">
                <i data-lucide="users" class="w-4 h-4 text-sky-400"></i> Export Attendees CSV
            </a>
        </div>
    </div>

    <!-- Summary Metrics KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Gross Realized Revenue</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i data-lucide="bangladeshi-taka" class="w-5 h-5 font-bold">৳</i>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-white mt-3"><?= format_currency((float)$totalGross) ?></div>
            <div class="text-[11px] text-emerald-400 mt-1">Approved & confirmed collections</div>
        </div>

        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tickets Issued</span>
                <div class="w-9 h-9 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-400">
                    <i data-lucide="ticket" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-white mt-3"><?= number_format($totalTicketsSold) ?></div>
            <div class="text-[11px] text-slate-400 mt-1">Across all live events</div>
        </div>

        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Gate Check-ins</span>
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                    <i data-lucide="scan-line" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-white mt-3"><?= number_format((int)$totalCheckedIn) ?></div>
            <div class="text-[11px] text-amber-400 mt-1">Admitted attendees</div>
        </div>

        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Turnout Rate</span>
                <div class="w-9 h-9 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                    <i data-lucide="percent" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-white mt-3"><?= $attendanceRate ?>%</div>
            <div class="text-[11px] text-sky-400 mt-1">Check-in vs sold ratio</div>
        </div>
    </div>

    <!-- 2 Column Layout: Payment Methods Breakdown & Gate Attendance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue by Payment Provider -->
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 border-b border-dark-700 pb-3">
                <i data-lucide="wallet" class="w-4 h-4 text-brand-400"></i>
                Mobile Payment Providers
            </h3>
            <div class="space-y-3">
                <?php if (empty($paymentBreakdown)): ?>
                    <p class="text-xs text-slate-500 py-4 text-center">No approved payments recorded yet.</p>
                <?php else: ?>
                    <?php foreach ($paymentBreakdown as $pb): ?>
                        <?php 
                        $pct = $totalGross > 0 ? round(($pb['total_amount'] / $totalGross) * 100, 1) : 0;
                        $colorClass = match($pb['payment_method']) {
                            'bkash' => 'bg-pink-500',
                            'nagad' => 'bg-orange-500',
                            'rocket' => 'bg-purple-500',
                            default => 'bg-brand-500'
                        };
                        ?>
                        <div class="bg-dark-900/60 border border-dark-700/60 rounded-xl p-3.5 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold uppercase tracking-wider text-white"><?= e($pb['payment_method']) ?></span>
                                <span class="font-mono font-bold text-emerald-400"><?= format_currency((float)$pb['total_amount']) ?></span>
                            </div>
                            <div class="w-full bg-dark-700 h-2 rounded-full overflow-hidden">
                                <div class="<?= $colorClass ?> h-full rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
                            </div>
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span><?= $pb['total_count'] ?> successful payment<?= $pb['total_count'] > 1 ? 's' : '' ?></span>
                                <span><?= $pct ?>% of volume</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Attendance & Check-in Performance -->
        <div class="lg:col-span-2 bg-dark-800/80 border border-dark-600/60 rounded-2xl p-5 shadow-xl backdrop-blur-sm space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 border-b border-dark-700 pb-3">
                <i data-lucide="user-check" class="w-4 h-4 text-brand-400"></i>
                Gate Turnout & Attendance
            </h3>
            <div class="space-y-4">
                <?php if (empty($attendanceData)): ?>
                    <p class="text-xs text-slate-500 py-4 text-center">No attendance data to report.</p>
                <?php else: ?>
                    <?php foreach ($attendanceData as $att): ?>
                        <?php 
                        $total = (int)$att['total_tickets'];
                        $checked = (int)$att['total_checked_in'];
                        $p = $total > 0 ? round(($checked / $total) * 100, 1) : 0;
                        ?>
                        <div class="bg-dark-900/60 border border-dark-700/60 rounded-xl p-3.5 space-y-2 text-xs">
                            <div class="flex items-center justify-between font-semibold text-white">
                                <span class="truncate max-w-md"><?= e($att['title']) ?></span>
                                <span class="font-mono text-brand-300"><?= $checked ?> / <?= $total ?> (<?= $p ?>%)</span>
                            </div>
                            <div class="w-full bg-dark-700 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-brand-500 to-emerald-400 h-full rounded-full transition-all duration-500" style="width: <?= $p ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Detailed Event Sales Table -->
    <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm space-y-4 p-5">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 border-b border-dark-700 pb-3">
            <i data-lucide="layers" class="w-4 h-4 text-brand-400"></i>
            Revenue Performance by Event
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-dark-700 text-slate-400 uppercase font-semibold">
                        <th class="py-3 px-3">Event Title</th>
                        <th class="py-3 px-3">Event Date</th>
                        <th class="py-3 px-3 text-center">Paid Orders</th>
                        <th class="py-3 px-3 text-center">Tickets Sold</th>
                        <th class="py-3 px-3 text-right">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700/60 text-slate-200">
                    <?php foreach ($eventSales as $es): ?>
                        <tr class="hover:bg-dark-700/40 transition">
                            <td class="py-3.5 px-3 font-semibold text-white"><?= e($es['title']) ?></td>
                            <td class="py-3.5 px-3 text-slate-400"><?= date('M d, Y', strtotime($es['event_date'])) ?></td>
                            <td class="py-3.5 px-3 text-center font-mono"><?= $es['total_bookings'] ?></td>
                            <td class="py-3.5 px-3 text-center font-mono font-bold text-brand-300"><?= $es['tickets_sold'] ?></td>
                            <td class="py-3.5 px-3 text-right font-mono font-bold text-emerald-400 text-sm"><?= format_currency((float)$es['total_revenue']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
