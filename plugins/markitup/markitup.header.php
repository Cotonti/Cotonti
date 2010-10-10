<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.tags
Tags=header.tpl:{HEADER_HEAD}
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

if (function_exists('cot_textarea') && cot_auth('plug', 'markitup', 'W'))
{
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

	$mkup_set = function_exists('cot_bbcode_parse') ? 'bbcode' : 'html';

	$markitup = <<<HTM
<script type="text/javascript" src="$smile_lang"></script>
<script type="text/javascript" src="./images/smilies/set.js"></script>
<script type="text/javascript" src="{$cfg['plugins_dir']}/markitup/js/jquery.markitup.js"></script>
<script type="text/javascript" src="$mkup_lang"></script>
<script type="text/javascript" src="{$cfg['plugins_dir']}/markitup/js/jqModal.js"></script>
<script type="text/javascript" src="{$cfg['plugins_dir']}/markitup/js/{$mkup_set}.set.js"></script>
<link rel="stylesheet" type="text/css" href="{$cfg['plugins_dir']}/markitup/skins/{$cfg['plugin']['markitup']['skin']}/style.css" />
<link rel="stylesheet" type="text/css" href="{$cfg['plugins_dir']}/markitup/style.css" />
HTM;
	if ($cfg['plugin']['markitup']['chili'])
	{
		$markitup .= '<script type="text/javascript" src="'.$cfg['plugins_dir'].'/markitup/js/chili.js"></script>';
	}
	$autorefresh = ($cfg['plugin']['markitup']['autorefresh']) ? 'true' : 'false';
	$parserpath = cot_url('plug', 'r=markitup&x=' . $sys['xk'], '', true);
	$markitup .= '
<script type="text/javascript">
//<![CDATA[
mySettings.previewAutorefresh = '.$autorefresh.';
mySettings.previewParserPath = "'.$parserpath.'";
mini.previewAutorefresh = '.$autorefresh.';
mini.previewParserPath = mySettings.previewParserPath;
$(document).ready(function() {
$("textarea.editor").markItUp(mySettings);
$("textarea.minieditor").markItUp(mini);
});
//]]>
</script>';

	$t->assign('HEADER_HEAD', $t->get('HEADER_HEAD') . $markitup);
}

?>
