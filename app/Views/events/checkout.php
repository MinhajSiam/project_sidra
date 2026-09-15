<div class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    <!-- Progress Indicator -->
    <div class="flex items-center justify-between max-w-md mx-auto mb-8 text-xs font-bold">
        <div class="flex items-center gap-2 text-brand-400">
            <span class="w-6 h-6 rounded-full bg-brand-600/20 text-brand-400 flex items-center justify-center border border-brand-500/30">1</span>
            Tickets
        </div>
        <div class="h-0.5 w-12 bg-brand-500"></div>
        <div class="flex items-center gap-2 text-brand-400">
            <span class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center shadow-md shadow-brand-500/30">2</span>
            Details
        </div>
        <div class="h-0.5 w-12 bg-slate-800"></div>
        <div class="flex items-center gap-2 text-slate-500">
            <span class="w-6 h-6 rounded-full bg-slate-800 flex items-center justify-center">3</span>
            Payment
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
        <!-- Left: Attendee Form (3 cols) -->
        <div class="md:col-span-3 space-y-6">
            <div class="glass-card rounded-3xl p-8 space-y-6">
                <div>
                    <h1 class="font-heading font-extrabold text-2xl text-white">Attendee Information</h1>
                    <p class="text-xs text-slate-400 mt-1">Please enter the attendee name and phone number for entry gate verification.</p>
                </div>

                <form action="<?= url('booking/store') ?>" method="POST" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">

                    <?php foreach ($items as $item): ?>
                        <input type="hidden" name="tickets[<?= $item['ticket_type']['id'] ?>]" value="<?= $item['quantity'] ?>">
                    <?php endforeach; ?>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Primary Attendee Full Name *</label>
                        <input type="text" name="name" required value="<?= e($customer['name'] ?? old('name')) ?>" placeholder="e.g. Tanvir Ahmed" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Email Address (For digital passes) *</label>
                        <input type="email" name="email" required value="<?= e($customer['email'] ?? old('email')) ?>" placeholder="e.g. tanvir@gmail.com" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Mobile Phone Number (For SMS verification) *</label>
                        <input type="text" name="phone" required value="<?= e($customer['phone'] ?? old('phone')) ?>" placeholder="e.g. 01711000000" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                        <span class="text-[11px] text-slate-500 mt-1">Bangladeshi mobile number (11 digits).</span>
                    </div>

                    <div class="pt-4 border-t border-slate-800">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" required class="mt-1 rounded bg-slate-900 border-slate-700 text-brand-600 focus:ring-0">
                            <span class="text-xs text-slate-400">
                                I confirm that attendee information is correct, and I agree to Sidra's <a href="<?= url('terms') ?>" target="_blank" class="text-brand-400 underline">Terms of Service</a> and gate admission rules.
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-xl gradient-brand text-white font-bold text-sm shadow-xl shadow-brand-600/30 hover:opacity-95 transition-all mt-4">
                        Confirm Reservation & Proceed to Payment
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Order Summary (2 cols) -->
        <div class="md:col-span-2 space-y-6">
            <div class="glass-card rounded-3xl p-6 space-y-6 border border-slate-800">
                <h3 class="font-heading font-bold text-lg text-white border-b border-slate-800 pb-3">Order Summary</h3>

                <div class="space-y-3">
                    <div class="text-xs font-semibold text-brand-400"><?= e($event['title']) ?></div>
                    <div class="text-xs text-slate-400">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1"></i>
                        <?= format_date($event['event_date']) ?>
                    </div>
                    <div class="text-xs text-slate-400">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 inline mr-1"></i>
                        <?= e($event['venue_name']) ?>
                    </div>
                </div>

                <!-- Ticket Tiers Itemized List -->
                <div class="space-y-2.5 pt-3 border-t border-slate-800">
                    <?php foreach ($items as $item): ?>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-300">
                                <?= e($item['ticket_type']['name']) ?> &times; <?= $item['quantity'] ?>
                            </span>
                            <span class="font-bold text-white"><?= format_currency($item['subtotal']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase text-slate-400">Total Due</span>
                    <span class="font-heading font-extrabold text-2xl text-white"><?= format_currency($totalAmount) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
