<div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
    <!-- Page Header & Filter Form -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-slate-800">
        <div>
            <h1 class="font-heading font-extrabold text-3xl sm:text-4xl text-white">Browse Events</h1>
            <p class="text-sm text-slate-400 mt-1">Discover upcoming concerts, tech conferences, and summits in Bangladesh.</p>
        </div>

        <!-- Filter & Search Controls -->
        <form action="<?= url('events') ?>" method="GET" class="flex flex-wrap items-center gap-3">
            <?php if ($currentCategory): ?>
                <input type="hidden" name="category" value="<?= e($currentCategory) ?>">
            <?php endif; ?>

            <div class="relative min-w-[200px]">
                <input type="text" name="q" value="<?= e($searchQuery) ?>" placeholder="Search keyword..." class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-xs text-white focus:outline-none focus:border-brand-500">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>

            <select name="sort" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-xs text-white focus:outline-none focus:border-brand-500 cursor-pointer">
                <option value="upcoming" <?= $currentSort === 'upcoming' ? 'selected' : '' ?>>Upcoming First</option>
                <option value="date_desc" <?= $currentSort === 'date_desc' ? 'selected' : '' ?>>Latest First</option>
                <option value="title_asc" <?= $currentSort === 'title_asc' ? 'selected' : '' ?>>Alphabetical (A-Z)</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl gradient-brand text-white font-bold text-xs shadow">
                Filter
            </button>
            <?php if ($searchQuery || $currentCategory): ?>
                <a href="<?= url('events') ?>" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-400 hover:text-white transition-colors">
                    Reset
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Category Pill Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
        <a href="<?= url('events') ?>" class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= empty($currentCategory) ? 'gradient-brand text-white shadow-lg' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
            All Events (<?= $totalEvents ?>)
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= url('events?category=' . urlencode($cat['slug'])) ?>" class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all <?= $currentCategory === $cat['slug'] ? 'gradient-brand text-white shadow-lg' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
                <?= e($cat['name']) ?> (<?= $cat['event_count'] ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Events Grid -->
    <?php if (empty($events)): ?>
        <div class="text-center py-20 glass-card rounded-2xl p-12 space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-800 text-slate-500 flex items-center justify-center mx-auto">
                <i data-lucide="calendar-x" class="w-8 h-8"></i>
            </div>
            <h3 class="font-heading font-bold text-xl text-white">No Events Found</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">We couldn't find any events matching your selected search criteria. Try modifying your filter.</p>
            <a href="<?= url('events') ?>" class="inline-block px-5 py-2.5 rounded-xl gradient-brand text-white text-xs font-bold">
                Browse All Events
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($events as $event): ?>
                <div class="glass-card rounded-2xl overflow-hidden glass-card-hover flex flex-col group">
                    <div class="relative h-52 bg-slate-900 overflow-hidden">
                        <img src="<?= upload_url($event['banner_image']) ?>" alt="<?= e($event['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-transparent to-transparent"></div>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md border border-slate-700/50 text-white font-bold text-xs">
                                <?= e($event['category_name']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-xs font-semibold text-brand-400">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                <?= format_date($event['event_date'], 'D, d M Y') ?>
                            </div>
                            <h3 class="font-heading font-bold text-xl text-white line-clamp-1 group-hover:text-brand-300 transition-colors">
                                <?= e($event['title']) ?>
                            </h3>
                            <div class="flex items-center gap-1.5 text-xs text-slate-400">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 flex-shrink-0"></i>
                                <span class="truncate"><?= e($event['venue_name']) ?>, <?= e($event['venue_city']) ?></span>
                            </div>
                            <p class="text-xs text-slate-400 line-clamp-2 mt-2">
                                <?= e($event['summary']) ?>
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] text-slate-500 uppercase font-semibold">Price starts</div>
                                <div class="font-heading font-extrabold text-lg text-white">
                                    <?= format_currency($event['min_price'] ?? 0) ?>
                                </div>
                            </div>
                            <a href="<?= url("events/{$event['slug']}") ?>" class="px-4 py-2.5 rounded-xl gradient-brand text-white font-bold text-xs hover:opacity-90 transition-opacity">
                                View & Book
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-center gap-2 pt-8">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= url('events?page=' . $i . ($currentCategory ? '&category=' . urlencode($currentCategory) : '') . ($searchQuery ? '&q=' . urlencode($searchQuery) : '')) ?>" 
                   class="w-10 h-10 rounded-xl flex items-center justify-center text-xs font-bold transition-all <?= $i === $page ? 'gradient-brand text-white shadow-lg' : 'bg-slate-900 border border-slate-800 text-slate-400 hover:text-white' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
