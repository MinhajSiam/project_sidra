<!-- Hero Section -->
<section class="relative pt-12 pb-24 overflow-hidden bg-radial-gradient">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-6">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-400 text-xs font-bold tracking-wide uppercase">
                <span class="w-2 h-2 rounded-full bg-brand-400 animate-pulse"></span>
                Next-Gen Event Ticketing
            </div>

            <h1 class="font-heading font-extrabold text-4xl sm:text-6xl lg:text-7xl tracking-tight text-white leading-[1.1]">
                Unforgettable Experiences, <br>
                <span class="gradient-text">Zero Compromise.</span>
            </h1>

            <p class="text-lg text-slate-400 font-normal leading-relaxed">
                Discover, book, and verify tickets for premier concerts, technology conferences, summits, and festivals across Bangladesh with instant mobile payments and dynamic secure QR passes.
            </p>

            <!-- Search Form -->
            <form action="<?= url('events') ?>" method="GET" class="mt-8 max-w-2xl mx-auto flex flex-col sm:flex-row gap-3 p-2 rounded-2xl glass-card shadow-2xl">
                <div class="flex-1 flex items-center gap-3 px-4 py-2">
                    <i data-lucide="search" class="w-5 h-5 text-slate-400"></i>
                    <input type="text" name="q" placeholder="Search by event title, artist, or venue..." class="w-full bg-transparent border-none text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-0">
                </div>
                <button type="submit" class="px-7 py-3 rounded-xl gradient-brand text-white font-bold text-sm shadow-lg shadow-brand-600/30 hover:shadow-brand-600/50 hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                    Explore Events
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Popular Category Badges -->
            <div class="pt-4 flex flex-wrap items-center justify-center gap-2">
                <span class="text-xs font-semibold text-slate-500 mr-1">Trending:</span>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= url('events?category=' . urlencode($cat['slug'])) ?>" class="px-3 py-1 rounded-lg bg-slate-900 border border-slate-800 text-xs font-medium text-slate-300 hover:border-brand-500/50 hover:text-white transition-all">
                        <?= e($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Trust Statistics Bar -->
        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-6 py-8 border-y border-slate-800/80">
            <div class="text-center">
                <div class="font-heading font-extrabold text-3xl sm:text-4xl text-white">50K+</div>
                <div class="text-xs font-medium text-slate-400 uppercase tracking-wider mt-1">Tickets Verified</div>
            </div>
            <div class="text-center">
                <div class="font-heading font-extrabold text-3xl sm:text-4xl text-brand-400">99.9%</div>
                <div class="text-xs font-medium text-slate-400 uppercase tracking-wider mt-1">Gate Check-in Uptime</div>
            </div>
            <div class="text-center">
                <div class="font-heading font-extrabold text-3xl sm:text-4xl text-white">0%</div>
                <div class="text-xs font-medium text-slate-400 uppercase tracking-wider mt-1">Duplicate Fraud</div>
            </div>
            <div class="text-center">
                <div class="font-heading font-extrabold text-3xl sm:text-4xl text-emerald-400">100%</div>
                <div class="text-xs font-medium text-slate-400 uppercase tracking-wider mt-1">Secure Manual Escrow</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Events Showcase -->
<?php if (!empty($featuredEvents)): ?>
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between mb-10">
        <div>
            <div class="text-xs font-bold text-brand-400 uppercase tracking-widest mb-2">Editor's Pick</div>
            <h2 class="font-heading font-bold text-3xl text-white">Featured Events</h2>
        </div>
        <a href="<?= url('events') ?>" class="text-sm font-semibold text-brand-400 hover:text-brand-300 flex items-center gap-1">
            View All Events <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php foreach ($featuredEvents as $event): ?>
            <div class="glass-card rounded-2xl overflow-hidden glass-card-hover flex flex-col group">
                <!-- Banner Image -->
                <div class="relative h-56 overflow-hidden bg-slate-900">
                    <img src="<?= upload_url($event['banner_image']) ?>" alt="<?= e($event['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-transparent to-transparent"></div>
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 rounded-full bg-brand-600 text-white font-bold text-xs shadow-lg">
                            <?= e($event['category_name']) ?>
                        </span>
                    </div>
                </div>

                <!-- Event Details -->
                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-xs font-semibold text-brand-400">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            <?= format_date($event['event_date']) ?> • <?= format_time($event['start_time']) ?>
                        </div>
                        <h3 class="font-heading font-bold text-xl text-white line-clamp-1 group-hover:text-brand-300 transition-colors">
                            <?= e($event['title']) ?>
                        </h3>
                        <p class="text-xs text-slate-400 line-clamp-2">
                            <?= e($event['summary']) ?>
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] text-slate-500 uppercase font-semibold">Starts from</div>
                            <div class="font-heading font-extrabold text-lg text-white">
                                <?= format_currency($event['min_price'] ?? 0) ?>
                            </div>
                        </div>
                        <a href="<?= url("events/{$event['slug']}") ?>" class="px-4 py-2 rounded-xl gradient-brand text-white font-bold text-xs hover:opacity-90 transition-opacity">
                            Get Tickets
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- How It Works Section -->
<section class="py-20 bg-dark-800/40 border-y border-slate-800/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="text-xs font-bold text-brand-400 uppercase tracking-widest mb-2">Simplicity & Security</div>
            <h2 class="font-heading font-bold text-3xl sm:text-4xl text-white">How Sidra Works</h2>
            <p class="text-sm text-slate-400 mt-3">Book and enter your favorite events in 3 effortless steps with maximum transaction transparency.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="glass-card rounded-2xl p-8 text-center space-y-4 relative">
                <div class="w-14 h-14 rounded-2xl bg-brand-600/20 text-brand-400 border border-brand-500/30 font-heading font-extrabold text-2xl flex items-center justify-center mx-auto shadow-xl">
                    1
                </div>
                <h3 class="font-heading font-bold text-xl text-white">Select Your Tickets</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Browse verified events, choose your preferred ticket tier (VIP, General, Student), and select your desired quantity.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="glass-card rounded-2xl p-8 text-center space-y-4 relative">
                <div class="w-14 h-14 rounded-2xl bg-brand-600/20 text-brand-400 border border-brand-500/30 font-heading font-extrabold text-2xl flex items-center justify-center mx-auto shadow-xl">
                    2
                </div>
                <h3 class="font-heading font-bold text-xl text-white">Submit Mobile Payment</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Transfer via bKash, Nagad, or Rocket and enter your Transaction ID (TrxID). Our finance team verifies every record securely.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="glass-card rounded-2xl p-8 text-center space-y-4 relative">
                <div class="w-14 h-14 rounded-2xl bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 font-heading font-extrabold text-2xl flex items-center justify-center mx-auto shadow-xl">
                    3
                </div>
                <h3 class="font-heading font-bold text-xl text-white">Scan QR at Gate</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Receive your digital pass instantly upon approval. Present your encrypted QR code on your phone or print to enter!
                </p>
            </div>
        </div>
    </div>
</section>
