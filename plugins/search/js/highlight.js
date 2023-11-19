/**
 * Search plugin
 *
 * JavaScript search results highlight
 *
 * @package Search
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

function highlightWords(node, regex, excludeElements) {
    if (node === null) {
        return;
    }
    excludeElements || (excludeElements = ['script', 'style', 'iframe', 'canvas', 'pre']);

    let child = node.firstChild;

    const callback = function(match) {
        let span = document.createElement('mark');
        span.className = 'search-highlight';
        span.textContent = match;
        return span;
    }

    while (child) {
        switch (child.nodeType) {
            case 1:
                if (excludeElements.indexOf(child.tagName.toLowerCase()) > -1) {
                    break;
                }
                highlightWords(child, regex, excludeElements);
                break;
            case 3:
                let bk = 0;
                child.data.replace(regex, function(all) {
                    let args = [].slice.call(arguments);
                    let offset = args[args.length - 2];
                    let newTextNode = child.splitText(offset + bk);
                    let tag;

                    bk -= child.data.length + all.length;

                    newTextNode.data = newTextNode.data.substring(all.length);
                    tag = callback.apply(window, [args[0]]);
                    child.parentNode.insertBefore(tag, newTextNode);
                    child = newTextNode;
                });
                regex.lastIndex = 0;
                break;
        }

        child = child.nextSibling;
    }

    //return node;
}