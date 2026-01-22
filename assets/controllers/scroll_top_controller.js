import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        window.addEventListener('scroll', this.toggleVisibility.bind(this));
        this.toggleVisibility();
    }

    disconnect() {
        window.removeEventListener('scroll', this.toggleVisibility.bind(this));
    }

    toggleVisibility() {
        if (window.scrollY > 300) {
            this.element.classList.add('scroll-top--visible');
        } else {
            this.element.classList.remove('scroll-top--visible');
        }
    }

    scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
}
