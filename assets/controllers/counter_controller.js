import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['number'];

    connect() {
        this.animated = false;
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.animated) {
                    this.animated = true;
                    this.animateCounters();
                }
            });
        }, { threshold: 0.5 });

        this.observer.observe(this.element);
    }

    disconnect() {
        if (this.observer) {
            this.observer.disconnect();
        }
    }

    animateCounters() {
        this.numberTargets.forEach(target => {
            const text = target.textContent;
            const hasPlus = text.includes('+');
            const hasPercent = text.includes('%');
            const hasJ = text.includes('j');
            
            let endValue = parseInt(text.replace(/[^0-9]/g, ''));
            let suffix = '';
            
            if (hasPlus) suffix = '+';
            if (hasPercent) suffix = '%';
            if (hasJ) suffix = 'j';

            let startValue = 0;
            const duration = 2000;
            const startTime = performance.now();

            const animate = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                const currentValue = Math.floor(easeOutQuart * endValue);
                
                target.textContent = currentValue + suffix;

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    target.textContent = endValue + suffix;
                }
            };

            requestAnimationFrame(animate);
        });
    }
}
