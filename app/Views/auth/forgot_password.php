<div class="space-y-6">
    <div class="text-center">
        <h2 class="font-heading font-extrabold text-2xl text-white">Reset Password</h2>
        <p class="text-xs text-slate-400 mt-1">Enter your registered email address to receive recovery instructions</p>
    </div>

    <form action="<?= url('forgot-password') ?>" method="POST" class="space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Email Address *</label>
            <input type="email" name="email" required placeholder="Enter your registered email" value="<?= old('email') ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
        </div>

        <button type="submit" class="w-full py-3.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-xl shadow-brand-600/30 hover:opacity-95 transition-all mt-2">
            Send Reset Link
        </button>
    </form>

    <div class="text-center pt-4 border-t border-slate-800 text-xs text-slate-400">
        Remembered your password? 
        <a href="<?= url('login') ?>" class="text-brand-400 font-bold hover:underline">Back to Login</a>
    </div>
</div>
