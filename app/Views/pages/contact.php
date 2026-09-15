<div class="py-16 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
    <div class="text-center space-y-4">
        <h1 class="font-heading font-extrabold text-4xl text-white">Get in Touch</h1>
        <p class="text-slate-400 text-sm">Have a question regarding your booking or want to host your next major event on Sidra?</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-600/20 text-brand-400 flex items-center justify-center flex-shrink-0">
                <i data-lucide="phone-call" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="font-heading font-bold text-white text-base">Customer Helpline</h3>
                <p class="text-xs text-slate-400 mt-1">Daily 9:00 AM - 10:00 PM</p>
                <div class="text-brand-400 font-bold text-sm mt-2"><?= e($settings['contact_phone'] ?? '+880 9612-888999') ?></div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-600/20 text-brand-400 flex items-center justify-center flex-shrink-0">
                <i data-lucide="mail" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="font-heading font-bold text-white text-base">Support & Inquiries</h3>
                <p class="text-xs text-slate-400 mt-1">24-hour response time</p>
                <div class="text-brand-400 font-bold text-sm mt-2"><?= e($settings['contact_email'] ?? 'support@sidra.test') ?></div>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-8 space-y-6">
        <h3 class="font-heading font-bold text-xl text-white">Send Us a Direct Message</h3>
        <form onsubmit="event.preventDefault(); alert('Thank you for contacting Sidra! Our representative will get back to you shortly.');" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Your Name</label>
                <input type="text" required placeholder="e.g. Tanvir Ahmed" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Email Address</label>
                <input type="email" required placeholder="e.g. tanvir@example.com" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Message</label>
                <textarea rows="4" required placeholder="How can our team assist you?" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm text-white focus:outline-none focus:border-brand-500"></textarea>
            </div>
            <button type="submit" class="w-full py-3.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-lg shadow-brand-600/30 hover:opacity-95">
                Send Message
            </button>
        </form>
    </div>
</div>
