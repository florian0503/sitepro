import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['filterBtn', 'card', 'loadMoreBtn'];

    connect() {
        this.visibleCount = this.getInitialVisibleCount();
        this.updateVisibility('all');
        window.addEventListener('resize', this.handleResize.bind(this));
    }

    disconnect() {
        window.removeEventListener('resize', this.handleResize.bind(this));
    }

    handleResize() {
        const activeBtn = this.filterBtnTargets.find(btn => btn.classList.contains('is-active'));
        const filter = activeBtn ? activeBtn.dataset.filter : 'all';
        this.visibleCount = this.getInitialVisibleCount();
        this.updateVisibility(filter);
    }

    getInitialVisibleCount() {
        if (window.innerWidth > 1024) {
            return 6; // Desktop: 2 rows of 3
        } else if (window.innerWidth > 768) {
            return 4; // Tablet: 2 rows of 2
        }
        return 2; // Mobile: 2 cards
    }

    getLoadMoreCount() {
        if (window.innerWidth > 1024) {
            return 3; // Desktop: 1 row of 3
        } else if (window.innerWidth > 768) {
            return 2; // Tablet: 1 row of 2
        }
        return 2; // Mobile: 2 cards
    }

    filter(event) {
        const filter = event.currentTarget.dataset.filter;
        this.visibleCount = this.getInitialVisibleCount();

        // Update active state
        this.filterBtnTargets.forEach(btn => btn.classList.remove('is-active'));
        event.currentTarget.classList.add('is-active');

        this.updateVisibility(filter);
    }

    loadMore() {
        this.visibleCount += this.getLoadMoreCount();
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
                if (displayedCount < this.visibleCount) {
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
            if (displayedCount >= totalMatching) {
                this.loadMoreBtnTarget.classList.add('is-hidden');
            } else {
                this.loadMoreBtnTarget.classList.remove('is-hidden');
            }
        }
    }
}
