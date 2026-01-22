import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['item'];

    connect() {
        this.observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        const delay = entry.target.dataset.delay || 0;
                        setTimeout(() => {
                            entry.target.classList.add('is-visible');
                        }, delay);
                        this.observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            }
        );

        this.itemTargets.forEach((item) => {
            this.observer.observe(item);
        });
    }

    disconnect() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }
}
