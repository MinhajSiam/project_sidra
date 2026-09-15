<div class="space-y-6">
    <div class="text-center">
        <h2 class="font-heading font-extrabold text-2xl text-white">Welcome Back</h2>
        <p class="text-xs text-slate-400 mt-1">Sign in to manage your bookings and digital tickets</p>
    </div>

    <form action="<?= url('login') ?>" method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="redirect" value="<?= e($redirect) ?>">

        <div>
            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Email or Phone Number</label>
            <input type="text" name="login" required placeholder="Enter email or mobile number" value="<?= old('login') ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-bold uppercase text-slate-300">Password</label>
                <a href="<?= url('forgot-password') ?>" class="text-xs text-brand-400 hover:text-brand-300">Forgot?</a>
            </div>
            <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <button type="submit" class="w-full py-3.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-xl shadow-brand-600/30 hover:opacity-95 transition-all mt-2">
            Sign In to Account
        </button>
    </form>

    <div class="text-center pt-4 border-t border-slate-800 text-xs text-slate-400">
        Don't have an account yet? 
        <a href="<?= url('register') ?>" class="text-brand-400 font-bold hover:underline">Register now</a>
    </div>
</div>
