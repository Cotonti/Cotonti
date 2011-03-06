<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=pfs.first
[END_COT_EXT]
==================== */

/**
 * Overrides markup in PFS insertText
 *
 * @package ckeditor
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$R['pfs_code_header_javascript'] = '
function addfile(gfile, c2, gdesc) {
	window.opener.CKEDITOR.instances.{$c2}.insertHtml(\'{$pfs_code_addfile}\');{$winclose}
}
function addthumb(gfile, c2, gdesc) {
	window.opener.CKEDITOR.instances.{$c2}.insertHtml(\'{$pfs_code_addthumb}\');{$winclose}
}
function addpix(gfile, c2, gdesc) {
	window.opener.CKEDITOR.instances.{$c2}.insertHtml(\'{$pfs_code_addpix}\');{$winclose}
}';

?>
