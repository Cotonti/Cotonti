<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=pfs.first
[END_COT_EXT]
==================== */

/**
 * Overrides markup in PFS insertText
 *
 * @package CKEditor
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (!$parser) {
    $parser = !empty(Cot::$sys['parser']) ? Cot::$sys['parser'] : Cot::$cfg['parser'];
}
$editor = Cot::$cfg['plugin'][$parser]['editor'];

if ($parser === 'html' && $editor === 'ckeditor') {
	Cot::$R['pfs_code_header_javascript'] = '
    function addHtmlToCKEditor(editor, html) {
        const viewFragment = editor.data.processor.toView(html);
        const modelFragment = editor.data.toModel(viewFragment);
        editor.model.insertContent(modelFragment, editor.model.document.selection);
    }
	function addfile(gfile, c2, gdesc) {
	    if (opener.editors[c2] !== undefined) {
            addHtmlToCKEditor(opener.editors[c2], \'{$pfs_code_addfile}\');
		} else {
			insertText(opener.document, c2, \'{$pfs_code_addfile}\');
		}
	    {$winclose}
	}
	function addthumb(gfile, c2, gdesc) {
	    if (opener.editors[c2] !== undefined) {
	        addHtmlToCKEditor(opener.editors[c2], \'{$pfs_code_addthumb}\');
		} else {
			insertText(opener.document, c2, \'{$pfs_code_addthumb}\');
		}
	    {$winclose}
	}
	function addpix(gfile, c2, gdesc) {
		if (opener.editors[c2] !== undefined) {
			addHtmlToCKEditor(opener.editors[c2], \'{$pfs_code_addpix}\');
		} else {
			insertText(opener.document, c2, \'{$pfs_code_addpix}\');
		}
		{$winclose}
	}';
}
