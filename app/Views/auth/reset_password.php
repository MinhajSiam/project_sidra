<div class="space-y-6">
    <div class="text-center">
        <h2 class="font-heading font-extrabold text-2xl text-white">Create New Password</h2>
        <p class="text-xs text-slate-400 mt-1">Choose a secure password for <?= e($email) ?></p>
    </div>

    <form action="<?= url('reset-password') ?>" method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <input type="hidden" name="email" value="<?= e($email) ?>">

        <div>
            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">New Password (Min 8 chars) *</label>
            <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Confirm New Password *</label>
            <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <button type="submit" class="w-full py-3.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-xl shadow-brand-600/30 hover:opacity-95 transition-all mt-2">
            Reset Password
        </button>
    </form>
</div>
