/**
 * Base Cotonti class
 */
class CotontiApplication
{
    /**
     * Load data from /index.php?a=get
     * Can be useful, for example when it is needed to load some dynamic content to cached page
     *
     * Example:
     * cot.loadData(['captcha', 'x']).then(result => {
     *    console.log(result);
     * });
     *
     * @param what Array or string. Data to load. For example, ['captcha', 'x'] (x - anti XSS parameter value)
     * @returns {Promise<{}>}
     */
    async loadData(what) {
        if (!what) {
            return {};
        }

        if (this.loadedData === undefined) {
            this.loadedData = {};
        }

        let dataToLoad = [];

        if (typeof what === 'string' || what instanceof String) {
            what = [what];
        }

        for (let item of what) {
            if (!(item in this.loadedData)) {
                dataToLoad.push(item);
            }
        }

        if (dataToLoad.length > 0) {
            // @todo change to system controller when it will be implemented
            let params = new URLSearchParams({a: 'get',});

            dataToLoad.forEach((item, index, array) => {
                params.append('data[' + index + ']', item);
            });
            params.append('_ajax', 1);
            try {
                let response = await fetch('index.php?' + params.toString());

                if (response.ok) {
                    const responseData = await response.json();
                    if (responseData.success) {
                        for (const key in responseData.data) {
                            this.loadedData[key] = responseData.data[key];
                        }
                    }

                } else {
                    // HTTP error
                }
            } catch (error) {
                // Request error
            }
        }

        let result = {};
        for (let item of what) {
            if ((item in this.loadedData)) {
                result[item] = this.loadedData[item];
            }
        }

        return result;
    }

    /**
     * Load captcha via ajax. Used on cached pages.
     */
    loadCaptcha() {
        this.loadData(['captcha', 'x']).then(result => {
            let captchaElements = document.querySelectorAll('.captcha-place-holder');
            for (let element of captchaElements) {
                element.innerHTML = result.captcha;
                this.executeScriptElements(element);
                element.classList.remove('captcha-place-holder', 'loading');
                element.classList.add('captcha');

                const form = element.closest('form');
                if (form !== null) {
                    let inputX = form.querySelector('input[type="hidden"][name="x"]');
                    if (inputX !== null) {
                        inputX.setAttribute('value', result.x);
                    }
                }
            }
        });
    }

    /**
     * If you append <script> tags to the elements of the finished DOM document, they will not be executed automatically.
     * The method executes <script> scripts nested in the specified element
     * @param containerElement Node
     */
    executeScriptElements(containerElement) {
        const scriptElements = containerElement.querySelectorAll('script');

        Array.from(scriptElements).forEach((scriptElement) => {
            const clonedElement = document.createElement('script');
            Array.from(scriptElement.attributes).forEach((attribute) => {
                clonedElement.setAttribute(attribute.name, attribute.value);
            });
            clonedElement.text = scriptElement.text;
            scriptElement.parentNode.replaceChild(clonedElement, scriptElement);
        });
    }
}

let cot = new CotontiApplication();
