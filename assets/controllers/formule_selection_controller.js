import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card'];

    select(event) {
        const clickedCard = event.currentTarget;

        // Retirer la sélection et le highlight de toutes les cartes
        this.cardTargets.forEach(card => {
            card.classList.remove('formule-card--selected');
            card.classList.remove('formule-card--highlight');
        });

        // Ajouter la sélection à la carte cliquée
        clickedCard.classList.add('formule-card--selected');
    }
}
