import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card'];

    connect() {
        // Trouver la carte présélectionnée et appliquer la logique
        const selectedCard = this.cardTargets.find(card => card.classList.contains('offer-card--selected'));
        if (selectedCard) {
            const cardIndex = this.cardTargets.indexOf(selectedCard);
            this.updateSubscriptionCards(cardIndex);
        }
    }

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

        // Griser les abonnements inférieurs au minimum requis
        this.updateSubscriptionCards(cardIndex);
    }

    updateSubscriptionCards(cardIndex) {
        const subscriptionCards = document.querySelectorAll('.subscription-card');
        subscriptionCards.forEach((card, index) => {
            card.classList.remove('subscription-card--disabled');
            card.classList.remove('subscription-card--selected');
            card.classList.remove('subscription-card--popular');
            if (index < cardIndex) {
                card.classList.add('subscription-card--disabled');
            } else if (index === cardIndex) {
                card.classList.add('subscription-card--selected');
            }
        });
    }
}
