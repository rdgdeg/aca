document.addEventListener('DOMContentLoaded', () => {
    const reveals = document.querySelectorAll('.reveal');
    const show = (el) => el.classList.add('is-visible');

    if (! ('IntersectionObserver' in window)) {
        reveals.forEach(show);
        return;
    }

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                show(entry.target);
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -8% 0px' });

    reveals.forEach((el, index) => {
        if (! el.style.transitionDelay) {
            el.style.setProperty('--delay', `${Math.min(index * 50, 240)}ms`);
        }

        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight - 24 && rect.bottom > 0) {
            show(el);
        } else {
            io.observe(el);
        }
    });

    window.setTimeout(() => reveals.forEach(show), 1200);
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
