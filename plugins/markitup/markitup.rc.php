<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
 * MarkItUp! connector for Cotonti
 *
 * @package markitup
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Language selection
global $lang;
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
cot_rc_add_file($smile_lang);
cot_rc_add_file('images/smilies/set.js');
cot_rc_add_file($cfg['plugins_dir'] . '/markitup/js/jquery.markitup.js');
cot_rc_add_file($mkup_lang);
cot_rc_add_file($cfg['plugins_dir'] . '/markitup/js/jqModal.js');
cot_rc_add_file($cfg['plugins_dir'] . "/markitup/js/{$mkup_set}.set.js");
cot_rc_add_file($cfg['plugins_dir'] . '/markitup/skins/' . $cfg['plugin']['markitup']['skin'] . '/style.css');
cot_rc_add_file($cfg['plugins_dir'] . '/markitup/style.css');
if ($cfg['plugin']['markitup']['chili'])
{
	cot_rc_add_file($cfg['plugins_dir'].'/markitup/js/chili.js');
}

// User-specific setup
$autorefresh = ($cfg['plugin']['markitup']['autorefresh']) ? 'true' : 'false';
cot_rc_add_embed('markitup.set', '$(document).ready(function() {
	mySettings.previewAutorefresh = '.$autorefresh.';
	mySettings.previewParserPath = "plug.php?r=markitup&x=" + $("input[name=\'x\'][type=\'hidden\']").eq(0).val();
	mini.previewAutorefresh = '.$autorefresh.';
	mini.previewParserPath = mySettings.previewParserPath;
	$("textarea.editor").markItUp(mySettings);
	$("textarea.minieditor").markItUp(mini);
});');

?>
