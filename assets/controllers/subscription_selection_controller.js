import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card'];

    select(event) {
        const clickedCard = event.currentTarget;

        // Retirer la sélection et le style popular de toutes les cartes
        this.cardTargets.forEach(card => {
            card.classList.remove('subscription-card--selected');
            card.classList.remove('subscription-card--popular');
        });

        // Ajouter la sélection à la carte cliquée
        clickedCard.classList.add('subscription-card--selected');
    }
}
