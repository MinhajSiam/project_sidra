<div class="space-y-8">
    <!-- Top Greeting & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-white">Management Console</h1>
            <p class="text-xs text-slate-400 mt-1">Platform overview, revenue metrics, and live queue status.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= url('admin/payments') ?>" class="px-4 py-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 font-bold text-xs hover:bg-amber-500/20 transition-all flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4"></i> Review Payments (<?= $pendingPaymentsCount ?>)
            </a>
            <a href="<?= url('gate/scan') ?>" target="_blank" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-2">
                <i data-lucide="camera" class="w-4 h-4"></i> Gate Scanner
            </a>
        </div>
    </div>

    <!-- 5 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <div class="glass-card rounded-2xl p-5 space-y-2 border border-slate-800">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
                <span>Total Revenue</span>
                <i data-lucide="banknote" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <div id="dashboard-total-revenue" class="font-heading font-extrabold text-2xl text-white"><?= format_currency($totalRevenue) ?></div>
            <div class="text-[10px] text-emerald-400 font-bold">Approved Payments</div>
        </div>

        <div class="glass-card rounded-2xl p-5 space-y-2 border border-slate-800">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
                <span>Total Bookings</span>
                <i data-lucide="receipt" class="w-4 h-4 text-brand-400"></i>
            </div>
            <div id="dashboard-total-bookings" class="font-heading font-extrabold text-2xl text-white"><?= number_format($totalBookings) ?></div>
            <div class="text-[10px] text-slate-400">Orders placed</div>
        </div>

        <div class="glass-card rounded-2xl p-5 space-y-2 border border-amber-500/20 bg-amber-950/10">
            <div class="flex items-center justify-between text-amber-400 text-xs font-semibold">
                <span>Pending Review</span>
                <i data-lucide="clock" class="w-4 h-4 text-amber-400"></i>
            </div>
            <div id="dashboard-pending-payments" class="font-heading font-extrabold text-2xl text-amber-300"><?= number_format($pendingPaymentsCount) ?></div>
            <div class="text-[10px] text-amber-400 font-bold">Requires Action</div>
        </div>

        <div class="glass-card rounded-2xl p-5 space-y-2 border border-slate-800">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
                <span>Tickets Sold</span>
                <i data-lucide="ticket" class="w-4 h-4 text-purple-400"></i>
            </div>
            <div id="dashboard-tickets-sold" class="font-heading font-extrabold text-2xl text-white"><?= number_format($ticketsSoldCount) ?></div>
            <div class="text-[10px] text-purple-400 font-bold">Issued Passes</div>
        </div>

        <div class="glass-card rounded-2xl p-5 space-y-2 border border-slate-800">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
                <span>Gate Check-ins</span>
                <i data-lucide="user-check" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <div id="dashboard-checked-in" class="font-heading font-extrabold text-2xl text-white"><?= number_format($checkedInCount) ?></div>
            <div class="text-[10px] text-emerald-400 font-bold">Admitted</div>
        </div>
    </div>

    <!-- Payments Awaiting Review Table -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-5 border border-slate-800">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading font-bold text-xl text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Payments Awaiting Review
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Approve to automatically generate valid digital QR tickets.</p>
            </div>
            <a href="<?= url('admin/payments') ?>" class="text-xs text-brand-400 hover:underline font-bold">View Full Queue &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 uppercase font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Booking Ref</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Method</th>
                        <th class="px-4 py-3">Sender Phone</th>
                        <th class="px-4 py-3">Transaction ID</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3 text-right">Review Action</th>
                    </tr>
                </thead>
                <tbody id="dashboard-pending-payments-list" class="divide-y divide-slate-800/60">
                    <?php if (empty($pendingPayments)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                <i data-lucide="check-circle-2" class="w-6 h-6 text-emerald-500 inline-block mb-1"></i>
                                <div>No pending payments. All orders are up to date!</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendingPayments as $p): ?>
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="px-4 py-3.5 font-mono font-bold text-brand-400"><?= e($p['booking_reference']) ?></td>
                                <td class="px-4 py-3.5 font-semibold text-white"><?= e($p['customer_name']) ?></td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $p['payment_method'] === 'bkash' ? 'bg-pink-950 text-pink-300' : ($p['payment_method'] === 'nagad' ? 'bg-orange-950 text-orange-300' : 'bg-purple-950 text-purple-300') ?>">
                                        <?= e($p['payment_method']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-300"><?= e($p['sender_number']) ?></td>
                                <td class="px-4 py-3.5 font-mono font-bold text-amber-300"><?= e($p['transaction_id']) ?></td>
                                <td class="px-4 py-3.5 font-bold text-white"><?= format_currency($p['amount']) ?></td>
                                <td class="px-4 py-3.5 text-right space-x-2">
                                    <form action="<?= url("admin/payments/{$p['id']}/approve") ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" onclick="return confirm('Approve this payment and generate official digital tickets?')" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="<?= url("admin/payments/{$p['id']}/reject") ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" onclick="return confirm('Reject this payment and restore ticket inventory?')" class="px-3 py-1 rounded-lg bg-rose-950 hover:bg-rose-900 border border-rose-500/40 text-rose-300 font-bold text-xs transition-colors">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2 Column Grid: Recent Bookings + Upcoming Events -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Bookings -->
        <div class="glass-card rounded-3xl p-6 space-y-4 border border-slate-800">
            <h3 class="font-heading font-bold text-lg text-white">Latest Orders</h3>
            <div id="dashboard-recent-bookings-list" class="space-y-3">
                <?php foreach ($recentBookings as $b): ?>
                    <div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-mono font-bold text-brand-400"><?= e($b['booking_reference']) ?></div>
                            <div class="text-white font-medium mt-0.5"><?= e($b['customer_name']) ?> • <?= e($b['event_title']) ?></div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-white"><?= format_currency($b['final_amount']) ?></div>
                            <span class="text-[10px] uppercase font-bold text-slate-400"><?= str_replace('_', ' ', $b['status']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Upcoming Events Status -->
        <div class="glass-card rounded-3xl p-6 space-y-4 border border-slate-800">
            <h3 class="font-heading font-bold text-lg text-white">Active Events</h3>
            <div class="space-y-3">
                <?php foreach ($upcomingEvents as $ev): ?>
                    <div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-white"><?= e($ev['title']) ?></div>
                            <div class="text-slate-400 mt-0.5"><?= format_date($ev['event_date']) ?> • <?= e($ev['venue_name']) ?></div>
                        </div>
                        <div class="text-right">
                            <a href="<?= url("admin/events/{$ev['id']}/edit") ?>" class="text-brand-400 hover:underline font-bold">
                                Edit & Quotas
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const endpoint = <?= json_encode(url('admin/dashboard/live'), JSON_UNESCAPED_SLASHES) ?>;
        const initialBookingCount = <?= (int)$totalBookings ?>;
        let knownBookingCount = initialBookingCount;
        let refreshInFlight = false;

        const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#039;',
            '"': '&quot;'
        })[character]);
        const money = (value) => '৳ ' + Number(value || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const number = (value) => Number(value || 0).toLocaleString('en-US');

        const paymentRows = (payments) => {
            if (!payments.length) {
                return `<tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">
                    <i data-lucide="check-circle-2" class="w-6 h-6 text-emerald-500 inline-block mb-1"></i>
                    <div>No pending payments. All orders are up to date!</div>
                </td></tr>`;
            }

            return payments.map((payment) => {
                const methodClass = payment.payment_method === 'bkash' ?
                    'bg-pink-950 text-pink-300' :
                    (payment.payment_method === 'nagad' ? 'bg-orange-950 text-orange-300' : 'bg-purple-950 text-purple-300');
                return `<tr class="hover:bg-slate-800/40 transition-colors">
                    <td class="px-4 py-3.5 font-mono font-bold text-brand-400">${escapeHtml(payment.booking_reference)}</td>
                    <td class="px-4 py-3.5 font-semibold text-white">${escapeHtml(payment.customer_name)}</td>
                    <td class="px-4 py-3.5"><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase ${methodClass}">${escapeHtml(payment.payment_method)}</span></td>
                    <td class="px-4 py-3.5 font-mono text-slate-300">${escapeHtml(payment.sender_number)}</td>
                    <td class="px-4 py-3.5 font-mono font-bold text-amber-300">${escapeHtml(payment.transaction_id)}</td>
                    <td class="px-4 py-3.5 font-bold text-white">${money(payment.amount)}</td>
                    <td class="px-4 py-3.5 text-right space-x-2">
                        <form action="${escapeHtml(<?= json_encode(url('admin/payments'), JSON_UNESCAPED_SLASHES) ?>)}/${Number(payment.id)}/approve" method="POST" class="inline">
                            <input type="hidden" name="_token" value="${escapeHtml(<?= json_encode(csrf_token()) ?>)}">
                            <button type="submit" onclick="return confirm('Approve this payment and generate official digital tickets?')" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow transition-colors">Approve</button>
                        </form>
                        <form action="${escapeHtml(<?= json_encode(url('admin/payments'), JSON_UNESCAPED_SLASHES) ?>)}/${Number(payment.id)}/reject" method="POST" class="inline">
                            <input type="hidden" name="_token" value="${escapeHtml(<?= json_encode(csrf_token()) ?>)}">
                            <button type="submit" onclick="return confirm('Reject this payment and restore ticket inventory?')" class="px-3 py-1 rounded-lg bg-rose-950 hover:bg-rose-900 border border-rose-500/40 text-rose-300 font-bold text-xs transition-colors">Reject</button>
                        </form>
                    </td>
                </tr>`;
            }).join('');
        };

        const bookingRows = (bookings) => bookings.map((booking) => `<div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between text-xs">
            <div><div class="font-mono font-bold text-brand-400">${escapeHtml(booking.booking_reference)}</div>
            <div class="text-white font-medium mt-0.5">${escapeHtml(booking.customer_name)} • ${escapeHtml(booking.event_title)}</div></div>
            <div class="text-right"><div class="font-bold text-white">${money(booking.final_amount)}</div>
            <span class="text-[10px] uppercase font-bold text-slate-400">${escapeHtml(String(booking.status).replaceAll('_', ' '))}</span></div>
        </div>`).join('');

        const refreshDashboard = async () => {
            if (refreshInFlight || document.hidden) return;
            refreshInFlight = true;
            try {
                const response = await fetch(endpoint + '?t=' + Date.now(), {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                });
                if (!response.ok) return;
                const data = await response.json();
                document.getElementById('dashboard-total-revenue').textContent = money(data.totalRevenue);
                document.getElementById('dashboard-total-bookings').textContent = number(data.totalBookings);
                document.getElementById('dashboard-pending-payments').textContent = number(data.pendingPaymentsCount);
                document.getElementById('dashboard-tickets-sold').textContent = number(data.ticketsSoldCount);
                document.getElementById('dashboard-checked-in').textContent = number(data.checkedInCount);
                document.getElementById('dashboard-pending-payments-list').innerHTML = paymentRows(data.pendingPayments || []);
                document.getElementById('dashboard-recent-bookings-list').innerHTML = bookingRows(data.recentBookings || []);

                if (data.totalBookings > knownBookingCount) {
                    const heading = document.querySelector('#dashboard-recent-bookings-list')?.previousElementSibling;
                    if (heading) heading.classList.add('text-emerald-300');
                    knownBookingCount = data.totalBookings;
                }
                if (window.lucide) lucide.createIcons();
            } catch (error) {
                // A temporary network failure should not interrupt dashboard use.
            } finally {
                refreshInFlight = false;
            }
        };

        setInterval(refreshDashboard, 5000);
    })();
</script>