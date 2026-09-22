<?php
$heroEvents = array_slice($featuredEvents, 0, 3);
$venueIcons = ['building-2', 'landmark', 'map', 'warehouse'];
$categoryIcons = ['cpu', 'trophy', 'palette', 'briefcase-business', 'music-2', 'sparkles'];
?>
<section class="relative overflow-hidden border-b border-slate-800/80 bg-[#0b0f19]">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_78%_18%,rgba(99,102,241,.24),transparent_34%),radial-gradient(circle_at_15%_80%,rgba(14,165,233,.12),transparent_30%)]"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
        <div class="grid lg:grid-cols-[.82fr_1.18fr] gap-12 items-center">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[.22em] text-brand-300 mb-5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span>Your next great night starts here</div>
                <h1 class="font-heading font-extrabold text-5xl sm:text-6xl lg:text-7xl leading-[.98] tracking-tight text-white">Find the room<br><span class="text-brand-400">worth being in.</span></h1>
                <p class="mt-6 text-base sm:text-lg leading-relaxed text-slate-400">Live concerts, sharp ideas, stadium energy, and intimate workshops. Discover the events that make the calendar matter.</p>
                <form action="<?= url('events') ?>" method="GET" class="mt-8 p-3 bg-white rounded-2xl shadow-2xl text-slate-900">
                    <div class="grid sm:grid-cols-2 gap-2"><label class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-slate-100 sm:col-span-2"><i data-lucide="search" class="w-4 h-4 text-slate-500"></i><input name="q" class="w-full bg-transparent text-sm outline-none" placeholder="Event name or keyword"></label><label class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-slate-100"><i data-lucide="map-pin" class="w-4 h-4 text-slate-500"></i><input name="location" class="w-full bg-transparent text-sm outline-none" placeholder="City or venue"></label><label class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-slate-100"><i data-lucide="calendar-days" class="w-4 h-4 text-slate-500"></i><input type="date" name="date_from" class="w-full bg-transparent text-sm outline-none text-slate-600"></label><select name="category" class="px-3 py-2.5 rounded-xl bg-slate-100 text-sm text-slate-600 sm:col-span-2">
                            <option value="">All categories</option><?php foreach ($categories as $category): ?><option value="<?= e($category['slug']) ?>"><?= e($category['name']) ?></option><?php endforeach; ?>
                        </select></div><button class="mt-2 w-full py-3 rounded-xl gradient-brand text-white text-sm font-bold">Search events <i data-lucide="arrow-up-right" class="w-4 h-4 inline"></i></button>
                </form>
            </div>
            <div class="relative min-h-[390px] sm:min-h-[470px]"><?php foreach ($heroEvents as $index => $event): ?><article class="hero-slide <?= $index === 0 ? '' : 'hidden' ?> absolute inset-0"><a href="<?= url("events/{$event['slug']}") ?>" class="block h-full">
                            <div class="h-full min-h-[390px] sm:min-h-[470px] rounded-[2rem] overflow-hidden relative border border-white/10 bg-slate-900"><img src="<?= upload_url($event['banner_image']) ?>" alt="<?= e($event['title']) ?>" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-[#080b13] via-transparent to-transparent"></div>
                                <div class="absolute left-6 right-6 bottom-7"><span class="px-3 py-1 rounded-full bg-white/15 text-white text-[10px] font-bold uppercase tracking-widest"><?= e($event['category_name']) ?></span>
                                    <h2 class="font-heading text-3xl sm:text-5xl font-extrabold text-white leading-tight mt-3"><?= e($event['title']) ?></h2>
                                    <div class="flex flex-wrap gap-4 mt-5 text-xs font-semibold text-slate-200"><span><i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i><?= format_date($event['event_date']) ?></span><span><i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i><?= e($event['venue_city']) ?></span><span class="text-emerald-300">From <?= format_currency($event['min_price'] ?? 0) ?></span></div>
                                </div>
                            </div>
                        </a></article><?php endforeach; ?><?php if (count($heroEvents) > 1): ?><div class="absolute bottom-5 right-6 z-10 flex gap-2"><?php foreach ($heroEvents as $index => $event): ?><button type="button" class="hero-dot w-2.5 h-2.5 rounded-full <?= $index === 0 ? 'bg-white' : 'bg-white/40' ?>" data-target="<?= $index ?>" aria-label="Show featured event <?= $index + 1 ?>"></button><?php endforeach; ?></div><?php endif; ?></div>
        </div>
    </div>
</section>

<section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.2em] text-brand-400">Plan the calendar</p>
            <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-white mt-2">Upcoming & trending</h2>
        </div><a href="<?= url('events') ?>" class="text-sm font-bold text-brand-300">See all events <i data-lucide="arrow-up-right" class="w-4 h-4 inline"></i></a>
    </div>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5"><?php foreach (array_slice($upcomingEvents, 0, 6) as $event): ?><article class="group bg-slate-900/70 border border-slate-800 rounded-2xl overflow-hidden hover:border-brand-500/50 transition-colors"><a href="<?= url("events/{$event['slug']}") ?>" class="block relative h-48 overflow-hidden"><img src="<?= upload_url($event['banner_image']) ?>" alt="<?= e($event['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 to-transparent"></div><span class="absolute top-4 left-4 px-2.5 py-1 rounded-md bg-slate-950/70 text-[10px] font-bold uppercase text-brand-200"><?= e($event['category_name']) ?></span>
                </a>
                <div class="p-5">
                    <h3 class="font-heading text-xl font-bold text-white line-clamp-1"><?= e($event['title']) ?></h3>
                    <div class="grid grid-cols-2 gap-3 mt-4 text-xs text-slate-400"><span><i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1"></i><?= format_date($event['event_date']) ?></span><span><i data-lucide="map-pin" class="w-3.5 h-3.5 inline mr-1"></i><?= e($event['venue_city']) ?></span></div>
                    <div class="flex items-end justify-between mt-5 pt-4 border-t border-slate-800">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-500">Starting from</p>
                            <p class="font-heading text-lg font-extrabold text-white"><?= format_currency($event['min_price'] ?? 0) ?></p>
                        </div><a href="<?= url("events/{$event['slug']}") ?>" class="px-3.5 py-2 rounded-lg gradient-brand text-xs font-bold text-white">Get tickets</a>
                    </div>
                </div>
            </article><?php endforeach; ?></div>
</section>

<section class="py-20 bg-slate-900/50 border-y border-slate-800/70">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[.2em] text-brand-400">Browse by mood</p>
        <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-white mt-2">Find your scene</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mt-8"><?php foreach ($categories as $index => $category): ?><a href="<?= url('events?category=' . urlencode($category['slug'])) ?>" class="group p-5 rounded-2xl bg-[#0b0f19] border border-slate-800 hover:border-brand-500/60 transition-all"><i data-lucide="<?= e($categoryIcons[$index % count($categoryIcons)]) ?>" class="w-6 h-6 text-brand-400"></i>
                    <h3 class="mt-8 text-sm font-bold text-white"><?= e($category['name']) ?></h3>
                    <p class="mt-1 text-[11px] text-slate-500"><?= (int)$category['event_count'] ?> events</p>
                </a><?php endforeach; ?></div>
    </div>
</section>

<section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.2em] text-brand-400">Go somewhere memorable</p>
            <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-white mt-2">Popular venues</h2>
        </div><a href="<?= url('events') ?>" class="text-sm font-bold text-brand-300">Explore events <i data-lucide="arrow-up-right" class="w-4 h-4 inline"></i></a>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4"><?php foreach ($featuredVenues as $index => $venue): ?><a href="<?= url('events?location=' . urlencode($venue['city'])) ?>" class="p-5 rounded-2xl border border-slate-800 bg-slate-900/60 hover:border-brand-500/50 transition-colors">
                <div class="w-11 h-11 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center"><i data-lucide="<?= $venueIcons[$index % count($venueIcons)] ?>" class="w-5 h-5 text-brand-300"></i></div>
                <h3 class="font-heading font-bold text-white mt-5"><?= e($venue['name']) ?></h3>
                <p class="text-xs text-slate-400 mt-2"><?= e($venue['city']) ?> · <?= (int)$venue['event_count'] ?> events</p>
            </a><?php endforeach; ?></div>
</section>

<section class="py-20 bg-brand-950/20 border-y border-brand-500/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[.2em] text-brand-400">Simple by design</p>
        <h2 class="font-heading text-3xl sm:text-4xl font-extrabold text-white mt-2">From discovery to front row.</h2>
        <div class="grid sm:grid-cols-3 gap-8 mt-10">
            <div><span class="text-4xl font-heading font-extrabold text-brand-400">01</span>
                <h3 class="font-bold text-white mt-3">Choose a moment</h3>
                <p class="text-xs text-slate-400 mt-2">Search the event, date, category, or venue that fits your plan.</p>
            </div>
            <div><span class="text-4xl font-heading font-extrabold text-brand-400">02</span>
                <h3 class="font-bold text-white mt-3">Reserve your pass</h3>
                <p class="text-xs text-slate-400 mt-2">Select a tier and complete secure mobile payment verification.</p>
            </div>
            <div><span class="text-4xl font-heading font-extrabold text-emerald-400">03</span>
                <h3 class="font-bold text-white mt-3">Walk in smiling</h3>
                <p class="text-xs text-slate-400 mt-2">Show your verified QR pass at the gate. No paper chase.</p>
            </div>
        </div>
        <div class="mt-12 p-6 sm:p-8 rounded-2xl bg-slate-900 border border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-400">For organizers</p>
                <h3 class="font-heading text-2xl font-bold text-white mt-1">Have a room that needs a crowd?</h3>
                <p class="text-sm text-slate-400 mt-2">Bring your next event to Sidra and let us handle discovery, tickets, and the gate.</p>
            </div><a href="<?= url('contact') ?>" class="shrink-0 px-5 py-3 rounded-xl gradient-brand text-white text-sm font-bold">Host your event <i data-lucide="arrow-up-right" class="w-4 h-4 inline ml-1"></i></a>
        </div>
    </div>
</section>

<script>
    (() => {
        const slides = [...document.querySelectorAll('.hero-slide')];
        const dots = [...document.querySelectorAll('.hero-dot')];
        if (slides.length < 2) return;
        let active = 0;
        const show = (index) => {
            active = index;
            slides.forEach((slide, i) => slide.classList.toggle('hidden', i !== active));
            dots.forEach((dot, i) => {
                dot.classList.toggle('bg-white', i === active);
                dot.classList.toggle('bg-white/40', i !== active);
            });
        };
        dots.forEach((dot, index) => dot.addEventListener('click', () => show(index)));
        setInterval(() => show((active + 1) % slides.length), 6500);
    })();
</script>