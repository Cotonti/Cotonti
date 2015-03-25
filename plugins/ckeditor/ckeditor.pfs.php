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

$editor = $cfg['plugin'][$parser]['editor'];
if (!$parser) $parser = ! empty($sys['parser']) ? $sys['parser'] : $cfg['parser'];

if ($parser == 'html' && $editor == 'ckeditor')
{
	$R['pfs_code_header_javascript'] = '
	function addfile(gfile, c2, gdesc) {
		if (opener.CKEDITOR.instances.{$c2} != undefined) {
			opener.CKEDITOR.instances.{$c2}.insertHtml(\'{$pfs_code_addfile}\');
		} else {
			insertText(opener.document, \'{$c2}\', \'{$pfs_code_addfile}\');
		}
		{$winclose}
	}
	function addthumb(gfile, c2, gdesc) {
		if (opener.CKEDITOR.instances.{$c2} != undefined) {
			opener.CKEDITOR.instances.{$c2}.insertHtml(\'{$pfs_code_addthumb}\');
		} else {
			insertText(opener.document, \'{$c2}\', \'{$pfs_code_addthumb}\');
		}
		{$winclose}
	}
	function addpix(gfile, c2, gdesc) {
		if (opener.CKEDITOR.instances.{$c2} != undefined) {
			opener.CKEDITOR.instances.{$c2}.insertHtml(\'{$pfs_code_addpix}\');
		} else {
			insertText(opener.document, \'{$c2}\', \'{$pfs_code_addpix}\');
		}
		{$winclose}
	}';
}
