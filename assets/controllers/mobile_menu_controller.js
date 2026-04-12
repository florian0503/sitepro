import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['menu'];

    connect() {
        this.isOpen = false;
    }

    toggle() {
        this.isOpen = !this.isOpen;

        if (this.isOpen) {
            this.menuTarget.classList.add('is-open');
            this.element.classList.add('menu-open');
        } else {
            this.menuTarget.classList.remove('is-open');
            this.element.classList.remove('menu-open');
        }
    }

    toggleDropdown(event) {
        const dropdown = event.currentTarget.closest('.nav-dropdown');
        dropdown.classList.toggle('is-open');
    }

    close() {
        this.isOpen = false;
        this.menuTarget.classList.remove('is-open');
        this.element.classList.remove('menu-open');
    }
}
