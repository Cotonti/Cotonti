/**
 * Editor initialization
 *
 * @package CKEditor
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

const ckeditorClasses = {
    /**
     * Full editor
     */
    'editor': 'full',

    /**
     * @deprecated
     */
    'medieditor': 'medium',

    /**
     * Medium editor
     */
    'medium-editor': 'medium',

    /**
     * @deprecated
     */
    'minieditor': 'basic',

    /**
     * Mini editor
     */
    'mini-editor': 'basic',
};

const ckeditorBaseConfig = {
    licenseKey: 'GPL',

    //placeholder: 'Type or paste your content here!',

    blockToolbar: [
        'fontSize',
        'fontColor',
        'fontBackgroundColor',
        '|',
        'bold',
        'italic',
        '|',
        'link',
        'insertImage',
        'insertTable',
        'insertTableLayout',
        '|',
        'bulletedList',
        'numberedList',
        'outdent',
        'indent'
    ],
    fontFamily: {
        supportAllValues: true
    },
    fontSize: {
        options: [10, 12, 14, 'default', 18, 20, 22],
        supportAllValues: true
    },
    fullscreen: {
        onEnterCallback: container =>
            container.classList.add(
                'editor-container',
                'editor-container_classic-editor',
                'editor-container_include-style',
                'editor-container_include-block-toolbar',
                'editor-container_include-fullscreen',
                'main-container'
            )
    },

    // @todo классы пустые сделать. Заголовки локализовать
    heading: {
        options: [
            {
                model: 'paragraph',
                title: 'Paragraph',
                class: 'ck-heading_paragraph'
            },
            {
                model: 'heading1',
                view: 'h1',
                title: 'Heading 1',
                class: 'ck-heading_heading1'
            },
            {
                model: 'heading2',
                view: 'h2',
                title: 'Heading 2',
                class: 'ck-heading_heading2'
            },
            {
                model: 'heading3',
                view: 'h3',
                title: 'Heading 3',
                class: 'ck-heading_heading3'
            },
            {
                model: 'heading4',
                view: 'h4',
                title: 'Heading 4',
                class: 'ck-heading_heading4'
            },
            {
                model: 'heading5',
                view: 'h5',
                title: 'Heading 5',
                class: 'ck-heading_heading5'
            },
            {
                model: 'heading6',
                view: 'h6',
                title: 'Heading 6',
                class: 'ck-heading_heading6'
            }
        ]
    },
    htmlSupport: {
        allow: [
            {
                name: /^.*$/,
                styles: true,
                attributes: true,
                classes: true
            }
        ]
    },
    image: {
        toolbar: [
            'toggleImageCaption',
            'imageTextAlternative',
            '|',
            'imageStyle:inline',
            'imageStyle:wrapText',
            'imageStyle:breakText',
            '|',
            'resizeImage'
        ]
    },
    link: {
        addTargetToExternalLinks: true,
        defaultProtocol: 'https://',
        decorators: {
            toggleDownloadable: {
                mode: 'manual',
                label: 'Downloadable',
                attributes: {
                    download: 'file'
                }
            }
        }
    },
    list: {
        properties: {
            styles: true,
            startIndex: true,
            reversed: true
        }
    },

    style: {
        definitions: [
            // @link https://ckeditor.com/docs/ckeditor5/latest/features/style.html#configuration
            {
                name: 'Marker',
                element: 'mark',
                classes: ['']
            },

            // @todo Не поддерживается в HTML5․ Рекомендуется использование CSS стилей.
            // {
            //     name: 'Big',
            //     element: 'big',
            //     classes: ['']
            // },
            {
                name: 'Small',
                element: 'small',
                classes: ['']
            },

            // Defined in Bootstrap
            {
                name: 'Typewriter',
                element: 'span',
                classes: ['font-monospace']
            },
            {
                name: 'Computer Code',
                element: 'code',
                classes: ['']
            },
            {
                name: 'Keyboard Phrase',
                element: 'kbd',
                classes: ['']
            },
            {
                name: 'Sample Text',
                element: 'samp',
                classes: ['']
            },
            {
                name: 'Variable',
                element: 'var',
                classes: ['']
            },
            {
                name: 'Deleted Text',
                element: 'del',
                classes: ['']
            },
            {
                name: 'Inserted Text',
                element: 'ins',
                classes: ['']
            },
            {
                name: 'Cited Work',
                element: 'cite',
                classes: ['']
            },
            // @todo добавить стиль или убрать
            {
                name: 'Inline Quotation',
                element: 'q',
                classes: ['']
            },

            // Defined in Bootstrap
            {
                name: 'Simple table',
                element: 'table',
                classes: ['table', 'table-simple']
            },
            // Defined in Bootstrap
            {
                name: 'Striped rows',
                element: 'table',
                classes: ['table', 'table-striped']
            },
            // Defined in Bootstrap
            {
                name: 'Striped columns',
                element: 'table',
                classes: ['table', 'table-striped-columns']
            },
            // Defined in Bootstrap
            {
                name: 'Hoverable rows',
                element: 'table',
                classes: ['table', 'table-hover']
            }
        ]
    },
    table: {
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
    },
};

if (jQuery === undefined) {
    if (window.addEventListener) {
        window.addEventListener('load', ckeditorReplace, false);
    } else if (window.attachEvent) {  // Maybe it’s not even necessary — addEventListener seems to be available everywhere already.
        window.attachEvent('onload', ckeditorReplace);
    } else {
        window.onload = ckeditorReplace;
    }
} else {
    $(document).ready(ckeditorReplace);
    ajaxSuccessHandlers.push(ckeditorReplace);
}
