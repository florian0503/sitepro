import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['form', 'wrapper'];

    async submit(event) {
        event.preventDefault();

        const form = this.formTarget;
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = 'Envoi en cours…';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            const data = await response.json();

            if (data.status === 'success') {
                this.wrapperTarget.innerHTML = `
                    <div class="newsletter-success">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <p><strong>${data.title}</strong></p>
                        <p class="newsletter-success-sub">${data.message}</p>
                    </div>`;
            } else {
                const errorDiv = this.wrapperTarget.querySelector('.newsletter-error')
                    || Object.assign(document.createElement('div'), { className: 'newsletter-error' });
                errorDiv.textContent = data.message;
                form.before(errorDiv);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
}
