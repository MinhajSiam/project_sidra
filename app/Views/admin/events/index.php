<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-white">Events Management</h1>
            <p class="text-xs text-slate-400 mt-1">Publish, edit, and organize events and ticket inventory.</p>
        </div>
        <a href="<?= url('admin/events/create') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl gradient-brand text-white font-bold text-xs shadow-lg hover:opacity-95">
            <i data-lucide="plus" class="w-4 h-4"></i> Create New Event
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="glass-card rounded-2xl p-4 flex flex-wrap items-center gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-3 w-full">
            <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Search event title or venue..." class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-xs text-white focus:outline-none focus:border-brand-500 min-w-[240px]">
            <select name="status" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-xs text-white">
                <option value="">All Statuses</option>
                <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="archived" <?= ($filters['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl gradient-brand text-white text-xs font-bold shadow">Filter</button>
            <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
                <a href="<?= url('admin/events') ?>" class="px-3 py-2 rounded-xl bg-slate-800 text-xs text-slate-400 hover:text-white">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Events List Table -->
    <div class="glass-card rounded-3xl overflow-hidden border border-slate-800">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900/90 text-slate-400 uppercase font-semibold border-b border-slate-800">
                <tr>
                    <th class="px-6 py-4">Event Details</th>
                    <th class="px-6 py-4">Category</th>
                    <th class="px-6 py-4">Date & Time</th>
                    <th class="px-6 py-4">Venue</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">No events found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $ev): ?>
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <img src="<?= upload_url($ev['banner_image']) ?>" class="w-12 h-12 rounded-xl object-cover border border-slate-700">
                                <div>
                                    <div class="font-bold text-white text-sm"><?= e($ev['title']) ?></div>
                                    <div class="text-slate-400 text-[11px]"><?= e($ev['slug']) ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-300"><?= e($ev['category_name']) ?></td>
                            <td class="px-6 py-4 text-slate-300">
                                <div><?= format_date($ev['event_date']) ?></div>
                                <div class="text-[11px] text-slate-500"><?= format_time($ev['start_time']) ?> - <?= format_time($ev['end_time']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                <div class="truncate max-w-[160px]"><?= e($ev['venue_name']) ?></div>
                                <div class="text-[11px] text-slate-500"><?= e($ev['venue_city']) ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full font-bold uppercase text-[10px] <?= $ev['status'] === 'published' ? 'badge-approved' : 'badge-pending' ?>">
                                    <?= e($ev['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <form action="<?= url("admin/events/{$ev['id']}/toggle") ?>" method="POST" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-xs font-semibold text-slate-400 hover:text-white" title="Toggle Publish">
                                        <?= $ev['status'] === 'published' ? 'Unpublish' : 'Publish' ?>
                                    </button>
                                </form>
                                <a href="<?= url("admin/events/{$ev['id']}/edit") ?>" class="text-brand-400 hover:underline font-bold">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
