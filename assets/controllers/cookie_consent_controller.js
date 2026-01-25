import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['banner', 'modal', 'analyticsCheckbox', 'marketingCheckbox'];

    static values = {
        cookieName: { type: String, default: 'cookie_consent' },
        cookieDuration: { type: Number, default: 365 }
    };

    connect() {
        const consent = this.getConsent();
        if (!consent) {
            this.showBanner();
        }
    }

    showBanner() {
        if (this.hasBannerTarget) {
            this.bannerTarget.classList.add('is-visible');
        }
    }

    hideBanner() {
        if (this.hasBannerTarget) {
            this.bannerTarget.classList.remove('is-visible');
        }
    }

    acceptAll() {
        this.saveConsent({
            essential: true,
            analytics: true,
            marketing: true
        });
        this.hideBanner();
        this.hideModal();
    }

    refuseAll() {
        this.saveConsent({
            essential: true,
            analytics: false,
            marketing: false
        });
        this.hideBanner();
        this.hideModal();
    }

    openSettings() {
        this.hideBanner();
        if (this.hasModalTarget) {
            this.modalTarget.classList.add('is-visible');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal() {
        this.hideModal();
        this.showBanner();
    }

    hideModal() {
        if (this.hasModalTarget) {
            this.modalTarget.classList.remove('is-visible');
            document.body.style.overflow = '';
        }
    }

    saveSettings() {
        const analytics = this.hasAnalyticsCheckboxTarget ? this.analyticsCheckboxTarget.checked : false;
        const marketing = this.hasMarketingCheckboxTarget ? this.marketingCheckboxTarget.checked : false;

        this.saveConsent({
            essential: true,
            analytics: analytics,
            marketing: marketing
        });

        this.hideModal();
    }

    saveConsent(consent) {
        const expirationDate = new Date();
        expirationDate.setDate(expirationDate.getDate() + this.cookieDurationValue);

        document.cookie = `${this.cookieNameValue}=${JSON.stringify(consent)}; expires=${expirationDate.toUTCString()}; path=/; SameSite=Lax`;

        this.applyConsent(consent);
    }

    getConsent() {
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === this.cookieNameValue) {
                try {
                    return JSON.parse(value);
                } catch {
                    return null;
                }
            }
        }
        return null;
    }

    applyConsent(consent) {
        if (consent.analytics) {
            this.enableAnalytics();
        }
        if (consent.marketing) {
            this.enableMarketing();
        }
    }

    enableAnalytics() {
        // Placeholder pour Google Analytics ou autre
        // window.dataLayer = window.dataLayer || [];
        // function gtag(){dataLayer.push(arguments);}
        // gtag('js', new Date());
        // gtag('config', 'GA_MEASUREMENT_ID');
        console.log('Analytics enabled');
    }

    enableMarketing() {
        // Placeholder pour cookies marketing (Facebook Pixel, etc.)
        console.log('Marketing cookies enabled');
    }
}
