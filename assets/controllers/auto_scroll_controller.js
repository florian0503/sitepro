import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['track'];

    connect() {
        this.scrollSpeed = 1;
        this.scrollDirection = 1;
        this.isPaused = false;
        this.isMobile = window.innerWidth <= 768;

        if (this.isMobile) {
            this.startAutoScroll();
        }

        window.addEventListener('resize', () => {
            this.isMobile = window.innerWidth <= 768;
            if (this.isMobile && !this.animationId) {
                this.startAutoScroll();
            } else if (!this.isMobile && this.animationId) {
                this.stopAutoScroll();
            }
        });

        this.trackTarget.addEventListener('touchstart', () => this.pause());
        this.trackTarget.addEventListener('touchend', () => this.resume());
    }

    disconnect() {
        this.stopAutoScroll();
    }

    startAutoScroll() {
        const scroll = () => {
            if (!this.isPaused && this.isMobile) {
                const track = this.trackTarget;
                const maxScroll = track.scrollWidth - track.clientWidth;

                track.scrollLeft += this.scrollSpeed * this.scrollDirection;

                if (track.scrollLeft >= maxScroll) {
                    this.scrollDirection = -1;
                } else if (track.scrollLeft <= 0) {
                    this.scrollDirection = 1;
                }
            }
            this.animationId = requestAnimationFrame(scroll);
        };
        this.animationId = requestAnimationFrame(scroll);
    }

    stopAutoScroll() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
            this.animationId = null;
        }
    }

    pause() {
        this.isPaused = true;
    }

    resume() {
        setTimeout(() => {
            this.isPaused = false;
        }, 2000);
    }
}
