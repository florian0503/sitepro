import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['card'];

    select(event) {
        const clickedCard = event.currentTarget;

        if (clickedCard.classList.contains('subscription-card--disabled')) return;

        this.cardTargets.forEach(card => {
            card.classList.remove('subscription-card--selected');
            card.classList.remove('subscription-card--popular');
        });

        clickedCard.classList.add('subscription-card--selected');

        const subscriptionValue = clickedCard.dataset.subscriptionValue || '';
        document.dispatchEvent(new CustomEvent('subscription:selected', { detail: { value: subscriptionValue } }));
    }
}
