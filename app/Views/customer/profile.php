<div class="py-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="pb-6 border-b border-slate-800">
        <h1 class="font-heading font-extrabold text-3xl text-white">Profile Settings</h1>
        <p class="text-xs text-slate-400 mt-1">Manage your attendee contact information and password.</p>
    </div>

    <div class="glass-card rounded-3xl p-8 space-y-6">
        <form action="<?= url('customer/profile') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Full Name</label>
                <input type="text" name="name" required value="<?= e($customer['name']) ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Email Address</label>
                <input type="email" value="<?= e($customer['email']) ?>" readonly class="w-full px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-slate-500 cursor-not-allowed">
                <span class="text-[10px] text-slate-500 mt-1">Email address cannot be modified once verified.</span>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Mobile Phone</label>
                <input type="text" name="phone" required value="<?= e($customer['phone']) ?>" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
            </div>

            <div class="pt-4 border-t border-slate-800">
                <h3 class="font-heading font-bold text-base text-white mb-2">Change Password</h3>
                <p class="text-xs text-slate-400 mb-3">Leave blank if you do not wish to update your current password.</p>
                <input type="password" name="password" placeholder="New Password (Min 8 characters)" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-xl shadow-brand-600/30 hover:opacity-95 transition-all mt-4">
                Save Profile Changes
            </button>
        </form>
    </div>
</div>
