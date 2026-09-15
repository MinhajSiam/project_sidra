<?php $pageTitle = 'Audit Logs'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-heading text-white flex items-center gap-2.5">
                <i data-lucide="activity" class="w-7 h-7 text-brand-400"></i>
                Platform Audit Trail
            </h1>
            <p class="text-sm text-slate-400 mt-1">Immutable security log of administrative actions, approvals, ticket releases, and permission edits.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-dark-800 text-slate-300 border border-dark-600">
                Latest <?= count($logs) ?> Events
            </span>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-dark-800/80 border border-dark-600/60 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-dark-700 bg-dark-900/60 text-slate-400 uppercase tracking-wider font-semibold">
                        <th class="py-3.5 px-4">Timestamp</th>
                        <th class="py-3.5 px-4">Actor</th>
                        <th class="py-3.5 px-4">Action</th>
                        <th class="py-3.5 px-4">Entity</th>
                        <th class="py-3.5 px-4">IP Address</th>
                        <th class="py-3.5 px-4 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700/60 text-slate-200">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">No audit events recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $l): ?>
                            <tr class="hover:bg-dark-700/40 transition">
                                <td class="py-4 px-4 align-top text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                    <div><?= date('Y-m-d', strtotime($l['created_at'])) ?></div>
                                    <div class="text-slate-500 text-[10px]"><?= date('h:i:s A', strtotime($l['created_at'])) ?></div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <?php if (!empty($l['user_name'])): ?>
                                        <div class="space-y-0.5">
                                            <div class="font-semibold text-white"><?= e($l['user_name']) ?></div>
                                            <div class="text-[10px] text-brand-300 font-medium"><?= e($l['role_name'] ?: 'Staff') ?></div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-500 italic">System / CLI</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded font-mono font-bold text-[11px] bg-dark-900 border border-dark-600 text-brand-300">
                                        <?= e($l['action']) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <span class="text-slate-300 font-medium capitalize">
                                        <?= e($l['entity_type']) ?>
                                        <?php if ($l['entity_id']): ?>
                                            <span class="font-mono text-slate-500">#<?= $l['entity_id'] ?></span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4 align-top font-mono text-slate-400 text-[11px]">
                                    <?= e($l['ip_address'] ?: '127.0.0.1') ?>
                                </td>
                                <td class="py-4 px-4 align-top text-right">
                                    <?php if (!empty($l['new_values'])): ?>
                                        <button type="button" onclick="showPayload(<?= htmlspecialchars($l['new_values'], ENT_QUOTES, 'UTF-8') ?>)" class="text-brand-400 hover:text-brand-300 inline-flex items-center gap-1 font-semibold text-[11px]">
                                            <i data-lucide="code" class="w-3.5 h-3.5"></i> Payload
                                        </button>
                                    <?php else: ?>
                                        <span class="text-slate-600 text-[11px]">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payload Inspector Modal -->
<div id="payload-modal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4" onclick="closePayloadModal()">
    <div class="relative max-w-lg w-full bg-dark-900 border border-dark-600 rounded-2xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <div class="p-4 border-b border-dark-700 bg-dark-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-xs uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="code" class="w-4 h-4 text-brand-400"></i>
                Audit Log Payload
            </h3>
            <button type="button" onclick="closePayloadModal()" class="text-slate-400 hover:text-white">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="p-4 bg-black/40 max-h-96 overflow-auto">
            <pre id="payload-content" class="font-mono text-xs text-emerald-400 whitespace-pre-wrap"></pre>
        </div>
        <div class="p-3 border-t border-dark-700 bg-dark-800 flex justify-end">
            <button type="button" onclick="closePayloadModal()" class="px-4 py-1.5 bg-dark-700 hover:bg-dark-600 text-slate-200 rounded-xl text-xs font-semibold">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function showPayload(data) {
    document.getElementById('payload-content').textContent = JSON.stringify(data, null, 2);
    document.getElementById('payload-modal').classList.remove('hidden');
}
function closePayloadModal() {
    document.getElementById('payload-modal').classList.add('hidden');
}
</script>
