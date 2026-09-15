<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-slate-400">
        <a href="<?= url('/') ?>" class="hover:text-white">Home</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="<?= url('events') ?>" class="hover:text-white">Events</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-200 font-semibold truncate"><?= e($event['title']) ?></span>
    </nav>

    <!-- Main Grid: Event Details (Left) + Ticket Selector (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Left 2 Columns: Banner & Full Info -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Banner Image -->
            <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-800 relative max-h-[420px]">
                <img src="<?= upload_url($event['banner_image']) ?>" alt="<?= e($event['title']) ?>" class="w-full h-full object-cover">
                <div class="absolute top-4 left-4">
                    <span class="px-3.5 py-1.5 rounded-full bg-slate-900/80 backdrop-blur-md border border-slate-700/60 text-white font-bold text-xs shadow-lg">
                        <?= e($event['category_name']) ?>
                    </span>
                </div>
            </div>

            <!-- Title & Quick Meta -->
            <div class="space-y-4">
                <h1 class="font-heading font-extrabold text-3xl sm:text-4xl lg:text-5xl text-white leading-tight">
                    <?= e($event['title']) ?>
                </h1>

                <!-- Key Event Metadata Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="glass-card rounded-2xl p-4 flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl bg-brand-600/20 text-brand-400 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="calendar" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-400">Date & Schedule</div>
                            <div class="text-sm font-bold text-white"><?= format_date($event['event_date'], 'l, d F Y') ?></div>
                            <div class="text-xs text-brand-400"><?= format_time($event['start_time']) ?> - <?= format_time($event['end_time']) ?></div>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl p-4 flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-xl bg-brand-600/20 text-brand-400 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="text-[10px] uppercase font-bold text-slate-400">Venue & City</div>
                            <div class="text-sm font-bold text-white truncate"><?= e($event['venue_name']) ?></div>
                            <div class="text-xs text-slate-400 truncate"><?= e($event['venue_address']) ?>, <?= e($event['venue_city']) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description & Schedule Details -->
            <div class="glass-card rounded-2xl p-8 space-y-6">
                <h2 class="font-heading font-bold text-2xl text-white">About This Event</h2>
                <div class="text-sm text-slate-300 leading-relaxed space-y-4 whitespace-pre-line">
                    <?= e($event['description']) ?>
                </div>

                <?php if (!empty($event['map_url'])): ?>
                <div class="pt-4 border-t border-slate-800">
                    <a href="<?= e($event['map_url']) ?>" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-brand-400 hover:text-brand-300">
                        <i data-lucide="navigation" class="w-4 h-4"></i>
                        Open Venue in Google Maps
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right 1 Column: Ticket Booking Selector Card -->
        <div class="space-y-6">
            <div class="glass-card rounded-3xl p-6 sm:p-8 shadow-2xl border border-brand-500/20 sticky top-28 space-y-6">
                <div>
                    <span class="text-[10px] font-bold tracking-wider uppercase text-brand-400">Step 1 of 3</span>
                    <h2 class="font-heading font-bold text-2xl text-white mt-1">Select Tickets</h2>
                    <p class="text-xs text-slate-400 mt-1">Choose your pass tier and quantity.</p>
                </div>

                <!-- Ticket Selection Form -->
                <form action="<?= url("events/{$event['slug']}/checkout") ?>" method="GET" class="space-y-5">
                    <div class="space-y-4">
                        <?php foreach ($ticketTypes as $tier): ?>
                            <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3 ticket-tier-row">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="font-heading font-bold text-base text-white"><?= e($tier['name']) ?></div>
                                        <div class="text-xs text-brand-400 font-extrabold mt-0.5"><?= format_currency($tier['price']) ?></div>
                                        <?php if ($tier['description']): ?>
                                            <div class="text-[11px] text-slate-400 mt-1"><?= e($tier['description']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold <?= (int)$tier['remaining_quantity'] > 20 ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : 'bg-amber-950 text-amber-400 border border-amber-500/30' ?>">
                                        <?= $tier['remaining_quantity'] ?> left
                                    </span>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-slate-800/80">
                                    <div class="text-xs text-slate-400">
                                        Subtotal: <span class="tier-subtotal font-bold text-white">৳ 0.00</span>
                                    </div>

                                    <!-- Plus / Minus Quantity Buttons -->
                                    <div class="qty-control flex items-center gap-2 bg-slate-800 rounded-xl p-1 border border-slate-700">
                                        <button type="button" class="qty-btn-minus w-7 h-7 rounded-lg bg-slate-700/80 hover:bg-slate-600 text-white font-bold flex items-center justify-center transition-colors">
                                            -
                                        </button>
                                        <input type="number" 
                                               name="tickets[<?= $tier['id'] ?>]" 
                                               value="0" 
                                               min="0" 
                                               max="<?= min((int)$tier['remaining_quantity'], (int)$tier['max_per_user']) ?>" 
                                               data-price="<?= $tier['price'] ?>"
                                               class="ticket-qty-input w-9 text-center bg-transparent border-none text-xs font-bold text-white focus:outline-none focus:ring-0 p-0" 
                                               readonly>
                                        <button type="button" class="qty-btn-plus w-7 h-7 rounded-lg bg-brand-600 hover:bg-brand-500 text-white font-bold flex items-center justify-center transition-colors">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Order Total Calculation Bar -->
                    <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-slate-400 font-semibold uppercase">Total Amount</div>
                            <div id="checkout-total-display" class="font-heading font-extrabold text-2xl text-white">৳ 0.00</div>
                        </div>
                        <button type="submit" id="checkout-submit-btn" class="px-6 py-3.5 rounded-xl gradient-brand text-white font-bold text-sm shadow-xl shadow-brand-600/30 hover:opacity-95 transition-all opacity-50 cursor-not-allowed" disabled>
                            Checkout Now
                        </button>
                    </div>
                </form>

                <div class="text-[11px] text-slate-400 space-y-1">
                    <div class="flex items-center gap-1.5 text-emerald-400 font-semibold">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        100% Guaranteed Official Tickets
                    </div>
                    <p>Instant digital QR generation upon finance payment review.</p>
                </div>
            </div>
        </div>
    </div>
</div>
