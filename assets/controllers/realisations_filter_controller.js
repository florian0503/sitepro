import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['filterBtn', 'card', 'loadMoreBtn'];

    connect() {
        this.visibleCount = 2; // Start with 2 cards on mobile
        this.updateVisibility('all');
        window.addEventListener('resize', this.handleResize.bind(this));
    }

    disconnect() {
        window.removeEventListener('resize', this.handleResize.bind(this));
    }

    handleResize() {
        const activeBtn = this.filterBtnTargets.find(btn => btn.classList.contains('is-active'));
        const filter = activeBtn ? activeBtn.dataset.filter : 'all';
        this.updateVisibility(filter);
    }

    isMobile() {
        return window.innerWidth <= 768;
    }

    filter(event) {
        const filter = event.currentTarget.dataset.filter;
        this.visibleCount = 2; // Reset to 2 when changing filter

        // Update active state
        this.filterBtnTargets.forEach(btn => btn.classList.remove('is-active'));
        event.currentTarget.classList.add('is-active');

        this.updateVisibility(filter);
    }

    loadMore() {
        this.visibleCount += 2; // Show 2 more cards
        const activeBtn = this.filterBtnTargets.find(btn => btn.classList.contains('is-active'));
        const filter = activeBtn ? activeBtn.dataset.filter : 'all';
        this.updateVisibility(filter);
    }

    updateVisibility(filter) {
        let displayedCount = 0;
        let totalMatching = 0;

        this.cardTargets.forEach(card => {
            const matchesFilter = filter === 'all' || card.dataset.category === filter;

            if (matchesFilter) {
                totalMatching++;
                // On desktop: show all, on mobile: respect visibleCount
                if (!this.isMobile() || displayedCount < this.visibleCount) {
                    card.style.display = '';
                    card.classList.remove('is-hidden');
                    displayedCount++;
                } else {
                    card.style.display = 'none';
                    card.classList.add('is-hidden');
                }
            } else {
                card.style.display = 'none';
            }
        });

        if (this.hasLoadMoreBtnTarget) {
            // Show button only on mobile when there are more cards to show
            if (!this.isMobile() || displayedCount >= totalMatching) {
                this.loadMoreBtnTarget.classList.add('is-hidden');
            } else {
                this.loadMoreBtnTarget.classList.remove('is-hidden');
            }
        }
    }
}
