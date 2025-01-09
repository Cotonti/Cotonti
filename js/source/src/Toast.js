/**
 * Toast
 * Show Toasts programmatically
 * @package Cotonti
 * @copyright (c) Cotonti Team
 *
 * @link https://getbootstrap.com/docs/5.3/components/toasts/
 */
export class Toast {
    static containerClass = 'toast-container position-fixed bottom-0 end-0 p-3';

    title = '';
    message = '';
    hint = '';
    type = '';
    delay = 5000;

    #container = null;

    /**
     * @param {String} title
     * @param {String} message
     * @param {String} hint
     * @param {String} type
     */
    constructor(title, message, hint = '', type = '') {
        if (window.bootstrap === undefined) {
            console.error('Bootstrap is not found. It is required for use "Toast" component');
            return;
        }

        this.title = title;
        this.message = message;
        this.hint = hint;
        this.type = type;
    }

    show() {
        if (this.#container === null) {
            let container = document.getElementById('toast-container');
            if (container === null) {
                container = this.#createContainer();
            }
            this.#container = container;
        }

        const toastTitle = this.title !== '' ? `<strong class="me-auto">${this.title}</strong>` : '';
        const toastHint = this.hint !== '' ? `<small>${this.hint}</small>` : '';
        const backGround = this.type !== '' ? ` text-bg-${this.type}` : '';

        const toastHTML = `<div class="toast${backGround}" role="alert" aria-live="assertive" aria-atomic="true" `
                + `data-bs-delay="${this.delay}">`
            + '<div class="toast-header">'
              + `${toastTitle}${toastHint}`
              + '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>'
            + '</div>'
            + `<div class="toast-body">${this.message}</div>`
          + '</div>';

        const toastWrapper = document.createElement('div');
        toastWrapper.innerHTML = toastHTML;

        const toastElement = toastWrapper.firstElementChild;

        // Append the toast to the container
        this.#container.appendChild(toastElement);

        // Initialize the toast
        const toast = new window.bootstrap.Toast(toastElement);
        toast.show();

        // Optionally remove the toast from the DOM after it hides
        this.#container.lastElementChild.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
            if (this.#container.innerHTML === '') {
                this.#container.remove();
                this.#container = null;
            }
        });
    }

    #createContainer() {
        const result = document.createElement('div');
        result.id = 'toast-container';
        result.className = Toast.containerClass;
        document.body.append(result);
        return result;
    }
}