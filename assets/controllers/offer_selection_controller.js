import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card'];

    select(event) {
        const clickedCard = event.currentTarget;
        const cardIndex = this.cardTargets.indexOf(clickedCard);

        // Retirer la sélection de toutes les cartes
        this.cardTargets.forEach(card => {
            card.classList.remove('offer-card--selected');
        });

        // Ajouter la sélection à la carte cliquée
        clickedCard.classList.add('offer-card--selected');

        // Mettre à jour les en-têtes du tableau
        const tableHeaders = document.querySelectorAll('.comparison-plan-col');
        tableHeaders.forEach((header, index) => {
            header.classList.remove('comparison-plan-col--active');
            if (index === cardIndex) {
                header.classList.add('comparison-plan-col--active');
            }
        });
    }
}
