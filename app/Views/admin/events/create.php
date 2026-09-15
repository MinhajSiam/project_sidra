<div class="py-6 max-w-4xl mx-auto space-y-8">
    <div class="flex items-center justify-between pb-6 border-b border-slate-800">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-white">Create New Event</h1>
            <p class="text-xs text-slate-400 mt-1">Configure event details, venue, dates, and initial ticket tiers.</p>
        </div>
        <a href="<?= url('admin/events') ?>" class="text-xs text-slate-400 hover:text-white font-bold">
            &larr; Back to Events
        </a>
    </div>

    <form action="<?= url('admin/events/store') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
        <?= csrf_field() ?>

        <!-- Event Basics -->
        <div class="glass-card rounded-3xl p-8 space-y-6">
            <h2 class="font-heading font-bold text-lg text-white">General Information</h2>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Event Title *</label>
                <input type="text" name="title" required placeholder="e.g. Bangladesh Tech Expo 2026" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Event Category *</label>
                    <select name="category_id" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Event Venue *</label>
                    <select name="venue_id" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                        <?php foreach ($venues as $v): ?>
                            <option value="<?= $v['id'] ?>"><?= e($v['name']) ?> (<?= e($v['city']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Event Banner Image</label>
                <input type="file" name="banner_image" accept="image/*" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-xs text-slate-400 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-500 cursor-pointer">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Event Date *</label>
                    <input type="date" name="event_date" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Start Time *</label>
                    <input type="time" name="start_time" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">End Time *</label>
                    <input type="time" name="end_time" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Short Summary (Max 300 chars) *</label>
                <input type="text" name="summary" required maxlength="300" placeholder="A one-sentence overview displayed on event cards" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Full Description & Schedule *</label>
                <textarea name="description" rows="6" required placeholder="Detailed information, keynote speakers, parking, dress code..." class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500"></textarea>
            </div>

            <div class="flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 text-xs font-semibold text-white cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded bg-slate-900 border-slate-700 text-brand-600 focus:ring-0">
                    Feature on Homepage Hero
                </label>

                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold uppercase text-slate-400">Publish Status:</label>
                    <select name="status" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white">
                        <option value="draft">Save as Draft</option>
                        <option value="published" selected>Publish Immediately</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Initial Ticket Tiers -->
        <div class="glass-card rounded-3xl p-8 space-y-6">
            <h2 class="font-heading font-bold text-lg text-white">Initial Ticket Tiers</h2>
            <p class="text-xs text-slate-400">Set up passes (e.g. VIP, General Admission, Early Bird) with quotas.</p>

            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Tier Name</label>
                        <input type="text" name="tier_name[]" value="General Admission" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Price (BDT)</label>
                        <input type="number" name="tier_price[]" value="1000" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Total Quantity</label>
                        <input type="number" name="tier_quantity[]" value="500" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Max Per User</label>
                        <input type="number" name="tier_max_per_user[]" value="5" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Tier Name</label>
                        <input type="text" name="tier_name[]" value="VIP Pass" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Price (BDT)</label>
                        <input type="number" name="tier_price[]" value="3000" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Total Quantity</label>
                        <input type="number" name="tier_quantity[]" value="100" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Max Per User</label>
                        <input type="number" name="tier_max_per_user[]" value="2" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full py-4 rounded-xl gradient-brand text-white font-bold text-sm shadow-xl shadow-brand-600/30 hover:opacity-95 transition-all">
            Save & Launch Event
        </button>
    </form>
</div>
