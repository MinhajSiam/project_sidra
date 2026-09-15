<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between pb-6 border-b border-slate-800">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-white">My Digital Passes</h1>
            <p class="text-xs text-slate-400 mt-1">Each digital pass carries a unique cryptographic QR code for gate admission.</p>
        </div>
        <span class="text-xs font-bold text-slate-400">
            Total Passes: <span class="text-brand-400"><?= count($tickets) ?></span>
        </span>
    </div>

    <?php if (empty($tickets)): ?>
        <div class="glass-card rounded-2xl p-16 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-800 text-slate-500 flex items-center justify-center mx-auto">
                <i data-lucide="ticket" class="w-8 h-8"></i>
            </div>
            <h3 class="font-heading font-bold text-xl text-white">No Tickets Issued Yet</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">Once your payment is approved by finance, your verified tickets will appear here.</p>
            <a href="<?= url('events') ?>" class="inline-block px-5 py-2.5 rounded-xl gradient-brand text-white font-bold text-xs">
                Explore Events
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($tickets as $ticket): ?>
                <div class="glass-card rounded-3xl overflow-hidden border border-slate-800 flex flex-col justify-between group">
                    <div class="relative h-44 bg-slate-900 overflow-hidden">
                        <img src="<?= upload_url($ticket['event_banner']) ?>" alt="<?= e($ticket['event_title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-transparent to-transparent"></div>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?= $ticket['status'] === 'valid' ? 'bg-emerald-600 text-white shadow' : 'bg-slate-800 text-slate-400' ?>">
                                <?= $ticket['status'] === 'valid' ? 'Valid for Entry' : 'Checked In' ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-bold text-brand-400"><?= e($ticket['ticket_code']) ?></span>
                                <span class="text-xs font-bold text-white bg-slate-800 px-2 py-0.5 rounded-md border border-slate-700"><?= e($ticket['ticket_type_name']) ?></span>
                            </div>
                            <h3 class="font-heading font-bold text-lg text-white line-clamp-1"><?= e($ticket['event_title']) ?></h3>
                            <div class="text-xs text-slate-400 space-y-1">
                                <div><i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1 text-slate-500"></i> <?= format_date($ticket['event_date']) ?> • <?= format_time($ticket['start_time']) ?></div>
                                <div class="truncate"><i data-lucide="map-pin" class="w-3.5 h-3.5 inline mr-1 text-slate-500"></i> <?= e($ticket['venue_name']) ?>, <?= e($ticket['venue_city']) ?></div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-800 flex items-center gap-3">
                            <a href="<?= url("customer/tickets/{$ticket['ticket_code']}") ?>" class="flex-1 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-center text-xs font-bold text-white transition-colors flex items-center justify-center gap-1.5">
                                <i data-lucide="qr-code" class="w-4 h-4"></i> Show QR
                            </a>
                            <a href="<?= url("customer/tickets/{$ticket['ticket_code']}/print") ?>" target="_blank" class="px-4 py-2.5 rounded-xl gradient-brand hover:opacity-90 text-center text-xs font-bold text-white transition-opacity flex items-center justify-center gap-1.5" title="Print PDF">
                                <i data-lucide="printer" class="w-4 h-4"></i> Print Pass
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
