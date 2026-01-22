import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card', 'buttonMore', 'buttonLess'];
    static values = {
        visible: { type: Number, default: 2 },
        increment: { type: Number, default: 2 },
        initial: { type: Number, default: 2 }
    }

    connect() {
        this.initializeCards();
    }

    initializeCards() {
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            // En mobile, masquer les cartes au-delà de la 2ème
            this.cardTargets.forEach((card, index) => {
                if (index >= this.visibleValue) {
                    card.classList.add('portfolio-card--hidden');
                } else {
                    card.classList.remove('portfolio-card--hidden');
                }
            });
            this.updateButtons();
        } else {
            // Sur desktop, toutes les cartes sont visibles
            this.cardTargets.forEach(card => {
                card.classList.remove('portfolio-card--hidden');
            });
            if (this.hasButtonMoreTarget) {
                this.buttonMoreTarget.style.display = 'none';
            }
            if (this.hasButtonLessTarget) {
                this.buttonLessTarget.style.display = 'none';
            }
        }
    }

    showMore(event) {
        event.preventDefault();
        this.visibleValue += this.incrementValue;
        this.updateVisibility();
    }

    showLess(event) {
        event.preventDefault();
        this.visibleValue = this.initialValue;
        this.updateVisibility();
    }

    updateVisibility() {
        const totalCards = this.cardTargets.length;
        
        this.cardTargets.forEach((card, index) => {
            if (index < this.visibleValue) {
                card.classList.remove('portfolio-card--hidden');
            } else {
                card.classList.add('portfolio-card--hidden');
            }
        });

        this.updateButtons();
    }

    updateButtons() {
        const totalCards = this.cardTargets.length;
        const isMobile = window.innerWidth <= 768;
        
        if (!isMobile) {
            if (this.hasButtonMoreTarget) {
                this.buttonMoreTarget.style.display = 'none';
            }
            if (this.hasButtonLessTarget) {
                this.buttonLessTarget.style.display = 'none';
            }
            return;
        }

        // Gérer le bouton "Voir plus"
        if (this.hasButtonMoreTarget) {
            if (this.visibleValue >= totalCards) {
                this.buttonMoreTarget.style.display = 'none';
            } else {
                this.buttonMoreTarget.style.display = 'inline-block';
            }
        }

        // Gérer le bouton "Voir moins"
        if (this.hasButtonLessTarget) {
            if (this.visibleValue > this.initialValue) {
                this.buttonLessTarget.style.display = 'inline-block';
            } else {
                this.buttonLessTarget.style.display = 'none';
            }
        }
    }
}
