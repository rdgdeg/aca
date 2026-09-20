document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.reveal').forEach((el, index) => {
        el.style.animationDelay = `${Math.min(index * 40, 400)}ms`;
    });
});

window.acaFavs = {
    key: 'aca-favs',
    ids() {
        try {
            return JSON.parse(localStorage.getItem(this.key) || '[]').map(Number);
        } catch {
            return [];
        }
    },
    has(id) {
        return this.ids().includes(Number(id));
    },
    toggle(id) {
        const value = Number(id);
        const next = this.has(value)
            ? this.ids().filter((item) => item !== value)
            : [...this.ids(), value];
        localStorage.setItem(this.key, JSON.stringify(next));
        document.dispatchEvent(new CustomEvent('aca-favs', { detail: next }));

        return next;
    },
};

window.acaSelectMerchant = function (id) {
    document.querySelectorAll('[data-merchant-id].is-selected').forEach((el) => {
        el.classList.remove('is-selected');
    });
    const card = document.querySelector(`[data-merchant-id="${id}"]`);
    if (card) {
        card.classList.add('is-selected');
    }
    if (typeof window.acaPulsePin === 'function') {
        window.acaPulsePin(id);
    }
};
