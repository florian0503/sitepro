import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['track'];

    connect() {
        console.log('Scroll carousel connected');
        this.onWheel = this.onWheel.bind(this);
        document.addEventListener('wheel', this.onWheel, { passive: false });
    }

    disconnect() {
        document.removeEventListener('wheel', this.onWheel);
    }

    onWheel(e) {
        const track = this.trackTarget;
        const rect = this.element.getBoundingClientRect();

        // Check if section is visible
        if (rect.top > window.innerHeight * 0.5 || rect.bottom < window.innerHeight * 0.5) {
            return;
        }

        const maxScroll = track.scrollWidth - track.clientWidth;
        const atStart = track.scrollLeft <= 0;
        const atEnd = track.scrollLeft >= maxScroll - 1;

        // Scroll down but not at end
        if (e.deltaY > 0 && !atEnd) {
            e.preventDefault();
            track.scrollLeft += Math.abs(e.deltaY);
        }
        // Scroll up but not at start
        else if (e.deltaY < 0 && !atStart) {
            e.preventDefault();
            track.scrollLeft -= Math.abs(e.deltaY);
        }
    }
}
