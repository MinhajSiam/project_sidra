/**
 * SIDRA EVENT TICKETING PLATFORM - CORE JAVASCRIPT
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Toast Auto-Dismissal
    const toast = document.getElementById('flash-toast');
    if (toast) {
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translate(-50%, -20px)';
            toast.style.transition = 'all 0.4s ease';
            setTimeout(() => toast.remove(), 400);
        }, 6000);
    }

    // 2. Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // 3. Dynamic Ticket Quantity & Price Calculator
    const ticketInputs = document.querySelectorAll('.ticket-qty-input');
    const totalDisplay = document.getElementById('checkout-total-display');
    const checkoutSubmitBtn = document.getElementById('checkout-submit-btn');

    function updateCheckoutTotal() {
        if (!totalDisplay) return;
        let total = 0;
        let totalCount = 0;

        ticketInputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            const price = parseFloat(input.dataset.price) || 0;
            total += (qty * price);
            totalCount += qty;

            const rowSubtotal = input.closest('.ticket-tier-row')?.querySelector('.tier-subtotal');
            if (rowSubtotal) {
                rowSubtotal.textContent = '৳ ' + (qty * price).toLocaleString('en-US', { minimumFractionDigits: 2 });
            }
        });

        totalDisplay.textContent = '৳ ' + total.toLocaleString('en-US', { minimumFractionDigits: 2 });
        if (checkoutSubmitBtn) {
            checkoutSubmitBtn.disabled = totalCount === 0;
            if (totalCount === 0) {
                checkoutSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                checkoutSubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    // Bind quantity increment / decrement buttons
    document.querySelectorAll('.qty-btn-plus').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.qty-control').querySelector('.ticket-qty-input');
            const max = parseInt(input.getAttribute('max')) || 10;
            let current = parseInt(input.value) || 0;
            if (current < max) {
                input.value = current + 1;
                updateCheckoutTotal();
            }
        });
    });

    document.querySelectorAll('.qty-btn-minus').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.qty-control').querySelector('.ticket-qty-input');
            let current = parseInt(input.value) || 0;
            if (current > 0) {
                input.value = current - 1;
                updateCheckoutTotal();
            }
        });
    });

    ticketInputs.forEach(input => {
        input.addEventListener('change', updateCheckoutTotal);
    });

    updateCheckoutTotal();
});

// Modal Helpers
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
