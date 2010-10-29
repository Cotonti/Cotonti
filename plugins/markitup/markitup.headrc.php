<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=headrc
[END_COT_EXT]
==================== */

/**
 * MarkItUp! connector for Cotonti
 *
 * @package markitup
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Language selection
// FIXME language selection is currently site-wide, not per user
$mkup_lang = $cfg['plugins_dir']."/markitup/lang/$lang.lang.js";
if (!file_exists($mkup_lang))
{
	$mkup_lang = $cfg['plugins_dir'].'/markitup/lang/en.lang.js';
}
$smile_lang = "./images/smilies/lang/$lang.lang.js";
if (!file_exists($smile_lang))
{
	$smile_lang = './images/smilies/lang/en.lang.js';
}

// BBcode or HTML preset
$mkup_set = function_exists('cot_bbcode_parse') ? 'bbcode' : 'html';

// Load head resources
cot_headrc_load_file($smile_lang);
cot_headrc_load_file('images/smilies/set.js');
cot_headrc_load_file($cfg['plugins_dir'] . '/markitup/js/jquery.markitup.js');
cot_headrc_load_file($mkup_lang);
cot_headrc_load_file($cfg['plugins_dir'] . '/markitup/js/jqModal.js');
cot_headrc_load_file($cfg['plugins_dir'] . "/markitup/js/{$mkup_set}.set.js");
cot_headrc_load_file($cfg['plugins_dir'] . '/markitup/skins/' . $cfg['plugin']['markitup']['skin'] . '/style.css', 'global', 'css');
cot_headrc_load_file($cfg['plugins_dir'] . '/markitup/style.css', 'global', 'css');
if ($cfg['plugin']['markitup']['chili'])
{
	cot_headrc_load_file($cfg['plugins_dir'].'/markitup/js/chili.js');
}

// User-specific setup
$autorefresh = ($cfg['plugin']['markitup']['autorefresh']) ? 'true' : 'false';
cot_headrc_load_embed('markitup.set', '$(document).ready(function() {
	mySettings.previewAutorefresh = '.$autorefresh.';
	mySettings.previewParserPath = "plug.php?r=markitup&x=" + $("input[name=\'x\'][type=\'hidden\']").eq(0).val();
	mini.previewAutorefresh = '.$autorefresh.';
	mini.previewParserPath = mySettings.previewParserPath;
	$("textarea.editor").markItUp(mySettings);
	$("textarea.minieditor").markItUp(mini);
});');

?>
