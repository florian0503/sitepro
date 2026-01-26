import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['cta', 'scrollTop', 'chatbot'];

    connect() {
        this.checkPosition = this.checkPosition.bind(this);

        window.addEventListener('scroll', this.checkPosition);
        window.addEventListener('resize', this.checkPosition);

        this.checkPosition();
    }

    disconnect() {
        window.removeEventListener('scroll', this.checkPosition);
        window.removeEventListener('resize', this.checkPosition);
    }

    checkPosition() {
        const blueSections = document.querySelectorAll('section.cta, .formule-card--selected, .offer-card--selected');

        if (this.hasCtaTarget) {
            this.updateButtonStyle(this.ctaTarget, blueSections);
        }
        if (this.hasScrollTopTarget) {
            this.updateButtonStyle(this.scrollTopTarget, blueSections);
        }
        if (this.hasChatbotTarget) {
            this.updateButtonStyle(this.chatbotTarget, blueSections);
        }
    }

    updateButtonStyle(button, blueSections) {
        const buttonRect = button.getBoundingClientRect();
        const buttonCenterX = buttonRect.left + buttonRect.width / 2;
        const buttonCenterY = buttonRect.top + buttonRect.height / 2;

        let isOverBlue = false;

        blueSections.forEach(section => {
            const sectionRect = section.getBoundingClientRect();
            const isFullWidth = section.tagName === 'SECTION';

            const verticalOverlap = buttonCenterY >= sectionRect.top && buttonCenterY <= sectionRect.bottom;

            if (isFullWidth) {
                if (verticalOverlap) {
                    isOverBlue = true;
                }
            } else {
                const horizontalOverlap = buttonCenterX >= sectionRect.left && buttonCenterX <= sectionRect.right;
                if (verticalOverlap && horizontalOverlap) {
                    isOverBlue = true;
                }
            }
        });

        if (isOverBlue) {
            button.classList.add('is-over-blue');
        } else {
            button.classList.remove('is-over-blue');
        }
    }
}
