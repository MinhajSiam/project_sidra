<?php $pageTitle = '403 — Access Forbidden'; ?>

<div class="min-h-[70vh] flex items-center justify-center py-16 px-4">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="relative mx-auto w-24 h-24 rounded-3xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400 shadow-2xl">
            <i data-lucide="shield-alert" class="w-12 h-12"></i>
            <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-rose-500 flex items-center justify-center text-white text-xs font-bold font-mono">!</div>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold font-mono uppercase tracking-widest text-rose-400">Error 403 &bull; Forbidden</span>
            <h1 class="text-3xl font-extrabold font-heading text-white">Access Denied</h1>
            <p class="text-sm text-slate-400 leading-relaxed">
                You do not have the necessary role permissions or privileges to view this administrative department or resource.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="javascript:history.back()" class="w-full sm:w-auto px-5 py-2.5 bg-dark-800 hover:bg-dark-700 text-slate-200 border border-dark-600 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Go Back
            </a>
            <a href="/admin/dashboard" class="w-full sm:w-auto px-5 py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-lg shadow-brand-600/20">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Admin Portal
            </a>
        </div>
    </div>
</div>
