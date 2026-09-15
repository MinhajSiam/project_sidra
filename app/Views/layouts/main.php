<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?><?= e($appName) ?></title>
    <meta name="description" content="Discover, book, and verify tickets for premier concerts, technology conferences, summits, and festivals across Bangladesh with Sidra.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        },
                        dark: {
                            900: '#0b0f19',
                            800: '#111827',
                            700: '#1e293b',
                            600: '#334155',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- App Styles -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-[#0b0f19] text-slate-100 min-h-full flex flex-col antialiased selection:bg-brand-600 selection:text-white">

    <!-- Flash Toast Notifications -->
    <?php if ($flashSuccess || $flashError || $flashWarning || $flashInfo): ?>
    <div id="flash-toast" class="fixed top-6 left-1/2 -translate-x-1/2 z-50 max-w-lg w-[90%] toast-animate cursor-pointer shadow-2xl rounded-xl p-4 flex items-center gap-3.5 border <?= $flashSuccess ? 'bg-emerald-950/90 border-emerald-500/40 text-emerald-200' : ($flashError ? 'bg-rose-950/90 border-rose-500/40 text-rose-200' : ($flashWarning ? 'bg-amber-950/90 border-amber-500/40 text-amber-200' : 'bg-slate-900/90 border-brand-500/40 text-indigo-200')) ?>" onclick="this.remove()">
        <i data-lucide="<?= $flashSuccess ? 'check-circle-2' : ($flashError ? 'alert-octagon' : ($flashWarning ? 'alert-triangle' : 'info')) ?>" class="w-5 h-5 flex-shrink-0"></i>
        <div class="text-sm font-medium leading-snug flex-1">
            <?= $flashSuccess ?: ($flashError ?: ($flashWarning ?: $flashInfo)) ?>
        </div>
        <i data-lucide="x" class="w-4 h-4 text-slate-400 hover:text-white flex-shrink-0"></i>
    </div>
    <?php endif; ?>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="<?= url('/') ?>" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl gradient-brand flex items-center justify-center shadow-lg shadow-brand-500/20 group-hover:scale-105 transition-transform duration-300">
                        <i data-lucide="ticket" class="w-5 h-5 text-white"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading font-extrabold text-2xl tracking-tight text-white flex items-center gap-1">
                            SIDRA
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500 inline-block"></span>
                        </span>
                        <span class="text-[10px] font-semibold tracking-wider uppercase text-slate-400 -mt-1">Event Platform</span>
                    </div>
                </a>

                <!-- Desktop Nav Menu -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="<?= url('/') ?>" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Home</a>
                    <a href="<?= url('events') ?>" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Browse Events</a>
                    <a href="<?= url('about') ?>" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">About Us</a>
                    <a href="<?= url('contact') ?>" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Contact</a>
                </nav>

                <!-- User & Auth Actions -->
                <div class="hidden md:flex items-center gap-4">
                    <?php if ($authCustomer): ?>
                        <div class="relative group">
                            <a href="<?= url('customer/dashboard') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl bg-slate-800/80 border border-slate-700/60 hover:border-brand-500/50 transition-all">
                                <div class="w-8 h-8 rounded-lg bg-brand-600/20 text-brand-400 font-bold flex items-center justify-center text-sm border border-brand-500/30">
                                    <?= strtoupper(substr($authCustomer['name'], 0, 1)) ?>
                                </div>
                                <div class="text-left">
                                    <div class="text-xs font-semibold text-white leading-tight"><?= e(explode(' ', $authCustomer['name'])[0]) ?></div>
                                    <div class="text-[10px] text-slate-400">My Account</div>
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                            </a>

                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 rounded-xl bg-slate-900 border border-slate-800 shadow-2xl py-2 hidden group-hover:block">
                                <a href="<?= url('customer/dashboard') ?>" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                                </a>
                                <a href="<?= url('customer/tickets') ?>" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                    <i data-lucide="qr-code" class="w-4 h-4"></i> My Digital Tickets
                                </a>
                                <a href="<?= url('customer/bookings') ?>" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                    <i data-lucide="receipt" class="w-4 h-4"></i> Order History
                                </a>
                                <a href="<?= url('customer/profile') ?>" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-800 hover:text-white">
                                    <i data-lucide="user" class="w-4 h-4"></i> Profile Settings
                                </a>
                                <div class="border-t border-slate-800 my-1"></div>
                                <form action="<?= url('logout') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs font-medium text-rose-400 hover:bg-rose-950/20">
                                        <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="text-sm font-semibold text-slate-300 hover:text-white transition-colors">
                            Sign In
                        </a>
                        <a href="<?= url('register') ?>" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white gradient-brand shadow-lg shadow-brand-600/20 hover:shadow-brand-600/40 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            Get Started
                        </a>
                    <?php endif; ?>

                    <?php if ($authUser): ?>
                        <a href="<?= url('admin') ?>" class="p-2 rounded-lg bg-slate-800 text-amber-400 hover:bg-slate-700 transition-colors border border-amber-500/20" title="Admin Portal">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobile-menu" class="md:hidden hidden border-t border-slate-800 bg-slate-900/95 px-4 pt-3 pb-6 space-y-3">
            <a href="<?= url('/') ?>" class="block py-2 text-base font-medium text-slate-300 hover:text-white">Home</a>
            <a href="<?= url('events') ?>" class="block py-2 text-base font-medium text-slate-300 hover:text-white">Browse Events</a>
            <a href="<?= url('about') ?>" class="block py-2 text-base font-medium text-slate-300 hover:text-white">About Us</a>
            <a href="<?= url('contact') ?>" class="block py-2 text-base font-medium text-slate-300 hover:text-white">Contact</a>
            
            <div class="pt-4 border-t border-slate-800">
                <?php if ($authCustomer): ?>
                    <a href="<?= url('customer/dashboard') ?>" class="block py-2 text-base font-medium text-brand-400">Dashboard & Tickets</a>
                    <form action="<?= url('logout') ?>" method="POST" class="pt-2">
                        <?= csrf_field() ?>
                        <button type="submit" class="text-sm font-medium text-rose-400">Sign Out</button>
                    </form>
                <?php else: ?>
                    <div class="flex flex-col gap-2 pt-2">
                        <a href="<?= url('login') ?>" class="w-full text-center py-2.5 rounded-xl border border-slate-700 font-semibold text-white">Sign In</a>
                        <a href="<?= url('register') ?>" class="w-full text-center py-2.5 rounded-xl gradient-brand font-semibold text-white">Create Account</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark-900 border-t border-slate-800/80 pt-16 pb-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl gradient-brand flex items-center justify-center shadow-lg shadow-brand-500/20">
                            <i data-lucide="ticket" class="w-5 h-5 text-white"></i>
                        </div>
                        <span class="font-heading font-extrabold text-2xl tracking-tight text-white">SIDRA</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        <?= e($settings['platform_tagline'] ?? 'Discover, Book & Verify Digital Tickets with Absolute Confidence.') ?>
                    </p>
                    <div class="flex items-center gap-3 text-slate-400 text-sm">
                        <i data-lucide="phone-call" class="w-4 h-4 text-brand-400"></i>
                        <span><?= e($settings['contact_phone'] ?? '+880 9612-888999') ?></span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-400 text-sm">
                        <i data-lucide="mail" class="w-4 h-4 text-brand-400"></i>
                        <span><?= e($settings['contact_email'] ?? 'support@sidra.test') ?></span>
                    </div>
                </div>

                <div>
                    <h4 class="font-heading font-bold text-white text-base mb-4">Quick Links</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="<?= url('events') ?>" class="hover:text-brand-400 transition-colors">All Events</a></li>
                        <li><a href="<?= url('events?sort=upcoming') ?>" class="hover:text-brand-400 transition-colors">Upcoming Conferences</a></li>
                        <li><a href="<?= url('about') ?>" class="hover:text-brand-400 transition-colors">About Sidra</a></li>
                        <li><a href="<?= url('contact') ?>" class="hover:text-brand-400 transition-colors">Helpline & Support</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-heading font-bold text-white text-base mb-4">Gate Staff & Security</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="<?= url('gate/scan') ?>" class="hover:text-brand-400 transition-colors flex items-center gap-1.5"><i data-lucide="qr-code" class="w-3.5 h-3.5 text-emerald-400"></i> Gate Scanner</a></li>
                        <li><a href="<?= url('admin/login') ?>" class="hover:text-brand-400 transition-colors">Staff Portal</a></li>
                        <li><a href="<?= url('terms') ?>" class="hover:text-brand-400 transition-colors">Terms of Service</a></li>
                        <li><a href="<?= url('privacy') ?>" class="hover:text-brand-400 transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-heading font-bold text-white text-base mb-4">Accepted Payment Methods</h4>
                    <p class="text-xs text-slate-400 mb-3">Instant manual payment processing via Bangladesh's leading mobile financial networks:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-pink-950/60 border border-pink-500/30 text-pink-300 text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-pink-500"></span> bKash
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-950/60 border border-orange-500/30 text-orange-300 text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span> Nagad
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-950/60 border border-purple-500/30 text-purple-300 text-xs font-bold">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span> Rocket
                        </span>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800/80 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; <?= date('Y') ?> <?= e($appName) ?>. All rights reserved. Built with precision for exceptional event experiences.</p>
                <div class="flex gap-6">
                    <a href="<?= url('terms') ?>" class="hover:text-slate-400">Terms</a>
                    <a href="<?= url('privacy') ?>" class="hover:text-slate-400">Privacy</a>
                    <a href="<?= url('contact') ?>" class="hover:text-slate-400">Support</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Initialize Lucide Icons & App JS -->
    <script>lucide.createIcons();</script>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
