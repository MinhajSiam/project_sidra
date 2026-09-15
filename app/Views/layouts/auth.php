<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?><?= e($appName) ?></title>

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
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        dark: {
                            900: '#0b0f19',
                            800: '#111827',
                            700: '#1e293b',
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
<body class="bg-[#0b0f19] text-slate-100 min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-radial-gradient">

    <!-- Flash Toast -->
    <?php if ($flashSuccess || $flashError || $flashWarning || $flashInfo): ?>
    <div id="flash-toast" class="fixed top-6 left-1/2 -translate-x-1/2 z-50 max-w-lg w-[90%] toast-animate cursor-pointer shadow-2xl rounded-xl p-4 flex items-center gap-3.5 border <?= $flashSuccess ? 'bg-emerald-950/90 border-emerald-500/40 text-emerald-200' : ($flashError ? 'bg-rose-950/90 border-rose-500/40 text-rose-200' : 'bg-slate-900/90 border-brand-500/40 text-indigo-200') ?>" onclick="this.remove()">
        <i data-lucide="<?= $flashSuccess ? 'check-circle-2' : ($flashError ? 'alert-octagon' : 'info') ?>" class="w-5 h-5 flex-shrink-0"></i>
        <div class="text-sm font-medium leading-snug flex-1">
            <?= $flashSuccess ?: ($flashError ?: ($flashWarning ?: $flashInfo)) ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="<?= url('/') ?>" class="inline-flex items-center gap-3 group">
            <div class="w-12 h-12 rounded-2xl gradient-brand flex items-center justify-center shadow-xl shadow-brand-500/25 group-hover:scale-105 transition-transform duration-300">
                <i data-lucide="ticket" class="w-6 h-6 text-white"></i>
            </div>
            <span class="font-heading font-extrabold text-3xl tracking-tight text-white flex items-center gap-1">
                SIDRA
                <span class="w-2 h-2 rounded-full bg-brand-500 inline-block"></span>
            </span>
        </a>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="glass-card py-8 px-6 shadow-2xl rounded-2xl sm:px-10 border border-slate-800/80">
            <?= $content ?>
        </div>
    </div>

    <script>lucide.createIcons();</script>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
