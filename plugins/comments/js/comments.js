/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(function () {
    class Comment {
        /**
         * @type {HTMLElement}
         */
        #container = null;

        /**
         * @type {HTMLElement}
         */
        #commentForm

        /**
         * @type {HTMLElement}
         */
        #errorElement = null;

        /**
         * @param {HTMLElement} commentForm
         */
        add(commentForm) {
            this.#commentForm = commentForm;
            this.#container = commentForm.closest('.comments-container');

            const url = commentForm.getAttribute('action');
            const formData = new FormData(commentForm);

            // Commented item url
            const currentUrlParams = this.#container.dataset.urlParams;
            if (currentUrlParams !== undefined) {
                formData.set('ci', currentUrlParams);
            }

            if (!this.#validateForm(formData)) {
                return;
            }

            // Send data to the server
            fetch(url, {method: 'POST', body: formData})
                .then(async (response) => {
                    let result = null;
                    try {
                        result = await response.json();
                    } catch (error) {
                        this.#showError(cot.L.comments_saveError);
                        return;
                    }

                    if (result.errors !== undefined) {
                        for (let field in result.errors) {
                            for (let key in result.errors[field]) {
                                this.#showError(result.errors[field][key], field);
                            }
                        }
                        return;
                    }

                    if (result.message !== undefined) {
                        this.#showMessage(result.message);
                    }

                    if (result.success) {
                        let refreshUrl = this.#getRefreshUrl();
                        if (refreshUrl !== null && refreshUrl !== '') {
                            ajaxSend({
                                method: 'GET',
                                url: refreshUrl,
                                divId: 'comments'
                            });
                        }
                    }

                })
                .catch((error) => {
                    console.error(error);
                    this.#showError(cot.L.comments_saveError);
                })
        }

        #getRefreshUrl() {
            const commentBlock = this.#container.querySelector('.comments-block');
            if (commentBlock.dataset.refreshUrl !== undefined) {
                return commentBlock.dataset.refreshUrl;
            }

            if (commentBlock.dataset.refresh !== undefined) {
                return atob(commentBlock.dataset.refresh);
            }
        }

        /**
         * @param {FormData} formData
         * @returns {boolean}
         */
        #validateForm(formData) {
            this.#hideWarnings();
            let hasErrors = false;
            if (formData.get('comment_text') === '') {
                this.#showError(cot.L.comments_tooShort, 'comment_text');
                hasErrors = true;
            }

            if (formData.get('comment_author') !== null && formData.get('comment_author') === '') {
                this.#showError(cot.L.comments_authorTooShort, 'comment_author');
                hasErrors = true;
            }

            if (formData.get('rverify') !== null && formData.get('rverify') === '') {
                this.#showError(cot.L.captcha_verification_failed, 'rverify');
                hasErrors = true;
            }

            return !hasErrors;
        }

        /**
         * Show comment form error
         * @param {string} errorMessage
         * @param {string|null}  field
         */
        #showError(errorMessage, field = null) {
            if (this.#errorElement === null) {
                this.#errorElement = this.#container.querySelector('.comments-error');
            }

            if (this.#errorElement === null) {
                return;
            }

            this.#addMessage(errorMessage, this.#errorElement);

            if (field !== null && field !== '' && field !== 'default') {
                this.#highLightFormField(field);
            }
        }

        /**
         * @param {string} message
         */
        #showMessage(message) {
            const element = this.#container.querySelector('.comments-success');
            if (element === null) {
                return;
            }
            this.#addMessage(message, element);
        }

        /**
         * @param {string} field
         * @param {string} type
         */
        #highLightFormField(field, type = 'error') {
            const element = this.#commentForm.querySelector('[name="' + field + '"]');
            const elementClass = type === 'error' ? 'is-invalid' : 'is-valid';
            element.classList.add(elementClass);
        }

        #hideWarnings() {
            if (this.#errorElement === null) {
                this.#errorElement = this.#container.querySelector('.comments-error');
            }

            if (this.#errorElement !== null) {
                this.#clearMessages(this.#errorElement);
            }

            let element = this.#container.querySelector('.comments-success');
            if (element !== null) {
                this.#clearMessages(element);
            }

            element = this.#container.querySelector('.comments-warnings');
            if (element !== null) {
                element.innerHTML = '';
            }

            let elements = this.#commentForm.querySelectorAll('.is-invalid');
            elements.forEach((element) => {
                element.classList.remove('is-invalid');
            });
        }

        /**
         * @param {string} message
         * @param {HTMLElement} element
         */
        #addMessage(message, element) {
            let messageElement = element.querySelector('.comments-message');
            if (messageElement === null) {
                messageElement = document.createElement("div");
                messageElement.classList.add('comments-error-message');
                element.append(messageElement);
            }

            let messageListElement = messageElement.querySelector('ul');
            if (messageListElement === null) {
                messageListElement = document.createElement("ul");
                messageElement.append(messageListElement);
            }

            element.style.display = null;

            const messageLiElement = document.createElement('li');
            messageLiElement.innerHTML = message;

            messageListElement.append(messageLiElement);
        }

        /**
         * @param {HTMLElement} element
         */
        #clearMessages(element) {
            const messageElement = element.querySelector('.comments-message');
            if (messageElement !== null) {
                messageElement.innerHTML = '';
            }

            element.style.display = 'none';
        }
    }

    document.addEventListener('submit', (event) => {
        if (event.target.getAttribute('name') === 'comment-form') {
            event.preventDefault();

            const commentForm = event.target;
            const comment = new Comment();
            comment.add(commentForm);
        }
    });
})();