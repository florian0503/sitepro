import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('submit', this.handleSubmit.bind(this));
    }

    handleSubmit(event) {
        const submitButton = this.element.querySelector('button[type=submit]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = 'Envoi en cours...';
        }
    }
}
