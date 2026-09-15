<?php $pageTitle = 'Event Categories'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-white flex items-center gap-2.5">
                <i data-lucide="tag" class="w-7 h-7 text-brand-400"></i>
                Event Categories
            </h1>
            <p class="text-sm text-slate-400 mt-1">Organize public events into searchable genres and topics.</p>
        </div>
        <div>
            <button type="button" onclick="openAddModal()" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow-lg shadow-brand-600/20">
                <i data-lucide="plus" class="w-4 h-4"></i> Add Category
            </button>
        </div>
    </div>

    <!-- Categories Grid / Table -->
    <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-dark-700 bg-dark-900/60 text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-4">Name & Slug</th>
                        <th class="py-3.5 px-4">Description</th>
                        <th class="py-3.5 px-4">Icon Key</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700/60 text-slate-200">
                    <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-dark-700/40 transition">
                            <td class="py-4 px-4 align-top">
                                <div class="space-y-0.5">
                                    <div class="font-bold text-white text-sm"><?= e($cat['name']) ?></div>
                                    <div class="font-mono text-slate-400 text-[11px]"><?= e($cat['slug']) ?></div>
                                </div>
                            </td>
                            <td class="py-4 px-4 align-top text-slate-300 max-w-xs">
                                <?= e($cat['description'] ?: '—') ?>
                            </td>
                            <td class="py-4 px-4 align-top">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-dark-900 border border-dark-600 font-mono text-brand-300 text-[11px]">
                                    <i data-lucide="<?= e($cat['icon'] ?: 'folder') ?>" class="w-3.5 h-3.5"></i>
                                    <?= e($cat['icon'] ?: 'folder') ?>
                                </span>
                            </td>
                            <td class="py-4 px-4 align-top">
                                <?php if ($cat['is_active']): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        <i data-lucide="check" class="w-3 h-3"></i> Active
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-dark-700 text-slate-400 border border-dark-600">
                                        Inactive
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-4 align-top text-right">
                                <button type="button" onclick="openEditModal(<?= htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8') ?>)" class="px-3 py-1.5 bg-dark-700 hover:bg-dark-600 text-slate-300 hover:text-white rounded-lg text-xs font-semibold transition inline-flex items-center gap-1">
                                    <i data-lucide="edit" class="w-3.5 h-3.5"></i> Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div id="add-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeAddModal()">
    <div class="relative max-w-md w-full bg-dark-900 border border-dark-600 rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <form method="POST" action="<?= url('admin/categories/store') ?>">
            <?= csrf_field() ?>
            <div class="p-5 border-b border-dark-700 bg-dark-800 flex items-center justify-between">
                <h3 class="font-bold text-white text-base flex items-center gap-2">
                    <i data-lucide="tag" class="w-5 h-5 text-brand-400"></i>
                    Add New Category
                </h3>
                <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Technology & Innovation" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Lucide Icon Name</label>
                    <input type="text" name="icon" value="calendar" placeholder="e.g. cpu, music, briefcases, mic" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="Brief summary of events in this category..." class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="add_active" value="1" checked class="rounded border-dark-600 bg-dark-800 text-brand-600 focus:ring-brand-500 w-4 h-4">
                    <label for="add_active" class="text-xs text-slate-300 font-medium">Visible on public event filters</label>
                </div>
            </div>
            <div class="p-4 border-t border-dark-700 bg-dark-800 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-slate-300 rounded-xl text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-brand-600/20">
                    <i data-lucide="check" class="w-4 h-4"></i> Create Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="edit-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeEditModal()">
    <div class="relative max-w-md w-full bg-dark-900 border border-dark-600 rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <form id="edit-form" method="POST" action="">
            <?= csrf_field() ?>
            <div class="p-5 border-b border-dark-700 bg-dark-800 flex items-center justify-between">
                <h3 class="font-bold text-white text-base flex items-center gap-2">
                    <i data-lucide="edit" class="w-5 h-5 text-brand-400"></i>
                    Edit Category
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Category Name *</label>
                    <input type="text" id="edit_name" name="name" required class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Lucide Icon Name</label>
                    <input type="text" id="edit_icon" name="icon" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                    <textarea id="edit_description" name="description" rows="3" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="edit_active" value="1" class="rounded border-dark-600 bg-dark-800 text-brand-600 focus:ring-brand-500 w-4 h-4">
                    <label for="edit_active" class="text-xs text-slate-300 font-medium">Visible on public event filters</label>
                </div>
            </div>
            <div class="p-4 border-t border-dark-700 bg-dark-800 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-slate-300 rounded-xl text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-brand-600/20">
                    <i data-lucide="check" class="w-4 h-4"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('add-modal').classList.remove('hidden');
}
function closeAddModal() {
    document.getElementById('add-modal').classList.add('hidden');
}
function openEditModal(cat) {
    document.getElementById('edit-form').action = '/admin/categories/' + cat.id + '/update';
    document.getElementById('edit_name').value = cat.name;
    document.getElementById('edit_icon').value = cat.icon || 'folder';
    document.getElementById('edit_description').value = cat.description || '';
    document.getElementById('edit_active').checked = !!parseInt(cat.is_active);
    document.getElementById('edit-modal').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}
</script>
