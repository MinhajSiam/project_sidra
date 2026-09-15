<div class="space-y-6">
    <div class="text-center">
        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center mx-auto mb-3 border border-amber-500/30">
            <i data-lucide="shield" class="w-5 h-5"></i>
        </div>
        <h2 class="font-heading font-extrabold text-2xl text-white">Staff Management Console</h2>
        <p class="text-xs text-slate-400 mt-1">Authorized personnel only (Admin, Finance, Gate Staff)</p>
    </div>

    <form action="<?= url('admin/login') ?>" method="POST" class="space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Staff Email Address *</label>
            <input type="email" name="email" required placeholder="admin@sidra.test" value="<?= old('email', 'admin@sidra.test') ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Staff Password *</label>
            <input type="password" name="password" required placeholder="••••••••" value="Password123!" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <button type="submit" class="w-full py-3.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-xl shadow-brand-600/30 hover:opacity-95 transition-all mt-2 flex items-center justify-center gap-2">
            <i data-lucide="key" class="w-4 h-4"></i>
            Authenticate Staff Session
        </button>
    </form>

    <div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-800 text-[11px] text-slate-400 space-y-1">
        <div class="font-bold text-slate-300">Quick Test Credentials (Pre-filled):</div>
        <div>Super Admin: <code class="text-amber-300">admin@sidra.test</code> / <code class="text-amber-300">Password123!</code></div>
        <div>Finance Manager: <code class="text-amber-300">finance@sidra.test</code> / <code class="text-amber-300">Password123!</code></div>
        <div>Gate Scanner: <code class="text-amber-300">gate@sidra.test</code> / <code class="text-amber-300">Password123!</code></div>
    </div>
</div>
