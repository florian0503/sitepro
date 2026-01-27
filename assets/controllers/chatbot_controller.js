import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['container', 'messages', 'input', 'badge'];

    isOpen = false;
    isLoading = false;

    connect() {
        this.addWelcomeMessage();
    }

    toggle() {
        this.isOpen = !this.isOpen;
        this.containerTarget.classList.toggle('chatbot--open', this.isOpen);

        if (this.isOpen) {
            this.badgeTarget.style.display = 'none';
            this.inputTarget.focus();
        }
    }

    close() {
        this.isOpen = false;
        this.containerTarget.classList.remove('chatbot--open');
    }

    async send(event) {
        event.preventDefault();

        const message = this.inputTarget.value.trim();
        if (!message || this.isLoading) return;

        this.inputTarget.value = '';
        this.addMessage(message, 'user');
        this.isLoading = true;
        this.showTypingIndicator();

        try {
            const response = await fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message }),
            });

            const data = await response.json();
            this.removeTypingIndicator();

            if (data.response) {
                this.addMessage(data.response, 'bot');
            } else if (data.error) {
                this.addMessage('Désolé, une erreur est survenue.', 'bot');
            }
        } catch (error) {
            this.removeTypingIndicator();
            this.addMessage('Désolé, impossible de contacter le serveur.', 'bot');
        }

        this.isLoading = false;
    }

    addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message chatbot-message--${sender}`;
        messageDiv.innerHTML = `<div class="chatbot-message-content">${this.escapeHtml(text)}</div>`;
        this.messagesTarget.appendChild(messageDiv);
        this.scrollToBottom();
    }

    addWelcomeMessage() {
        setTimeout(() => {
            this.addMessage('Bonjour ! Je suis l\'assistant de EntryWeb. Comment puis-je vous aider ?', 'bot');
        }, 500);
    }

    showTypingIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'chatbot-message chatbot-message--bot chatbot-typing';
        indicator.innerHTML = `
            <div class="chatbot-message-content">
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            </div>
        `;
        this.messagesTarget.appendChild(indicator);
        this.scrollToBottom();
    }

    removeTypingIndicator() {
        const indicator = this.messagesTarget.querySelector('.chatbot-typing');
        if (indicator) {
            indicator.remove();
        }
    }

    scrollToBottom() {
        this.messagesTarget.scrollTop = this.messagesTarget.scrollHeight;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    handleKeydown(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            this.send(event);
        }
    }
}
