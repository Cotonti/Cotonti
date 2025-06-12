/**
 * Functions for CKEditor
 *
 * @package CKEditor
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

const {ButtonView, Plugin} = CKEDITOR;

class ReadMorePlugin extends Plugin {
    init() {
        const editor = this.editor;

        editor.ui.componentFactory.add('readMore', locale => {
            const view = new ButtonView(locale);
            const t = editor.t;
            view.set({
                label: t('Read more'),
                icon: `
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M2 3h12v1H2V3zM2 6h12v1H2V6zM2 9h12v1H2V9z" fill="currentColor"/>
                        <circle cx="5" cy="13" r="0.75" fill="currentColor"/>
                        <circle cx="8" cy="13" r="0.75" fill="currentColor"/>
                        <circle cx="11" cy="13" r="0.75" fill="currentColor"/>
                    </svg>
                `,
                tooltip: true
            });

            view.on('execute', () => {
                addHtmlToCKEditor(editor, '<hr class="more">');
            });

            return view;
        });
    }
}

function ckeditorReplace() {
    let textareas = document.getElementsByTagName('textarea');
    if (textareas === undefined || textareas.length === 0) {
        return
    }

    const {ClassicEditor, ButtonView} = CKEDITOR;

    // ckeditorConfig can be defined in the theme
    if (typeof ckeditorConfig === 'undefined' || ckeditorConfig === null) {
        ckeditorConfig = {};
    }

    for (let textarea of textareas) {
        let classList = textarea.classList;
        if (classList.contains('editor-initialized')) {
            continue;
        }
        const editorHeight =  (textarea.offsetHeight - 50) + 'px';
        for (let key of classList) {
            if (ckeditorClasses[key] !== undefined) {
                ClassicEditor
                    .create(textarea, {...ckeditorBaseConfig, ...ckeditorConfig, ...ckeditorPreset[ckeditorClasses[key]]})
                    .then((editor) => {
                        textarea.classList.add('editor-initialized');

                        editor.ui.view.editable.element.style.minHeight = editorHeight;
                        const editorContainer = editor.ui.view.element;
                        editorContainer.style.maxWidth = '99%';

                        if (window.editors === undefined) {
                            window.editors = {};
                        }
                        window.editors[textarea.name] = editor;
                    })
                    .catch( error => {
                        console.error(error);
                    });
                break;
            }
        }
    }
}

function addHtmlToCKEditor(editor, html) {
    const viewFragment = editor.data.processor.toView(html);
    const modelFragment = editor.data.toModel(viewFragment);
    editor.model.insertContent(modelFragment, editor.model.document.selection);
}