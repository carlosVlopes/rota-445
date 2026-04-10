import './bootstrap';
import Alpine from 'alpinejs';

Alpine.data('orderPage', () => ({
    tab: new URLSearchParams(window.location.search).get('tab') ?? 'menu',
    showModal: false,
    showTicket: false,
    prod: null,
    qty: 1,
    notes: '',
    optVals: {},
    extraVals: {},
    items: window.__ORDER__?.items ?? [],
    total: window.__ORDER__?.total ?? 0,
    addUrl: window.__ORDER__?.addUrl ?? '',
    removeUrl: window.__ORDER__?.removeUrl ?? '',
    csrf: window.__ORDER__?.csrf ?? '',

    get pendingCount() {
        return this.items.filter(i => i.status === 'pending').length;
    },

    openProduct(p) {
        this.prod = p;
        this.qty = 1;
        this.notes = '';
        this.optVals = {};
        this.extraVals = {};
        this.showModal = true;
    },

    isExtraOn(optId, choiceId) {
        return !!this.extraVals[optId + '_' + choiceId];
    },

    toggleExtra(optId, choiceId) {
        const k = optId + '_' + choiceId;
        this.extraVals[k] = !this.extraVals[k];
    },

    fmt(n) {
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(n);
    },

    itemTotal(item) {
        const delta = item.options.reduce((s, o) => s + o.price_delta, 0);
        return (item.unit_price + delta) * item.quantity;
    },

    async addToOrder() {
        const fd = new FormData();
        fd.append('product_id', this.prod.id);
        fd.append('quantity', this.qty);
        if (this.notes.trim()) {
            fd.append('notes', this.notes.trim());
        }

        let i = 0;
        for (const opt of (this.prod.options ?? [])) {
            if (opt.type === 'toggle') {
                if (this.optVals[opt.id]) {
                    fd.append(`options[${i}][option_id]`, opt.id);
                    i++;
                }
            } else if (opt.type === 'select') {
                if (this.optVals[opt.id]) {
                    fd.append(`options[${i}][option_id]`, opt.id);
                    fd.append(`options[${i}][choice_id]`, this.optVals[opt.id]);
                    i++;
                }
            } else if (opt.type === 'extra') {
                for (const c of (opt.choices ?? [])) {
                    if (this.extraVals[opt.id + '_' + c.id]) {
                        fd.append(`options[${i}][option_id]`, opt.id);
                        fd.append(`options[${i}][choice_id]`, c.id);
                        i++;
                    }
                }
            } else if (opt.type === 'text') {
                const v = this.optVals[opt.id];
                if (v && String(v).trim()) {
                    fd.append(`options[${i}][option_id]`, opt.id);
                    fd.append(`options[${i}][text_value]`, String(v).trim());
                    i++;
                }
            }
        }

        const res = await fetch(this.addUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
            body: fd,
        });

        if (res.ok) {
            const data = await res.json();
            this.items.push(data.item);
            this.total = data.total;
            this.showModal = false;
        }
    },

    async removeOrderItem(itemId) {
        const url = this.removeUrl.replace('__ID__', itemId);

        const res = await fetch(url, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
        });

        if (res.ok) {
            const data = await res.json();
            this.items = this.items.filter(i => i.id !== itemId);
            this.total = data.total;
        }
    },
}));

window.Alpine = Alpine;
Alpine.start();
