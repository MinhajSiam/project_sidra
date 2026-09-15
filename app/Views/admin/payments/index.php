<?php $pageTitle = 'Payments & Verification'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-white flex items-center gap-2.5">
                <i data-lucide="credit-card" class="w-7 h-7 text-brand-400"></i>
                Manual Payments Review
            </h1>
            <p class="text-sm text-slate-400 mt-1">Verify mobile financial service payments (bKash, Nagad, Rocket) and release digital tickets.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <?= count(array_filter($payments, fn($p) => $p['status'] === 'pending')) ?> Pending Verification
            </span>
        </div>
    </div>

    <!-- Filter Bar & Status Tabs -->
    <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-4 shadow-xl backdrop-blur-sm space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-dark-700/60 pb-4">
            <!-- Status Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                <a href="/admin/payments?status=pending" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 <?= ($currentStatus === 'pending') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-dark-700' ?>">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    Pending Queue
                </a>
                <a href="/admin/payments?status=approved" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 <?= ($currentStatus === 'approved') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'text-slate-400 hover:text-white hover:bg-dark-700' ?>">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Approved
                </a>
                <a href="/admin/payments?status=rejected" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 <?= ($currentStatus === 'rejected') ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'text-slate-400 hover:text-white hover:bg-dark-700' ?>">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                    Rejected
                </a>
                <a href="/admin/payments?status=" class="px-4 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 <?= empty($currentStatus) ? 'bg-brand-600/20 text-brand-300 border border-brand-500/30' : 'text-slate-400 hover:text-white hover:bg-dark-700' ?>">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                    All Records
                </a>
            </div>

            <!-- Method Filter Pills -->
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 hidden sm:inline">Provider:</span>
                <?php foreach (['all' => 'All', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket'] as $mKey => $mLabel): ?>
                    <a href="/admin/payments?status=<?= e($currentStatus) ?>&method=<?= $mKey === 'all' ? '' : $mKey ?>" class="px-2.5 py-1 rounded-lg text-xs font-medium transition <?= ($filters['payment_method'] === $mKey || ($mKey === 'all' && empty($filters['payment_method']))) ? 'bg-brand-600 text-white font-semibold' : 'bg-dark-700 text-slate-400 hover:text-white' ?>">
                        <?= $mLabel ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Search Form -->
        <form method="GET" action="/admin/payments" class="flex flex-col sm:flex-row items-center gap-3">
            <input type="hidden" name="status" value="<?= e($currentStatus) ?>">
            <input type="hidden" name="method" value="<?= e($filters['payment_method'] ?? '') ?>">
            <div class="relative flex-1 w-full">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Search TrxID, sender phone, customer name, booking ref..." class="w-full bg-dark-900 border border-dark-600 rounded-xl pl-10 pr-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
                </button>
                <?php if (!empty($filters['search']) || !empty($filters['payment_method']) || !empty($currentStatus)): ?>
                    <a href="/admin/payments" class="p-2 text-slate-400 hover:text-white hover:bg-dark-700 rounded-xl transition" title="Clear Filters">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Payments List -->
    <?php if (empty($payments)): ?>
        <div class="bg-dark-800/60 border border-dark-700/60 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-dark-700 flex items-center justify-center mx-auto mb-4 text-slate-500">
                <i data-lucide="check-check" class="w-8 h-8 text-emerald-400"></i>
            </div>
            <h3 class="text-lg font-bold text-white mb-1">No payments match the filter</h3>
            <p class="text-sm text-slate-400">There are no records found matching your current query parameters.</p>
        </div>
    <?php else: ?>
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-dark-700 bg-dark-900/60 text-slate-400 uppercase tracking-wider font-semibold">
                            <th class="py-3.5 px-4">Transaction / Method</th>
                            <th class="py-3.5 px-4">Booking & Event</th>
                            <th class="py-3.5 px-4">Customer Details</th>
                            <th class="py-3.5 px-4">Amount</th>
                            <th class="py-3.5 px-4">Proof Screenshot</th>
                            <th class="py-3.5 px-4">Status & Review</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-700/60 text-slate-200">
                        <?php foreach ($payments as $p): ?>
                            <?php 
                            $methodBadge = match($p['payment_method']) {
                                'bkash' => 'bg-pink-950/60 border-pink-500/40 text-pink-300',
                                'nagad' => 'bg-orange-950/60 border-orange-500/40 text-orange-300',
                                'rocket' => 'bg-purple-950/60 border-purple-500/40 text-purple-300',
                                default => 'bg-dark-700 border-dark-600 text-slate-300'
                            };
                            ?>
                            <tr class="hover:bg-dark-700/40 transition">
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider border <?= $methodBadge ?>">
                                                <?= e($p['payment_method']) ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="font-mono font-bold text-white bg-dark-900 px-2 py-1 rounded border border-dark-600 select-all text-xs">
                                                <?= e($p['transaction_id']) ?>
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            From: <span class="font-mono text-slate-300"><?= e($p['sender_number']) ?></span>
                                        </div>
                                        <div class="text-[10px] text-slate-500">
                                            <?= date('M d, Y h:i A', strtotime($p['created_at'])) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1">
                                        <a href="/admin/bookings/<?= $p['booking_id'] ?>" class="font-mono font-semibold text-brand-400 hover:text-brand-300 hover:underline">
                                            <?= e($p['booking_reference']) ?>
                                        </a>
                                        <div class="text-xs font-medium text-white line-clamp-1 max-w-[200px]" title="<?= e($p['event_title']) ?>">
                                            <?= e($p['event_title']) ?>
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            Booking Total: <?= format_currency((float)$p['booking_total']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-0.5">
                                        <div class="font-medium text-white"><?= e($p['customer_name']) ?></div>
                                        <div class="text-[11px] text-slate-400"><?= e($p['customer_email']) ?></div>
                                        <div class="text-[11px] text-slate-400 font-mono"><?= e($p['customer_phone'] ?: 'N/A') ?></div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="text-sm font-bold font-mono text-emerald-400">
                                        <?= format_currency((float)$p['amount']) ?>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <?php if (!empty($p['proof_screenshot'])): ?>
                                        <button type="button" onclick="viewProof('<?= asset($p['proof_screenshot']) ?>', '<?= e($p['transaction_id']) ?>')" class="group relative block w-16 h-16 rounded-xl overflow-hidden border border-dark-600 bg-dark-900 shadow hover:border-brand-500 transition">
                                            <img src="<?= asset($p['proof_screenshot']) ?>" alt="Proof" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                                <i data-lucide="maximize-2" class="w-4 h-4"></i>
                                            </div>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-[11px] text-slate-500 italic">No screenshot</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <?php if ($p['status'] === 'pending'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                            Pending Review
                                        </span>
                                    <?php elseif ($p['status'] === 'approved'): ?>
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                Approved
                                            </span>
                                            <?php if (!empty($p['reviewer_name'])): ?>
                                                <div class="text-[10px] text-slate-400">
                                                    by <?= e($p['reviewer_name']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($p['reviewed_at'])): ?>
                                                <div class="text-[10px] text-slate-500">
                                                    <?= date('M d, h:i A', strtotime($p['reviewed_at'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif ($p['status'] === 'rejected'): ?>
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                Rejected
                                            </span>
                                            <?php if (!empty($p['rejection_reason'])): ?>
                                                <div class="text-[10px] text-rose-300 max-w-[180px] bg-rose-950/40 p-1.5 rounded border border-rose-800/40 mt-1">
                                                    <?= e($p['rejection_reason']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($p['reviewer_name'])): ?>
                                                <div class="text-[10px] text-slate-400">
                                                    by <?= e($p['reviewer_name']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 align-top text-right">
                                    <?php if ($p['status'] === 'pending'): ?>
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Approve Form -->
                                            <form method="POST" action="/admin/payments/<?= $p['id'] ?>/approve" onsubmit="return confirm('Approve payment of <?= format_currency((float)$p['amount']) ?> for booking <?= e($p['booking_reference']) ?>? Digital tickets will be generated immediately.')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-lg shadow-emerald-600/20">
                                                    <i data-lucide="check" class="w-3.5 h-3.5"></i> Approve
                                                </button>
                                            </form>

                                            <!-- Reject Button -->
                                            <button type="button" onclick="openRejectModal('<?= $p['id'] ?>', '<?= e($p['transaction_id']) ?>', '<?= format_currency((float)$p['amount']) ?>')" class="px-3 py-1.5 bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                                                <i data-lucide="x" class="w-3.5 h-3.5"></i> Reject
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <a href="/admin/bookings/<?= $p['booking_id'] ?>" class="px-3 py-1.5 bg-dark-700 hover:bg-dark-600 text-slate-300 rounded-lg text-xs font-medium transition inline-flex items-center gap-1">
                                            View Order <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Screenshot Proof Preview Modal -->
<div id="proof-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeProofModal()">
    <div class="relative max-w-2xl w-full bg-dark-900 border border-dark-600 rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between p-4 border-b border-dark-700 bg-dark-800">
            <h3 class="font-bold text-white text-sm flex items-center gap-2">
                <i data-lucide="file-check" class="w-4 h-4 text-brand-400"></i>
                Payment Proof Screenshot: <span id="proof-trx-id" class="font-mono text-brand-300"></span>
            </h3>
            <button type="button" onclick="closeProofModal()" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-dark-700 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-4 flex items-center justify-center bg-black/50 max-h-[75vh] overflow-auto">
            <img id="proof-img" src="" alt="Proof" class="max-w-full max-h-[70vh] rounded-lg shadow-lg object-contain">
        </div>
        <div class="p-3 border-t border-dark-700 bg-dark-800 flex justify-end">
            <button type="button" onclick="closeProofModal()" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-white rounded-xl text-xs font-semibold">
                Close Preview
            </button>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div id="reject-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeRejectModal()">
    <div class="relative max-w-md w-full bg-dark-900 border border-rose-500/30 rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <form id="reject-form" method="POST" action="">
            <?= csrf_field() ?>
            <div class="p-5 border-b border-dark-700 bg-dark-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-white text-base">Reject Payment Submission</h3>
                    <p class="text-xs text-slate-400">TrxID: <span id="reject-trx-id" class="font-mono text-white"></span></p>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <p class="text-xs text-slate-300 leading-relaxed">
                    Rejecting this payment will cancel booking order and restore ticket quantity to event tier inventory immediately.
                </p>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Reason for Rejection *</label>
                    <textarea name="rejection_reason" rows="3" required class="w-full bg-dark-800 border border-dark-600 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500" placeholder="e.g. Transaction ID was not found in the mobile statement, or the sender number did not match.">Transaction ID could not be verified with provider statement or amount did not match.</textarea>
                </div>
            </div>
            <div class="p-4 border-t border-dark-700 bg-dark-800 flex items-center justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-slate-300 rounded-xl text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-rose-600/20">
                    <i data-lucide="x" class="w-4 h-4"></i> Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function viewProof(url, trxId) {
    document.getElementById('proof-img').src = url;
    document.getElementById('proof-trx-id').textContent = trxId;
    document.getElementById('proof-modal').classList.remove('hidden');
}

function closeProofModal() {
    document.getElementById('proof-modal').classList.add('hidden');
    document.getElementById('proof-img').src = '';
}

function openRejectModal(paymentId, trxId, amount) {
    document.getElementById('reject-form').action = '/admin/payments/' + paymentId + '/reject';
    document.getElementById('reject-trx-id').textContent = trxId + ' (' + amount + ')';
    document.getElementById('reject-modal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
