import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['select', 'display', 'options', 'option'];

    connect() {
        this.isOpen = false;
        this.selectedIndex = 0;
        this.buildCustomSelect();
        document.addEventListener('click', this.handleOutsideClick.bind(this));
    }

    disconnect() {
        document.removeEventListener('click', this.handleOutsideClick.bind(this));
    }

    buildCustomSelect() {
        const select = this.selectTarget;
        const options = select.querySelectorAll('option');
        
        const wrapper = document.createElement('div');
        wrapper.className = 'custom-select';
        
        const display = document.createElement('div');
        display.className = 'custom-select-display';
        display.setAttribute('data-custom-select-target', 'display');
        display.innerHTML = `
            <span class="custom-select-text">${options[0].textContent}</span>
            <svg class="custom-select-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6"/>
            </svg>
        `;
        display.addEventListener('click', this.toggle.bind(this));
        
        const optionsList = document.createElement('div');
        optionsList.className = 'custom-select-options';
        optionsList.setAttribute('data-custom-select-target', 'options');
        
        options.forEach((option, index) => {
            const optionEl = document.createElement('div');
            optionEl.className = 'custom-select-option';
            if (index === 0) optionEl.classList.add('is-placeholder');
            if (option.selected) optionEl.classList.add('is-selected');
            optionEl.textContent = option.textContent;
            optionEl.dataset.value = option.value;
            optionEl.dataset.index = index;
            optionEl.addEventListener('click', () => this.selectOption(index, option.value, option.textContent));
            optionsList.appendChild(optionEl);
        });
        
        wrapper.appendChild(display);
        wrapper.appendChild(optionsList);
        
        select.style.display = 'none';
        select.parentNode.insertBefore(wrapper, select.nextSibling);
        
        this.wrapper = wrapper;
        this.displayEl = display;
        this.optionsEl = optionsList;
    }

    toggle(event) {
        event.stopPropagation();
        this.isOpen = !this.isOpen;
        this.wrapper.classList.toggle('is-open', this.isOpen);
    }

    close() {
        this.isOpen = false;
        this.wrapper.classList.remove('is-open');
    }

    selectOption(index, value, text) {
        this.selectedIndex = index;
        this.selectTarget.value = value;
        this.selectTarget.dispatchEvent(new Event('change'));
        
        this.displayEl.querySelector('.custom-select-text').textContent = text;
        this.displayEl.classList.toggle('has-value', index !== 0);
        
        this.optionsEl.querySelectorAll('.custom-select-option').forEach((opt, i) => {
            opt.classList.toggle('is-selected', i === index);
        });
        
        this.close();
    }

    handleOutsideClick(event) {
        if (!this.wrapper.contains(event.target)) {
            this.close();
        }
    }
}
