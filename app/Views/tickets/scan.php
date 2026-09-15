<div class="space-y-6">
    <!-- Active Event Selector & Gate Stats Bar -->
    <div class="glass-card rounded-2xl p-4 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <form method="GET" class="flex items-center gap-3">
            <label class="text-xs font-bold text-slate-400 uppercase whitespace-nowrap">Gate Event:</label>
            <select name="event_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-xs font-bold text-white focus:outline-none focus:border-brand-500 cursor-pointer max-w-[280px]">
                <?php foreach ($events as $ev): ?>
                    <option value="<?= $ev['id'] ?>" <?= $selectedEventId === (int)$ev['id'] ? 'selected' : '' ?>>
                        <?= e($ev['title']) ?> (<?= format_date($ev['event_date']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($stats): ?>
            <div class="flex items-center gap-4 text-xs">
                <div class="text-center">
                    <div class="text-[10px] uppercase font-bold text-slate-400">Total Passes</div>
                    <div class="font-heading font-extrabold text-white text-base"><?= $stats['total_issued'] ?></div>
                </div>
                <div class="text-center">
                    <div class="text-[10px] uppercase font-bold text-emerald-400">Checked In</div>
                    <div class="font-heading font-extrabold text-emerald-400 text-base" id="stat-checked-in"><?= $stats['total_checked_in'] ?></div>
                </div>
                <div class="text-center">
                    <div class="text-[10px] uppercase font-bold text-rose-400">Duplicates</div>
                    <div class="font-heading font-extrabold text-rose-400 text-base" id="stat-duplicates"><?= $stats['duplicate_attempts'] ?></div>
                </div>
                <div class="text-center">
                    <div class="text-[10px] uppercase font-bold text-slate-400">Remaining</div>
                    <div class="font-heading font-extrabold text-slate-300 text-base"><?= $stats['remaining_at_gate'] ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scanner Live Feedback Alert Banner -->
    <div id="scan-result-banner" class="hidden rounded-2xl p-5 border shadow-2xl transition-all">
        <div class="flex items-center gap-4">
            <div id="scan-result-icon" class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-xl font-bold"></div>
            <div class="flex-1">
                <div id="scan-result-title" class="font-heading font-extrabold text-xl tracking-tight"></div>
                <div id="scan-result-message" class="text-xs font-semibold mt-0.5 opacity-90"></div>
                <div id="scan-result-details" class="text-xs mt-1 space-x-2 font-mono"></div>
            </div>
        </div>
    </div>

    <!-- Camera Viewport & Manual Code Entry Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Camera Scanner Area (2 Cols) -->
        <div class="md:col-span-2 glass-card rounded-3xl p-6 space-y-4 border border-slate-800 flex flex-col items-center">
            <div class="w-full flex items-center justify-between">
                <span class="text-xs font-bold text-white flex items-center gap-2">
                    <i data-lucide="camera" class="w-4 h-4 text-emerald-400"></i>
                    Live Camera Feed
                </span>
                <span id="camera-status" class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Ready</span>
            </div>

            <!-- HTML5 Scanner Container -->
            <div class="relative w-full max-w-sm aspect-square bg-slate-950 rounded-2xl overflow-hidden border-2 border-slate-700 flex items-center justify-center shadow-2xl">
                <div id="reader" class="w-full h-full"></div>
                <div class="scan-laser pointer-events-none"></div>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" id="toggle-camera-btn" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white transition-colors">
                    Restart Camera
                </button>
            </div>
        </div>

        <!-- Manual Code Entry Box & Gate Instructions (1 Col) -->
        <div class="space-y-6">
            <div class="glass-card rounded-3xl p-6 space-y-4 border border-slate-800">
                <h3 class="font-heading font-bold text-base text-white flex items-center gap-2">
                    <i data-lucide="keyboard" class="w-4 h-4 text-brand-400"></i>
                    Manual Ticket Entry
                </h3>
                <p class="text-xs text-slate-400">If QR code cannot be scanned, type the code printed on the pass.</p>

                <form id="manual-verify-form" onsubmit="handleManualSubmit(event)" class="space-y-3">
                    <input type="text" id="manual-ticket-code" required placeholder="e.g. SDR-TKT-A1B2-C3D4" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-sm font-mono uppercase text-white focus:outline-none focus:border-brand-500">
                    <button type="submit" class="w-full py-3 rounded-xl gradient-brand text-white font-bold text-xs shadow-lg hover:opacity-95 transition-opacity">
                        Verify Code
                    </button>
                </form>
            </div>

            <div class="glass-card rounded-3xl p-6 space-y-3 border border-slate-800 text-xs text-slate-400">
                <div class="font-bold text-white uppercase text-[10px]">Gate Staff Instructions</div>
                <p>1. Point camera directly at the QR code on attendee phone or printout.</p>
                <p>2. Green chime indicates valid pass. Admit attendee immediately.</p>
                <p>3. Red buzz indicates duplicate or already admitted pass. Direct to resolution desk.</p>
            </div>
        </div>
    </div>

    <!-- Recent Gate Scans Log Table -->
    <div class="glass-card rounded-3xl p-6 space-y-4 border border-slate-800">
        <h3 class="font-heading font-bold text-lg text-white">Recent Gate Scans</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-slate-400 uppercase font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Result</th>
                        <th class="px-4 py-3">Ticket Code</th>
                        <th class="px-4 py-3">Attendee</th>
                        <th class="px-4 py-3">Tier</th>
                        <th class="px-4 py-3">Gate</th>
                        <th class="px-4 py-3">Time</th>
                    </tr>
                </thead>
                <tbody id="recent-scans-tbody" class="divide-y divide-slate-800/60">
                    <?php if (empty($recentScans)): ?>
                        <tr id="no-scans-row">
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">No scans logged for this session yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentScans as $scan): ?>
                            <tr>
                                <td class="px-4 py-3 font-bold uppercase text-[10px]">
                                    <span class="px-2 py-0.5 rounded-full <?= $scan['result'] === 'valid' ? 'bg-emerald-950 text-emerald-400' : 'bg-rose-950 text-rose-400' ?>">
                                        <?= e($scan['result']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-white"><?= e($scan['ticket_code']) ?></td>
                                <td class="px-4 py-3 font-semibold"><?= e($scan['attendee_name']) ?></td>
                                <td class="px-4 py-3 text-slate-400"><?= e($scan['ticket_type_name']) ?></td>
                                <td class="px-4 py-3 text-slate-400"><?= e($scan['gate_name']) ?></td>
                                <td class="px-4 py-3 text-slate-500"><?= format_datetime($scan['scanned_at'], 'h:i:s A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
let html5QrCode = null;
let isScanningActive = true;

// Web Audio API Sound generator
const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
function playChime(type) {
    try {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);

        if (type === 'valid') {
            osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
            osc.frequency.setValueAtTime(880, audioCtx.currentTime + 0.1); // A5
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.35);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.35);
        } else {
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(160, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.4, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.5);
        }
    } catch(e) {}
}

function showResultBanner(status, title, message, details) {
    const banner = document.getElementById('scan-result-banner');
    const icon = document.getElementById('scan-result-icon');
    const titleEl = document.getElementById('scan-result-title');
    const msgEl = document.getElementById('scan-result-message');
    const detailsEl = document.getElementById('scan-result-details');

    banner.classList.remove('hidden', 'bg-emerald-950/90', 'border-emerald-500', 'text-emerald-200', 'bg-rose-950/90', 'border-rose-500', 'text-rose-200');

    if (status === 'valid') {
        banner.classList.add('bg-emerald-950/90', 'border-emerald-500', 'text-emerald-200');
        icon.className = 'w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center';
        icon.innerHTML = '✓';
        playChime('valid');

        // Increment stats
        const statEl = document.getElementById('stat-checked-in');
        if (statEl) statEl.textContent = parseInt(statEl.textContent || 0) + 1;
    } else {
        banner.classList.add('bg-rose-950/90', 'border-rose-500', 'text-rose-200');
        icon.className = 'w-12 h-12 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center';
        icon.innerHTML = '✕';
        playChime('error');

        if (status === 'duplicate') {
            const statEl = document.getElementById('stat-duplicates');
            if (statEl) statEl.textContent = parseInt(statEl.textContent || 0) + 1;
        }
    }

    titleEl.textContent = title;
    msgEl.textContent = message;
    detailsEl.innerHTML = details || '';
}

async function verifyIdentifier(identifier) {
    // If identifier is a full URL from QR, extract the verification token from path
    if (identifier.includes('/verify/')) {
        identifier = identifier.split('/verify/')[1].split('?')[0];
    }

    try {
        const response = await fetch('<?= url("gate/verify") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_token() ?>'
            },
            body: JSON.stringify({
                identifier: identifier,
                gate_name: 'Main Gate'
            })
        });

        const data = await response.json();

        let detailsHtml = '';
        if (data.ticket) {
            detailsHtml = `<span>Attendee: <strong>${data.ticket.attendee_name}</strong></span> | <span>Tier: <strong>${data.ticket.ticket_type_name}</strong></span> | <span>Code: <strong>${data.ticket.ticket_code}</strong></span>`;
        }

        showResultBanner(data.status, data.title, data.message, detailsHtml);

        // Prepend to recent scans table
        prependScanRow(data.status, data.ticket?.ticket_code || identifier, data.ticket?.attendee_name || 'N/A', data.ticket?.ticket_type_name || 'N/A', 'Main Gate', data.timestamp || 'Just now');

    } catch (err) {
        showResultBanner('invalid', 'NETWORK ERROR', 'Could not reach verification server. Please retry.', '');
    }
}

