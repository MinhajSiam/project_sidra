<?php $pageTitle = 'Platform Settings'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-white flex items-center gap-2.5">
                <i data-lucide="settings" class="w-7 h-7 text-brand-400"></i>
                Platform Configuration
            </h1>
            <p class="text-sm text-slate-400 mt-1">Configure receiving mobile numbers (bKash/Nagad/Rocket), payment guidelines, and ticket terms.</p>
        </div>
    </div>

    <!-- Settings Form -->
    <form method="POST" action="<?= url('admin/settings/update') ?>" class="space-y-6">
        <?= csrf_field() ?>

        <!-- 1. General Branding -->
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-6 shadow-xl backdrop-blur-sm space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 border-b border-dark-700 pb-3">
                <i data-lucide="globe" class="w-4 h-4 text-brand-400"></i>
                General Platform & Branding
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Platform Brand Name</label>
                    <input type="text" name="settings[platform_name]" value="<?= e(\App\Models\Setting::get('platform_name', 'Sidra Event Ticketing Platform')) ?>" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Platform Tagline</label>
                    <input type="text" name="settings[platform_tagline]" value="<?= e(\App\Models\Setting::get('platform_tagline', 'Discover, Book & Verify Digital Tickets with Absolute Confidence')) ?>" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Customer Support Email</label>
                    <input type="email" name="settings[contact_email]" value="<?= e(\App\Models\Setting::get('contact_email', 'support@sidra.test')) ?>" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Customer Support Helpline</label>
                    <input type="text" name="settings[contact_phone]" value="<?= e(\App\Models\Setting::get('contact_phone', '+880 9612-888999')) ?>" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
            </div>
        </div>

        <!-- 2. Manual Mobile Payments Configuration -->
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-6 shadow-xl backdrop-blur-sm space-y-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 border-b border-dark-700 pb-3">
                <i data-lucide="smartphone" class="w-4 h-4 text-brand-400"></i>
                Manual Payment Receiving Accounts (MFS)
            </h3>

            <!-- bKash Box -->
            <div class="border border-pink-500/20 bg-pink-950/10 rounded-xl p-4 space-y-3">
                <div class="flex items-center gap-2 font-bold text-pink-400 text-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span>
                    bKash Account Details
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Receiving Phone Number</label>
                        <input type="text" name="settings[bkash_number]" value="<?= e(\App\Models\Setting::get('bkash_number', '01711-223344')) ?>" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white font-mono focus:outline-none focus:border-pink-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Account Type</label>
                        <select name="settings[bkash_type]" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-pink-500">
                            <option value="Merchant" <?= \App\Models\Setting::get('bkash_type') === 'Merchant' ? 'selected' : '' ?>>Merchant (Make Payment)</option>
                            <option value="Personal" <?= \App\Models\Setting::get('bkash_type') === 'Personal' ? 'selected' : '' ?>>Personal (Send Money)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Checkout Payment Instructions</label>
                        <textarea name="settings[bkash_instructions]" rows="2" class="w-full bg-dark-900 border border-dark-600 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-pink-500"><?= e(\App\Models\Setting::get('bkash_instructions', '')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Nagad Box -->
            <div class="border border-orange-500/20 bg-orange-950/10 rounded-xl p-4 space-y-3">
                <div class="flex items-center gap-2 font-bold text-orange-400 text-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                    Nagad Account Details
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Receiving Phone Number</label>
                        <input type="text" name="settings[nagad_number]" value="<?= e(\App\Models\Setting::get('nagad_number', '01811-223344')) ?>" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white font-mono focus:outline-none focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Account Type</label>
                        <select name="settings[nagad_type]" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-orange-500">
                            <option value="Merchant" <?= \App\Models\Setting::get('nagad_type') === 'Merchant' ? 'selected' : '' ?>>Merchant (Merchant Pay)</option>
                            <option value="Personal" <?= \App\Models\Setting::get('nagad_type') === 'Personal' ? 'selected' : '' ?>>Personal (Send Money)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Checkout Payment Instructions</label>
                        <textarea name="settings[nagad_instructions]" rows="2" class="w-full bg-dark-900 border border-dark-600 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-orange-500"><?= e(\App\Models\Setting::get('nagad_instructions', '')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Rocket Box -->
            <div class="border border-purple-500/20 bg-purple-950/10 rounded-xl p-4 space-y-3">
                <div class="flex items-center gap-2 font-bold text-purple-400 text-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                    Rocket Account Details
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Receiving Phone Number (with 12th check digit)</label>
                        <input type="text" name="settings[rocket_number]" value="<?= e(\App\Models\Setting::get('rocket_number', '01911-223344-5')) ?>" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Account Type</label>
                        <select name="settings[rocket_type]" class="w-full bg-dark-900 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                            <option value="Merchant" <?= \App\Models\Setting::get('rocket_type') === 'Merchant' ? 'selected' : '' ?>>Merchant (Merchant Pay)</option>
                            <option value="Personal" <?= \App\Models\Setting::get('rocket_type') === 'Personal' ? 'selected' : '' ?>>Personal (Send Money)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Checkout Payment Instructions</label>
                        <textarea name="settings[rocket_instructions]" rows="2" class="w-full bg-dark-900 border border-dark-600 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-purple-500"><?= e(\App\Models\Setting::get('rocket_instructions', '')) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Ticket Terms & Conditions -->
        <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl p-6 shadow-xl backdrop-blur-sm space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 border-b border-dark-700 pb-3">
                <i data-lucide="file-text" class="w-4 h-4 text-brand-400"></i>
                Ticket Admission Terms & Conditions
            </h3>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Default Terms printed on Digital & PDF Tickets</label>
                <textarea name="settings[ticket_terms]" rows="4" class="w-full bg-dark-900 border border-dark-600 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-brand-500 leading-relaxed"><?= e(\App\Models\Setting::get('ticket_terms', '')) ?></textarea>
                <p class="text-[11px] text-slate-500 mt-1">Printed on the bottom security stub of all customer passes and QR PDF prints.</p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="px-6 py-3 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-brand-600/30">
                <i data-lucide="save" class="w-4 h-4"></i> Save All Platform Settings
            </button>
        </div>
    </form>
</div>
