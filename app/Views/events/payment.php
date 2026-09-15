<div class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <!-- Progress Indicator -->
    <div class="flex items-center justify-between max-w-md mx-auto mb-8 text-xs font-bold">
        <div class="flex items-center gap-2 text-brand-400">
            <span class="w-6 h-6 rounded-full bg-brand-600/20 text-brand-400 flex items-center justify-center border border-brand-500/30">1</span>
            Tickets
        </div>
        <div class="h-0.5 w-12 bg-brand-500"></div>
        <div class="flex items-center gap-2 text-brand-400">
            <span class="w-6 h-6 rounded-full bg-brand-600/20 text-brand-400 flex items-center justify-center border border-brand-500/30">2</span>
            Details
        </div>
        <div class="h-0.5 w-12 bg-brand-500"></div>
        <div class="flex items-center gap-2 text-brand-400">
            <span class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center shadow-md shadow-brand-500/30">3</span>
            Payment
        </div>
    </div>

    <!-- Booking Overview Card -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-brand-500/20">
        <div>
            <div class="text-[10px] uppercase font-bold text-brand-400 tracking-wider">Booking Reference</div>
            <div class="font-heading font-extrabold text-2xl text-white tracking-tight"><?= e($booking['booking_reference']) ?></div>
            <div class="text-xs text-slate-400 mt-1"><?= e($booking['event_title']) ?> • <?= e($booking['customer_name']) ?></div>
        </div>
        <div class="text-left sm:text-right">
            <div class="text-[10px] uppercase font-bold text-slate-400">Payable Amount</div>
            <div class="font-heading font-extrabold text-3xl text-white"><?= format_currency($booking['final_amount']) ?></div>
        </div>
    </div>

    <!-- Payment Methods Section -->
    <div class="glass-card rounded-3xl p-8 space-y-8">
        <div>
            <h2 class="font-heading font-bold text-2xl text-white">Select Manual Payment Method</h2>
            <p class="text-xs text-slate-400 mt-1">Send the exact amount from your mobile wallet, then enter your TrxID below for verification.</p>
        </div>

        <!-- Payment Method Tabs -->
        <div class="grid grid-cols-3 gap-4" id="method-selector">
            <?php foreach ($paymentMethods as $key => $method): ?>
                <button type="button" 
                        onclick="selectPaymentMethod('<?= $key ?>')" 
                        id="tab-<?= $key ?>" 
                        class="method-tab p-4 rounded-2xl border text-center transition-all cursor-pointer <?= $key === 'bkash' ? 'bg-slate-800/90 border-brand-500 text-white shadow-lg' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white' ?>">
                    <div class="font-heading font-extrabold text-lg" style="color: <?= $method['brand_color'] ?>;">
                        <?= e($method['name']) ?>
                    </div>
                    <div class="text-[10px] text-slate-400 uppercase mt-0.5 font-semibold">Manual Send</div>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Dynamic Instructions Container -->
        <?php foreach ($paymentMethods as $key => $method): ?>
            <div id="instructions-<?= $key ?>" class="method-instructions p-6 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-4 <?= $key !== 'bkash' ? 'hidden' : '' ?>">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-800">
                    <div>
                        <div class="text-xs text-slate-400 font-semibold"><?= e($method['name']) ?> Account Number (<?= e($method['default_type']) ?>)</div>
                        <div class="font-heading font-extrabold text-2xl text-white tracking-wider flex items-center gap-3 mt-1">
                            <span id="copy-num-<?= $key ?>"><?= e($method['default_number']) ?></span>
                            <button type="button" onclick="navigator.clipboard.writeText('<?= e($method['default_number']) ?>'); alert('Copied <?= e($method['default_number']) ?> to clipboard!');" class="text-xs bg-slate-800 hover:bg-slate-700 text-brand-400 px-3 py-1 rounded-lg border border-slate-700">
                                Copy
                            </button>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400 font-semibold">Required Reference</div>
                        <div class="font-mono text-sm font-bold text-amber-400 bg-amber-950/40 border border-amber-500/30 px-3 py-1.5 rounded-lg mt-1">
                            <?= e($booking['booking_reference']) ?>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Step-by-Step Payment Instructions:</div>
                    <div class="text-xs text-slate-300 leading-relaxed whitespace-pre-line font-mono bg-slate-950/60 p-4 rounded-xl border border-slate-800">
                        <?= e($method['instructions']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Payment Submission Form -->
        <form action="<?= url("booking/{$booking['booking_reference']}/payment") ?>" method="POST" enctype="multipart/form-data" class="space-y-6 pt-4 border-t border-slate-800">
            <?= csrf_field() ?>
            <input type="hidden" name="payment_method" id="selected-payment-method" value="bkash">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Your Sender Mobile Number *</label>
                    <input type="text" name="sender_number" required placeholder="The bKash/Nagad/Rocket number you sent from" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Transaction ID (TrxID) *</label>
                    <input type="text" name="transaction_id" required placeholder="e.g. BL92KJ78X0" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white font-mono uppercase focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Payment Screenshot Proof (Optional)</label>
                <input type="file" name="proof_image" accept="image/png, image/jpeg, image/webp" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-xs text-slate-400 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-500 cursor-pointer">
                <span class="text-[11px] text-slate-500 mt-1">Upload receipt or SMS screenshot (JPG, PNG, WebP up to 5MB).</span>
            </div>

            <button type="submit" class="w-full py-4 rounded-xl gradient-brand text-white font-bold text-sm shadow-xl shadow-brand-600/30 hover:opacity-95 transition-all">
                Submit Payment for Verification
            </button>
        </form>
    </div>
</div>

<script>
function selectPaymentMethod(methodKey) {
    document.getElementById('selected-payment-method').value = methodKey;

    // Toggle tabs
    document.querySelectorAll('.method-tab').forEach(tab => {
        tab.classList.remove('bg-slate-800/90', 'border-brand-500', 'text-white', 'shadow-lg');
        tab.classList.add('bg-slate-900', 'border-slate-800', 'text-slate-400');
    });
    const activeTab = document.getElementById('tab-' + methodKey);
    activeTab.classList.remove('bg-slate-900', 'border-slate-800', 'text-slate-400');
    activeTab.classList.add('bg-slate-800/90', 'border-brand-500', 'text-white', 'shadow-lg');

    // Toggle instructions
    document.querySelectorAll('.method-instructions').forEach(inst => inst.classList.add('hidden'));
    document.getElementById('instructions-' + methodKey).classList.remove('hidden');
}
</script>
