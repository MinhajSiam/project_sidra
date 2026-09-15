<?php $pageTitle = 'Venues Management'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-white flex items-center gap-2.5">
                <i data-lucide="map-pin" class="w-7 h-7 text-brand-400"></i>
                Venues & Locations
            </h1>
            <p class="text-sm text-slate-400 mt-1">Manage convention centers, auditoriums, stadiums, and hall capacities.</p>
        </div>
        <div>
            <button type="button" onclick="openAddModal()" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow-lg shadow-brand-600/20">
                <i data-lucide="plus" class="w-4 h-4"></i> Add Venue
            </button>
        </div>
    </div>

    <!-- Venues Table -->
    <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-dark-700 bg-dark-900/60 text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-4">Venue Name</th>
                        <th class="py-3.5 px-4">City & Address</th>
                        <th class="py-3.5 px-4">Capacity</th>
                        <th class="py-3.5 px-4">Contact Phone</th>
                        <th class="py-3.5 px-4">Google Map</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700/60 text-slate-200">
                    <?php foreach ($venues as $v): ?>
                        <tr class="hover:bg-dark-700/40 transition">
                            <td class="py-4 px-4 align-top">
                                <div class="font-bold text-white text-sm"><?= e($v['name']) ?></div>
                            </td>
                            <td class="py-4 px-4 align-top">
                                <div class="space-y-0.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-dark-900 text-brand-300 border border-dark-600">
                                        <?= e($v['city'] ?: 'Dhaka') ?>
                                    </span>
                                    <div class="text-slate-300 text-xs mt-1"><?= e($v['address']) ?></div>
                                </div>
                            </td>
                            <td class="py-4 px-4 align-top">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-dark-900 border border-dark-600 font-mono text-xs font-bold text-white">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <?= number_format((int)$v['capacity']) ?> guests
                                </span>
                            </td>
                            <td class="py-4 px-4 align-top font-mono text-slate-300">
                                <?= e($v['contact_phone'] ?: '—') ?>
                            </td>
                            <td class="py-4 px-4 align-top">
                                <?php if (!empty($v['map_url'])): ?>
                                    <a href="<?= e($v['map_url']) ?>" target="_blank" class="text-brand-400 hover:text-brand-300 inline-flex items-center gap-1">
                                        Open Map <i data-lucide="external-link" class="w-3 h-3"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-500 italic">No link</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-4 align-top text-right">
                                <button type="button" onclick="openEditModal(<?= htmlspecialchars(json_encode($v), ENT_QUOTES, 'UTF-8') ?>)" class="px-3 py-1.5 bg-dark-700 hover:bg-dark-600 text-slate-300 hover:text-white rounded-lg text-xs font-semibold transition inline-flex items-center gap-1">
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

<!-- Add Venue Modal -->
<div id="add-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeAddModal()">
    <div class="relative max-w-lg w-full bg-dark-900 border border-dark-600 rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <form method="POST" action="<?= url('admin/venues/store') ?>">
            <?= csrf_field() ?>
            <div class="p-5 border-b border-dark-700 bg-dark-800 flex items-center justify-between">
                <h3 class="font-bold text-white text-base flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-5 h-5 text-brand-400"></i>
                    Register New Venue
                </h3>
                <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Venue Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Bangabandhu International Conference Center (BICC)" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">City *</label>
                        <input type="text" name="city" required value="Dhaka" placeholder="e.g. Dhaka, Chittagong" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Max Capacity *</label>
                        <input type="number" name="capacity" required value="1000" min="1" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Street Address *</label>
                    <input type="text" name="address" required placeholder="e.g. Agargaon, Sher-e-Bangla Nagar, Dhaka-1207" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" placeholder="e.g. +880 1711 000000" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Google Maps URL</label>
                    <input type="url" name="map_url" placeholder="https://maps.google.com/..." class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
            </div>
            <div class="p-4 border-t border-dark-700 bg-dark-800 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-slate-300 rounded-xl text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-brand-600/20">
                    <i data-lucide="check" class="w-4 h-4"></i> Save Venue
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Venue Modal -->
<div id="edit-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeEditModal()">
    <div class="relative max-w-lg w-full bg-dark-900 border border-dark-600 rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <form id="edit-form" method="POST" action="">
            <?= csrf_field() ?>
            <div class="p-5 border-b border-dark-700 bg-dark-800 flex items-center justify-between">
                <h3 class="font-bold text-white text-base flex items-center gap-2">
                    <i data-lucide="edit" class="w-5 h-5 text-brand-400"></i>
                    Edit Venue
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Venue Name *</label>
                    <input type="text" id="edit_name" name="name" required class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">City *</label>
                        <input type="text" id="edit_city" name="city" required class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Max Capacity *</label>
                        <input type="number" id="edit_capacity" name="capacity" required min="1" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Street Address *</label>
                    <input type="text" id="edit_address" name="address" required class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Contact Phone</label>
                    <input type="text" id="edit_contact_phone" name="contact_phone" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Google Maps URL</label>
                    <input type="url" id="edit_map_url" name="map_url" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
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
function openEditModal(v) {
    document.getElementById('edit-form').action = '/admin/venues/' + v.id + '/update';
    document.getElementById('edit_name').value = v.name;
    document.getElementById('edit_city').value = v.city || 'Dhaka';
    document.getElementById('edit_capacity').value = v.capacity || 1000;
    document.getElementById('edit_address').value = v.address;
    document.getElementById('edit_contact_phone').value = v.contact_phone || '';
    document.getElementById('edit_map_url').value = v.map_url || '';
    document.getElementById('edit-modal').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}
</script>
