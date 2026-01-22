import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card'];
    static values = {
        visible: { type: Number, default: 4 }
    }

    connect() {
        this.initializeCards();
        // Écouter les changements de taille de fenêtre
        window.addEventListener('resize', () => this.initializeCards());
    }

    initializeCards() {
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            // En mobile, masquer les cartes au-delà de la 4ème
            this.cardTargets.forEach((card, index) => {
                if (index >= this.visibleValue) {
                    card.classList.add('reference-card--hidden');
                } else {
                    card.classList.remove('reference-card--hidden');
                }
            });
        } else {
            // Sur desktop, toutes les cartes sont visibles
            this.cardTargets.forEach(card => {
                card.classList.remove('reference-card--hidden');
            });
        }
    }
}
