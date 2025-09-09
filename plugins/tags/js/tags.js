/**
 * Tags
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
function initTagsSelector () {
    const elements = document.querySelectorAll('.tags-select');
    if ((typeof window.jQuery === 'undefined') || elements.length === 0) {
        return;
    }

    elements.forEach((element) => {
        if (element.dataset.inited !== undefined) {
           return;
        }

        $(element).select2({
            tags: true,
            ajax: {
                url: "?r=tags",
                dataType: 'json',
                delay: 500,
                cache: false
            },
            minimumInputLength: 1
        });

        element.dataset.inited = 'true';
    });
}

document.addEventListener('DOMContentLoaded', () => initTagsSelector());
