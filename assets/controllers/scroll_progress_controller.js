import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.updateProgress();
        window.addEventListener('scroll', this.updateProgress.bind(this));
    }

    disconnect() {
        window.removeEventListener('scroll', this.updateProgress.bind(this));
    }

    updateProgress() {
        const progressBar = document.querySelector('.scroll-progress-bar');
        if (!progressBar) return;

        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        
        const scrollPercentage = (scrollTop / (documentHeight - windowHeight)) * 100;
        
        progressBar.style.width = `${Math.min(scrollPercentage, 100)}%`;
    }
}
