<?php $pageTitle = '500 — System Error'; ?>

<div class="min-h-[70vh] flex items-center justify-center py-16 px-4">
    <div class="max-w-xl w-full text-center space-y-6">
        <div class="relative mx-auto w-24 h-24 rounded-3xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400 shadow-2xl">
            <i data-lucide="server-crash" class="w-12 h-12"></i>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold font-mono uppercase tracking-widest text-rose-400">Error 500 &bull; Server Exception</span>
            <h1 class="text-3xl font-extrabold font-heading text-white">System Error Occurred</h1>
            <p class="text-sm text-slate-400 leading-relaxed">
                An internal server error occurred while processing your request. Our technical team has been logged of this incident.
            </p>
        </div>

        <?php if (!empty($exception)): ?>
            <div class="text-left bg-dark-900 border border-rose-500/30 rounded-2xl p-4 shadow space-y-2">
                <div class="flex items-center gap-2 text-rose-400 font-bold text-xs">
                    <i data-lucide="terminal" class="w-4 h-4"></i>
                    Debug Information:
                </div>
                <div class="font-mono text-xs text-white break-words">
                    <?= e($exception->getMessage()) ?>
                </div>
                <div class="font-mono text-[11px] text-slate-400">
                    in <?= e($exception->getFile()) ?>:<?= $exception->getLine() ?>
                </div>
                <details class="text-[11px] text-slate-400 pt-1 cursor-pointer">
                    <summary class="hover:text-white font-medium">Toggle Stack Trace</summary>
                    <pre class="mt-2 text-[10px] text-slate-400 overflow-x-auto p-2 bg-dark-800 rounded font-mono"><?= e($exception->getTraceAsString()) ?></pre>
                </details>
            </div>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="/" class="w-full sm:w-auto px-5 py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-lg shadow-brand-600/20">
                <i data-lucide="home" class="w-4 h-4"></i> Back to Homepage
            </a>
            <a href="javascript:location.reload()" class="w-full sm:w-auto px-5 py-2.5 bg-dark-800 hover:bg-dark-700 text-slate-200 border border-dark-600 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Retry Request
            </a>
        </div>
    </div>
</div>
