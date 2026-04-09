const checkboxes = document.querySelectorAll('.devis-checkbox');
const totalHtEl = document.getElementById('total-ht');
const totalMonthlyEl = document.getElementById('total-monthly');
const monthlySection = document.getElementById('monthly-section');
const categorySubtotals = document.querySelectorAll('.category-subtotal');
const selectedListEl = document.getElementById('selected-items-list');

function formatPrice(n) {
    return n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function recalculate() {
    let ht = 0;
    let monthly = 0;
    const catTotals = {};
    let selectedHtml = '';

    checkboxes.forEach(function(cb) {
        if (cb.checked) {
            const price = parseFloat(cb.dataset.price);
            const cat = cb.dataset.category;
            const label = cb.closest('.devis-item-row').querySelector('strong').textContent;
            if (cb.dataset.monthly === '1') {
                monthly += price;
                selectedHtml += '<div class="d-flex justify-content-between small mb-1"><span>' + label + '</span><span>' + formatPrice(price) + ' \u20ac/mois</span></div>';
            } else {
                ht += price;
                selectedHtml += '<div class="d-flex justify-content-between small mb-1"><span>' + label + '</span><span>' + formatPrice(price) + ' \u20ac</span></div>';
            }
            catTotals[cat] = (catTotals[cat] || 0) + price;
        }
    });

    totalHtEl.textContent = formatPrice(ht);
    totalMonthlyEl.textContent = formatPrice(monthly);
    monthlySection.style.display = monthly > 0 ? 'block' : 'none';

    if (selectedHtml) {
        selectedListEl.innerHTML = selectedHtml;
    } else {
        selectedListEl.innerHTML = '<p class="text-muted mb-0">Aucune prestation s\u00e9lectionn\u00e9e</p>';
    }

    categorySubtotals.forEach(function(el) {
        const cat = el.dataset.category;
        const val = catTotals[cat] || 0;
        el.textContent = formatPrice(val) + ' \u20ac';
        el.className = 'badge category-subtotal ' + (val > 0 ? 'bg-primary' : 'bg-secondary');
    });
}

checkboxes.forEach(function(cb) {
    cb.addEventListener('change', function() {
        this.closest('.devis-item-row').classList.toggle('selected', this.checked);
        recalculate();
    });
});
