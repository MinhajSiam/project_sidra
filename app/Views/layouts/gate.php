<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Gate Scanner — <?= e($appName) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#6366f1',
                            600: '#4f46e5',
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
    <!-- HTML5-QRCode Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-black text-white min-h-full flex flex-col antialiased">
    <!-- Compact Gate Staff Header -->
    <header class="h-16 bg-slate-900 border-b border-slate-800 px-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg gradient-brand flex items-center justify-center">
                <i data-lucide="qr-code" class="w-4 h-4 text-white"></i>
            </div>
            <div>
                <span class="font-heading font-extrabold text-base tracking-tight text-white flex items-center gap-1.5">
                    GATE SCANNER
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-ping"></span>
                </span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-400 font-medium hidden sm:inline">
                Staff: <strong class="text-white"><?= e($staffUser['name'] ?? 'Gate Guard') ?></strong>
            </span>
            <a href="<?= url('admin') ?>" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300 transition-colors">
                Exit to Portal
            </a>
        </div>
    </header>

    <main class="flex-1 flex flex-col p-4 max-w-4xl mx-auto w-full">
        <?= $content ?>
    </main>

    <script>lucide.createIcons();</script>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
