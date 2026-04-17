import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card'];

    connect() {
        this.selectedSubscription = '';
        this._onSubscriptionSelected = this.onSubscriptionSelected.bind(this);
        document.addEventListener('subscription:selected', this._onSubscriptionSelected);

        const selectedCard = this.cardTargets.find(card => card.classList.contains('offer-card--selected'));
        if (selectedCard) {
            const cardIndex = this.cardTargets.indexOf(selectedCard);
            this.updateSubscriptionCards(cardIndex);
        }
    }

    disconnect() {
        document.removeEventListener('subscription:selected', this._onSubscriptionSelected);
    }

    onSubscriptionSelected(event) {
        this.selectedSubscription = event.detail.value;
        this.updateAllButtons();
    }

    select(event) {
        if (event.target.closest('.btn-offer')) return;

        const clickedCard = event.currentTarget;
        const cardIndex = this.cardTargets.indexOf(clickedCard);

        this.cardTargets.forEach(card => {
            card.classList.remove('offer-card--selected');
        });

        clickedCard.classList.add('offer-card--selected');

        const tableHeaders = document.querySelectorAll('.comparison-plan-col');
        tableHeaders.forEach((header, index) => {
            header.classList.remove('comparison-plan-col--active');
            if (index === cardIndex) {
                header.classList.add('comparison-plan-col--active');
            }
        });

        this.updateSubscriptionCards(cardIndex);
        this.updateAllButtons();
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
                this.selectedSubscription = card.dataset.subscriptionValue || '';
            }
        });
        this.updateAllButtons();
    }

    updateAllButtons() {
        this.cardTargets.forEach(card => {
            const offerValue = card.dataset.offerValue;
            const btn = card.querySelector('.btn-offer');
            if (!btn || !offerValue) return;

            let href = `/contact?offer=${offerValue}`;
            if (this.selectedSubscription) {
                href += `&subscription=${this.selectedSubscription}`;
            }
            btn.href = href;
        });
    }
}
