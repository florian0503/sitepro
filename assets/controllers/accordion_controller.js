import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['item', 'content', 'icon'];

    toggle(event) {
        const item = event.currentTarget.closest('[data-accordion-target="item"]');
        const content = item.querySelector('[data-accordion-target="content"]');
        const icon = item.querySelector('[data-accordion-target="icon"]');

        const isOpen = item.classList.contains('active');

        this.itemTargets.forEach((i) => {
            i.classList.remove('active');
            const c = i.querySelector('[data-accordion-target="content"]');
            if (c) {
                c.style.maxHeight = null;
            }
        });

        if (!isOpen) {
            item.classList.add('active');
            if (content) {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        }
    }
}
