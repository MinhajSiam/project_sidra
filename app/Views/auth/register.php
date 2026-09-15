<div class="space-y-6">
    <div class="text-center">
        <h2 class="font-heading font-extrabold text-2xl text-white">Create Account</h2>
        <p class="text-xs text-slate-400 mt-1">Join Sidra to discover events and secure digital tickets</p>
    </div>

    <form action="<?= url('register') ?>" method="POST" class="space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Full Name *</label>
            <input type="text" name="name" required placeholder="e.g. Tanvir Ahmed" value="<?= old('name') ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Email Address *</label>
            <input type="email" name="email" required placeholder="e.g. tanvir@gmail.com" value="<?= old('email') ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Mobile Phone Number *</label>
            <input type="text" name="phone" required placeholder="e.g. 01711000000" value="<?= old('phone') ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Password (Min 8 characters) *</label>
            <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Confirm Password *</label>
            <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <button type="submit" class="w-full py-3.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-xl shadow-brand-600/30 hover:opacity-95 transition-all mt-2">
            Complete Registration
        </button>
    </form>

    <div class="text-center pt-4 border-t border-slate-800 text-xs text-slate-400">
        Already have an account? 
        <a href="<?= url('login') ?>" class="text-brand-400 font-bold hover:underline">Sign In</a>
    </div>
</div>
