<?php $pageTitle = 'Staff & Role Management'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-white flex items-center gap-2.5">
                <i data-lucide="shield-check" class="w-7 h-7 text-brand-400"></i>
                Staff & Role Permissions
            </h1>
            <p class="text-sm text-slate-400 mt-1">Manage staff accounts with Role-Based Access Control (RBAC) across administrative functions.</p>
        </div>
        <div>
            <button type="button" onclick="openAddModal()" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow-lg shadow-brand-600/20">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Add Staff Member
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-dark-700 bg-dark-900/60 text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-4">Staff Member</th>
                        <th class="py-3.5 px-4">Assigned Role</th>
                        <th class="py-3.5 px-4">Phone</th>
                        <th class="py-3.5 px-4">Account Status</th>
                        <th class="py-3.5 px-4">Registered On</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700/60 text-slate-200">
                    <?php foreach ($users as $u): ?>
                        <?php
                        $roleColor = match((int)$u['role_id']) {
                            1 => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                            2 => 'bg-brand-500/10 text-brand-400 border-brand-500/30',
                            3 => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                            4 => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                            5 => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                            default => 'bg-dark-700 text-slate-400 border-dark-600'
                        };
                        ?>
                        <tr class="hover:bg-dark-700/40 transition">
                            <td class="py-4 px-4 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-600/20 text-brand-300 border border-brand-500/30 flex items-center justify-center font-bold text-xs uppercase">
                                        <?= strtoupper(substr($u['name'], 0, 2)) ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-white text-sm"><?= e($u['name']) ?></div>
                                        <div class="text-[11px] text-slate-400"><?= e($u['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 align-top">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide border <?= $roleColor ?>">
                                    <i data-lucide="shield" class="w-3 h-3"></i>
                                    <?= e($u['role_display']) ?>
                                </span>
                            </td>
                            <td class="py-4 px-4 align-top font-mono text-slate-300">
                                <?= e($u['phone'] ?: '—') ?>
                            </td>
                            <td class="py-4 px-4 align-top">
                                <?php if ($u['status'] === 'active'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        <i data-lucide="check" class="w-3 h-3"></i> Active
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                        Suspended
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-4 align-top text-slate-400 text-[11px]">
                                <?= date('M d, Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td class="py-4 px-4 align-top text-right">
                                <button type="button" onclick="openEditModal(<?= htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8') ?>)" class="px-3 py-1.5 bg-dark-700 hover:bg-dark-600 text-slate-300 hover:text-white rounded-lg text-xs font-semibold transition inline-flex items-center gap-1">
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

<!-- Add User Modal -->
<div id="add-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeAddModal()">
    <div class="relative max-w-md w-full bg-dark-900 border border-dark-600 rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <form method="POST" action="<?= url('admin/users/store') ?>">
            <?= csrf_field() ?>
            <div class="p-5 border-b border-dark-700 bg-dark-800 flex items-center justify-between">
                <h3 class="font-bold text-white text-base flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-5 h-5 text-brand-400"></i>
                    Add Staff Member
                </h3>
                <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Tariqul Islam" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address *</label>
                    <input type="email" name="email" required placeholder="staff@sidra.test" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number</label>
                    <input type="text" name="phone" placeholder="017XXXXXXXX" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Assigned Role *</label>
                    <select name="role_id" required class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= e($r['display_name']) ?> (<?= e($r['name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Password *</label>
                    <input type="password" name="password" required minlength="8" placeholder="Minimum 8 characters" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                </div>
            </div>
            <div class="p-4 border-t border-dark-700 bg-dark-800 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-slate-300 rounded-xl text-xs font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-brand-600/20">
                    <i data-lucide="check" class="w-4 h-4"></i> Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="edit-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closeEditModal()">
    <div class="relative max-w-md w-full bg-dark-900 border border-dark-600 rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <form id="edit-form" method="POST" action="">
            <?= csrf_field() ?>
            <div class="p-5 border-b border-dark-700 bg-dark-800 flex items-center justify-between">
                <h3 class="font-bold text-white text-base flex items-center gap-2">
                    <i data-lucide="edit" class="w-5 h-5 text-brand-400"></i>
                    Edit Staff Member
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name *</label>
                    <input type="text" id="edit_name" name="name" required class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address *</label>
                    <input type="email" id="edit_email" name="email" required class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number</label>
                    <input type="text" id="edit_phone" name="phone" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Assigned Role *</label>
                    <select id="edit_role_id" name="role_id" required class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= e($r['display_name']) ?> (<?= e($r['name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Account Status</label>
                    <select id="edit_status" name="status" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-brand-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive / Suspended</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">New Password (leave empty to keep current)</label>
                    <input type="password" name="password" minlength="8" placeholder="Enter new password if updating" class="w-full bg-dark-800 border border-dark-600 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
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
function openEditModal(u) {
    document.getElementById('edit-form').action = '/admin/users/' + u.id + '/update';
    document.getElementById('edit_name').value = u.name;
    document.getElementById('edit_email').value = u.email;
    document.getElementById('edit_phone').value = u.phone || '';
    document.getElementById('edit_role_id').value = u.role_id;
    document.getElementById('edit_status').value = u.status;
    document.getElementById('edit-modal').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}
</script>
