/**
 * Modal
 * Show Modal programmatically
 * @package Cotonti
 * @copyright (c) Cotonti Team
 *
 * @link https://getbootstrap.com/docs/5.3/components/modal/
 */
export class Modal {
    title = '';
    content = '';

    /**
     * @type {({label: String, onClick: function, closeOnClick: bool, type: string}|string)[]|null}
     */
    buttons = null;

    /**
     * Vertically centered
     * @type {boolean}
     */
    centered = false;

    dialogClass = 'modal-dialog';

    /**
     * @type {Element}
     */
    #element = null;

    /**
     * @param {String} title
     * @param {String} content
     * @param {({
     *    label: String,
     *    onClick: function|undefined,
     *    closeOnClick: bool|undefined,
     *    type: string|undefined,
     *    btnType: string|undefined
     *  }|string)[]|null} buttons
     */
    constructor(title, content, buttons) {
        if (window.bootstrap === undefined) {
            console.error('Bootstrap is not found. It is required for use "Modal" component');
            return;
        }

        this.title = title;
        this.content = content;
        this.buttons = buttons;
    }

    show() {
        let dialogClass = this.dialogClass;
        if (this.centered) {
            dialogClass += ' modal-dialog-centered';
        }

        const modalHTML = '<div class="modal fade" id="CotontiDynamicModal" tabindex="-1" '
                + 'aria-labelledby="CotontiDynamicModalLabel" aria-hidden="true">'
          + `<div class="${dialogClass}">`
            + '<div class="modal-content">'
              + '<div class="modal-header">'
                + `<h5 class="modal-title" id="CotontiDynamicModalLabel">${this.title}</h5>`
                + '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>'
              + '</div>'
              + `<div class="modal-body">${this.content}</div>`
              + '<div class="modal-footer"></div>'
            + '</div>'
          + '</div>'
        + '</div>';

        const modalWrapper = document.createElement('div');
        modalWrapper.innerHTML = modalHTML;
        this.#element = modalWrapper.firstElementChild;

        this.#addButtons();

        // Append the modal to the body
        document.body.appendChild(this.#element);

        // Initialize and show the modal
        const modal = new bootstrap.Modal(this.#element);
        modal.show();

        // Remove the modal from the DOM after it is hidden
        this.#element.addEventListener('hidden.bs.modal', () => {
            this.#element.remove();
        });
    }

    #addButtons() {
        const buttons = this.#buildButtons();

        if (buttons === '' || buttons === []) {
            return;
        }

        const container = this.#element.querySelector('.modal-footer');

        if (typeof buttons === 'string') {
            container.innerHTML = buttons;
            return;
        }

        if (Array.isArray(buttons)) {
            buttons.forEach((button) => {
                if (typeof button === 'string') {
                    container.innerHTML += button;
                    return;
                }
                container.appendChild(button);
            })
        }
    }

    #buildButtons() {
        const result = [];
        let buttons = this.buttons;
        if (buttons === []) {
            return result;
        }

        if (buttons === null) {
            return '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
        }

        if (typeof buttons === 'string') {
            return buttons;
        }

        if (Array.isArray(buttons)) {
            buttons.forEach((buttonData) => {
                if (typeof buttonData === 'string') {
                    result.push(buttonData);
                    return;
                }

                const button = document.createElement('button');
                if (buttonData.label !== undefined) {
                    button.innerHTML = buttonData.label;
                }

                button.setAttribute('type', buttonData.btnType !== undefined ? buttonData.btnType : 'button');
                button.classList.add('btn');

                if (buttonData.type !== undefined) {
                    button.classList.add('btn-' + buttonData.type);
                }

                if (buttonData.closeOnClick !== undefined && buttonData.closeOnClick === true) {
                    button.setAttribute('data-bs-dismiss', 'modal');
                }

                if (buttonData.onClick !== undefined && typeof buttonData.onClick === 'function') {
                    button.addEventListener('click', (event) => {return buttonData.onClick(event)});
                }

                result.push(button);
            });
        }
        return result;
    }
}