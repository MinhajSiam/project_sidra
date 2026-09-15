<div class="py-16 max-w-md mx-auto px-4 space-y-6">
    <?php if ($ticket): ?>
        <?php if ($ticket['status'] === 'valid'): ?>
            <div class="glass-card rounded-3xl p-8 text-center space-y-4 border-2 border-emerald-500/40 shadow-2xl bg-emerald-950/20">
                <div class="w-16 h-16 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/20">
                    <i data-lucide="check-circle" class="w-8 h-8"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold tracking-widest uppercase text-emerald-400">OFFICIAL VERIFICATION</span>
                    <h1 class="font-heading font-extrabold text-2xl text-white mt-1">GENUINE VALID TICKET</h1>
                </div>
                <div class="pt-4 border-t border-slate-800 space-y-2 text-xs">
                    <div class="font-bold text-white text-base"><?= e($ticket['event_title']) ?></div>
                    <div class="text-slate-300 font-semibold"><?= e($ticket['ticket_type_name']) ?> Pass</div>
                    <div class="text-slate-400">Attendee: <strong class="text-white"><?= e($ticket['attendee_name']) ?></strong></div>
                    <div class="text-slate-400">Date: <?= format_date($ticket['event_date']) ?></div>
                    <div class="text-slate-400">Venue: <?= e($ticket['venue_name']) ?></div>
                    <div class="font-mono text-brand-400 text-xs mt-2"><?= e($ticket['ticket_code']) ?></div>
                </div>
            </div>
        <?php elseif ($ticket['status'] === 'used'): ?>
            <div class="glass-card rounded-3xl p-8 text-center space-y-4 border-2 border-rose-500/40 shadow-2xl bg-rose-950/20">
                <div class="w-16 h-16 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center mx-auto shadow-lg shadow-rose-500/20">
                    <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold tracking-widest uppercase text-rose-400">ADMISSION RECORD</span>
                    <h1 class="font-heading font-extrabold text-2xl text-white mt-1">ALREADY CHECKED IN</h1>
                </div>
                <div class="pt-4 border-t border-slate-800 space-y-2 text-xs">
                    <p class="text-rose-300">This ticket has already been used for gate entry. Duplicate entry is not permitted.</p>
                    <div class="font-bold text-white"><?= e($ticket['event_title']) ?></div>
                    <div class="text-slate-400">Attendee: <?= e($ticket['attendee_name']) ?></div>
                    <div class="font-mono text-rose-400 text-xs mt-2"><?= e($ticket['ticket_code']) ?></div>
                </div>
            </div>
        <?php else: ?>
            <div class="glass-card rounded-3xl p-8 text-center space-y-4 border-2 border-amber-500/40 shadow-2xl bg-amber-950/20">
                <div class="w-16 h-16 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center justify-center mx-auto shadow-lg">
                    <i data-lucide="x-circle" class="w-8 h-8"></i>
                </div>
                <h1 class="font-heading font-extrabold text-2xl text-white">CANCELLED PASS</h1>
                <p class="text-xs text-amber-300">This ticket is marked as cancelled or refunded.</p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="glass-card rounded-3xl p-8 text-center space-y-4 border-2 border-rose-500/40 shadow-2xl bg-rose-950/20">
            <div class="w-16 h-16 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center mx-auto shadow-lg shadow-rose-500/20">
                <i data-lucide="shield-alert" class="w-8 h-8"></i>
            </div>
            <h1 class="font-heading font-extrabold text-2xl text-white">INVALID TICKET</h1>
            <p class="text-xs text-rose-300">No matching ticket record could be found in Sidra's database. This pass may be forged or illegitimate.</p>
        </div>
    <?php endif; ?>

    <div class="text-center">
        <a href="<?= url('/') ?>" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white font-semibold">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Sidra Platform
        </a>
    </div>
</div>