function prependScanRow(result, code, name, tier, gate, time) {
    const tbody = document.getElementById('recent-scans-tbody');
    const noRow = document.getElementById('no-scans-row');
    if (noRow) noRow.remove();

    const tr = document.createElement('tr');
    tr.className = 'bg-slate-800/60 animate-pulse';
    tr.innerHTML = `
        <td class="px-4 py-3 font-bold uppercase text-[10px]">
            <span class="px-2 py-0.5 rounded-full ${result === 'valid' ? 'bg-emerald-950 text-emerald-400' : 'bg-rose-950 text-rose-400'}">
                ${result}
            </span>
        </td>
        <td class="px-4 py-3 font-mono font-bold text-white">${code}</td>
        <td class="px-4 py-3 font-semibold">${name}</td>
        <td class="px-4 py-3 text-slate-400">${tier}</td>
        <td class="px-4 py-3 text-slate-400">${gate}</td>
        <td class="px-4 py-3 text-slate-500">${time}</td>
    `;
    tbody.insertBefore(tr, tbody.firstChild);
    setTimeout(() => tr.classList.remove('bg-slate-800/60', 'animate-pulse'), 1500);
}

function handleManualSubmit(e) {
    e.preventDefault();
    const code = document.getElementById('manual-ticket-code').value.trim();
    if (code) {
        verifyIdentifier(code);
        document.getElementById('manual-ticket-code').value = '';
    }
}

// Initialize Camera Scanner
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Html5Qrcode !== 'undefined') {
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 15, qrbox: { width: 250, height: 250 } };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                if (!isScanningActive) return;
                isScanningActive = false;
                verifyIdentifier(decodedText);
                // Pause 2 seconds before next scan to prevent duplicate camera read
                setTimeout(() => { isScanningActive = true; }, 2200);
            },
            (errorMessage) => {
                // scanning frame error ignored
            }
        ).catch(err => {
            document.getElementById('camera-status').textContent = 'Camera Inactive (Use Manual Code)';
            document.getElementById('camera-status').className = 'text-[10px] font-bold uppercase text-amber-400';
        });
    }
});
</script>
