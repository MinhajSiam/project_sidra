<?php
$pendingCount = \App\Core\Database::fetch("SELECT COUNT(*) as total FROM `payments` WHERE status = 'pending'")['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>Admin Portal — <?= e($appName) ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

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
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
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
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-[#0b0f19] text-slate-100 min-h-full flex antialiased">

    <!-- Flash Toast -->
    <?php if ($flashSuccess || $flashError || $flashWarning || $flashInfo): ?>
    <div id="flash-toast" class="fixed top-6 right-6 z-50 max-w-md w-full toast-animate cursor-pointer shadow-2xl rounded-xl p-4 flex items-center gap-3.5 border <?= $flashSuccess ? 'bg-emerald-950/90 border-emerald-500/40 text-emerald-200' : ($flashError ? 'bg-rose-950/90 border-rose-500/40 text-rose-200' : 'bg-slate-900/90 border-brand-500/40 text-indigo-200') ?>" onclick="this.remove()">
        <i data-lucide="<?= $flashSuccess ? 'check-circle-2' : ($flashError ? 'alert-octagon' : 'info') ?>" class="w-5 h-5 flex-shrink-0"></i>
        <div class="text-sm font-medium leading-snug flex-1">
            <?= $flashSuccess ?: ($flashError ?: ($flashWarning ?: $flashInfo)) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Mobile Backdrop -->
    <div id="admin-backdrop" class="fixed inset-0 bg-black/60 z-40 hidden md:hidden" onclick="toggleAdminSidebar()"></div>

    <!-- Sidebar Navigation -->
    <aside id="admin-sidebar" class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-dark-800 border-r border-slate-800 flex flex-col flex-shrink-0 min-h-screen transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
        <!-- Sidebar Brand -->
        <div class="h-20 flex items-center px-6 border-b border-slate-800 gap-3">
            <div class="w-9 h-9 rounded-xl gradient-brand flex items-center justify-center shadow-lg shadow-brand-500/20">
                <i data-lucide="shield" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <span class="font-heading font-extrabold text-xl tracking-tight text-white flex items-center gap-1">
                    SIDRA
                    <span class="text-[10px] bg-brand-500/20 text-brand-400 font-bold px-1.5 py-0.5 rounded border border-brand-500/30">ADMIN</span>
                </span>
            </div>
        </div>

        <!-- Staff Profile Summary -->
        <div class="p-4 mx-3 my-3 rounded-xl bg-slate-900/60 border border-slate-800/80 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-brand-600/20 text-brand-400 font-bold flex items-center justify-center text-sm border border-brand-500/30 flex-shrink-0">
                <?= strtoupper(substr($authUser['name'] ?? 'A', 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <div class="text-xs font-bold text-white truncate"><?= e($authUser['name'] ?? 'Staff') ?></div>
                <div class="text-[10px] text-brand-400 font-medium truncate"><?= e($authUser['role_display'] ?? 'Administrator') ?></div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 space-y-1 text-sm font-medium overflow-y-auto">
            <a href="<?= url('admin') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <i data-lucide="layout-dashboard" class="w-4 h-4 text-brand-400"></i>
                Dashboard
            </a>

            <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Event Operations</div>

            <a href="<?= url('admin/events') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                Events Management
            </a>

            <a href="<?= url('admin/tickets') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <i data-lucide="tags" class="w-4 h-4 text-slate-400"></i>
                Ticket Inventory
            </a>

            <a href="<?= url('admin/categories') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <i data-lucide="layers" class="w-4 h-4 text-slate-400"></i>
                Categories
            </a>

            <a href="<?= url('admin/venues') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i>
                Venues
            </a>

            <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Finance & Sales</div>

            <a href="<?= url('admin/payments') ?>" class="flex items-center justify-between px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <div class="flex items-center gap-3">
                    <i data-lucide="credit-card" class="w-4 h-4 text-slate-400"></i>
                    Payments Queue
                </div>
                <?php if ($pendingCount > 0): ?>
                    <span class="text-xs bg-amber-500/20 text-amber-300 font-bold px-2 py-0.5 rounded-full border border-amber-500/40 animate-pulse">
                        <?= $pendingCount ?>
                    </span>
                <?php endif; ?>
            </a>

            <a href="<?= url('admin/bookings') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <i data-lucide="receipt" class="w-4 h-4 text-slate-400"></i>
                All Bookings
            </a>

            <a href="<?= url('admin/customers') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <i data-lucide="users" class="w-4 h-4 text-slate-400"></i>
                Customer Directory
            </a>

            <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Gate & Analytics</div>

            <a href="<?= url('gate/scan') ?>" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-xl text-emerald-400 hover:bg-emerald-950/30 transition-colors">
                <i data-lucide="qr-code" class="w-4 h-4 text-emerald-400"></i>
                Gate QR Scanner
            </a>

            <a href="<?= url('admin/reports') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-slate-400"></i>
                Reports & Export
            </a>

            <?php if (\App\Core\Auth::hasRole('super_admin')): ?>
            <div class="pt-4 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">System Admin</div>

            <a href="<?= url('admin/users') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <i data-lucide="user-check" class="w-4 h-4 text-slate-400"></i>
                Staff & Roles
            </a>

            <a href="<?= url('admin/audit-logs') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <i data-lucide="file-clock" class="w-4 h-4 text-slate-400"></i>
                Audit Logs
            </a>

            <a href="<?= url('admin/settings') ?>" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                <i data-lucide="settings" class="w-4 h-4 text-slate-400"></i>
                System Settings
            </a>
            <?php endif; ?>
        </nav>

        <!-- Quick Sign Out -->
        <div class="p-3 border-t border-slate-800">
            <form action="<?= url('admin/logout') ?>" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl bg-slate-900 text-xs font-semibold text-rose-400 hover:bg-rose-950/20 border border-slate-800 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Container -->
    <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
        <!-- Top Navigation Bar -->
        <header class="h-20 bg-dark-900 border-b border-slate-800 px-4 sm:px-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleAdminSidebar()" class="md:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800" aria-label="Toggle navigation">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <a href="<?= url('/') ?>" target="_blank" class="hidden sm:flex items-center gap-1.5 text-xs text-slate-400 hover:text-white transition-colors">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    View Public Portal
                </a>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                <a href="<?= url('gate/scan') ?>" target="_blank" class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg bg-emerald-950/80 border border-emerald-500/30 text-emerald-300 text-xs font-bold hover:bg-emerald-900/80 transition-colors">
                    <i data-lucide="camera" class="w-4 h-4"></i> <span class="hidden sm:inline">Gate Scanner</span><span class="sm:hidden">Scan</span>
                </a>
                <a href="<?= url('admin/events/create') ?>" class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg gradient-brand text-white text-xs font-bold hover:opacity-90 shadow-md shadow-brand-600/20">
                    <i data-lucide="plus" class="w-4 h-4"></i> <span class="hidden sm:inline">New Event</span><span class="sm:hidden">Event</span>
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-8">
            <?= $content ?>
        </main>
    </div>

    <script>
        lucide.createIcons();
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-backdrop');
            if (!sidebar || !backdrop) return;
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
