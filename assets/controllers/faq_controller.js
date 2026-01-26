import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['item', 'answer', 'icon'];

    toggle(event) {
        const item = event.currentTarget.closest('[data-faq-target="item"]');
        const answer = item.querySelector('[data-faq-target="answer"]');
        const icon = item.querySelector('[data-faq-target="icon"]');

        const isOpen = item.classList.contains('faq-item--open');

        // Close all other items
        this.itemTargets.forEach((otherItem) => {
            if (otherItem !== item && otherItem.classList.contains('faq-item--open')) {
                const otherAnswer = otherItem.querySelector('[data-faq-target="answer"]');
                const otherIcon = otherItem.querySelector('[data-faq-target="icon"]');
                otherItem.classList.remove('faq-item--open');
                otherAnswer.style.maxHeight = null;
                otherIcon.classList.remove('faq-icon--open');
            }
        });

        // Toggle current item
        if (isOpen) {
            item.classList.remove('faq-item--open');
            answer.style.maxHeight = null;
            icon.classList.remove('faq-icon--open');
        } else {
            item.classList.add('faq-item--open');
            answer.style.maxHeight = answer.scrollHeight + 'px';
            icon.classList.add('faq-icon--open');
        }
    }
}
