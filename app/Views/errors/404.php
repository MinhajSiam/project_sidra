<?php $pageTitle = '404 — Page Not Found'; ?>

<div class="min-h-[70vh] flex items-center justify-center py-16 px-4">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="relative mx-auto w-24 h-24 rounded-3xl bg-brand-500/10 border border-brand-500/30 flex items-center justify-center text-brand-400 shadow-2xl">
            <i data-lucide="compass" class="w-12 h-12"></i>
            <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-brand-600 flex items-center justify-center text-white text-xs font-bold font-mono">?</div>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold font-mono uppercase tracking-widest text-brand-400">Error 404 &bull; Not Found</span>
            <h1 class="text-3xl font-extrabold font-heading text-white">Lost Your Way?</h1>
            <p class="text-sm text-slate-400 leading-relaxed">
                The event, ticket pass, or page you were looking for doesn't exist, has been moved, or may have concluded.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="/" class="w-full sm:w-auto px-5 py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-lg shadow-brand-600/20">
                <i data-lucide="home" class="w-4 h-4"></i> Back to Homepage
            </a>
            <a href="/events" class="w-full sm:w-auto px-5 py-2.5 bg-dark-800 hover:bg-dark-700 text-slate-200 border border-dark-600 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4"></i> Browse Events
            </a>
        </div>
    </div>
</div>
