<div class="py-6 max-w-4xl mx-auto space-y-8">
    <div class="flex items-center justify-between pb-6 border-b border-slate-800">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-white">Edit Event: <?= e($event['title']) ?></h1>
            <p class="text-xs text-slate-400 mt-1">Update schedule, venue, and ticket tier quotas.</p>
        </div>
        <a href="<?= url('admin/events') ?>" class="text-xs text-slate-400 hover:text-white font-bold">
            &larr; Back to Events
        </a>
    </div>

    <!-- Event Edit Form -->
    <form action="<?= url("admin/events/{$event['id']}/update") ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
        <?= csrf_field() ?>

        <div class="glass-card rounded-3xl p-8 space-y-6">
            <h2 class="font-heading font-bold text-lg text-white">Event Details</h2>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Event Title *</label>
                <input type="text" name="title" required value="<?= e($event['title']) ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Category *</label>
                    <select name="category_id" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $event['category_id'] === $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Venue *</label>
                    <select name="venue_id" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                        <?php foreach ($venues as $v): ?>
                            <option value="<?= $v['id'] ?>" <?= $event['venue_id'] === $v['id'] ? 'selected' : '' ?>><?= e($v['name']) ?> (<?= e($v['city']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Replace Banner Image</label>
                <?php if ($event['banner_image']): ?>
                    <div class="mb-3">
                        <img src="<?= upload_url($event['banner_image']) ?>" class="w-32 h-20 rounded-xl object-cover border border-slate-700">
                    </div>
                <?php endif; ?>
                <input type="file" name="banner_image" accept="image/*" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-xs text-slate-400 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-500 cursor-pointer">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Event Date *</label>
                    <input type="date" name="event_date" required value="<?= e($event['event_date']) ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Start Time *</label>
                    <input type="time" name="start_time" required value="<?= e($event['start_time']) ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">End Time *</label>
                    <input type="time" name="end_time" required value="<?= e($event['end_time']) ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Short Summary *</label>
                <input type="text" name="summary" required maxlength="300" value="<?= e($event['summary']) ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Description & Schedule *</label>
                <textarea name="description" rows="6" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500"><?= e($event['description']) ?></textarea>
            </div>

            <div class="flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 text-xs font-semibold text-white cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" <?= $event['is_featured'] ? 'checked' : '' ?> class="rounded bg-slate-900 border-slate-700 text-brand-600 focus:ring-0">
                    Featured Event
                </label>

                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold uppercase text-slate-400">Status:</label>
                    <select name="status" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white">
                        <option value="draft" <?= $event['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= $event['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="archived" <?= $event['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-xl hover:opacity-95 transition-all">
                Update Event Details
            </button>
        </div>
    </form>

    <!-- Existing Ticket Tiers Management -->
    <div class="glass-card rounded-3xl p-8 space-y-6">
        <h2 class="font-heading font-bold text-xl text-white">Ticket Inventory Tiers</h2>

        <div class="space-y-4">
            <?php foreach ($ticketTypes as $tier): ?>
                <form action="<?= url("admin/tickets/type/{$tier['id']}/update") ?>" method="POST" class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                    <?= csrf_field() ?>
                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Tier Name</label>
                            <input type="text" name="name" required value="<?= e($tier['name']) ?>" class="w-full px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Price (BDT)</label>
                            <input type="number" name="price" required value="<?= (float)$tier['price'] ?>" class="w-full px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Total Qty</label>
                            <input type="number" name="total_quantity" required value="<?= $tier['total_quantity'] ?>" class="w-full px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Remaining</label>
                            <input type="text" value="<?= $tier['remaining_quantity'] ?>" readonly class="w-full px-3 py-1.5 rounded-lg bg-slate-950/40 border border-slate-800 text-xs text-slate-400">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="text-xs text-slate-400">
                            Max per user: <input type="number" name="max_per_user" value="<?= $tier['max_per_user'] ?>" class="w-12 px-1.5 py-0.5 rounded bg-slate-950 border border-slate-800 text-xs text-white">
                        </div>
                        <div class="flex items-center gap-2">
                            <select name="status" class="px-2 py-1 rounded bg-slate-950 border border-slate-800 text-xs text-white">
                                <option value="active" <?= $tier['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $tier['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="sold_out" <?= $tier['status'] === 'sold_out' ? 'selected' : '' ?>>Sold Out</option>
                            </select>
                            <button type="submit" class="px-3 py-1 rounded-lg gradient-brand text-white font-bold text-xs">
                                Save Tier
                            </button>
                        </div>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>

        <!-- Add New Tier Accordion/Form -->
        <div class="pt-6 border-t border-slate-800">
            <h3 class="font-heading font-bold text-base text-white mb-3">Add Additional Ticket Tier</h3>
            <form action="<?= url('admin/tickets/type/store') ?>" method="POST" class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-4">
                <?= csrf_field() ?>
                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Tier Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Student Pass" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Price (BDT) *</label>
                        <input type="number" name="price" required placeholder="500" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Quantity *</label>
                        <input type="number" name="total_quantity" required placeholder="200" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Max Per User *</label>
                        <input type="number" name="max_per_user" value="2" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white">
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl gradient-brand text-white font-bold text-xs shadow">
                    Add Ticket Tier
                </button>
            </form>
        </div>
    </div>
</div>
