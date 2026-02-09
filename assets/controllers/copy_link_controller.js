import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'button', 'text'];

    copy() {
        navigator.clipboard.writeText(this.inputTarget.value).then(() => {
            this.textTarget.textContent = 'Copié !';
            setTimeout(() => {
                this.textTarget.textContent = 'Copier';
            }, 2000);
        });
    }
}
