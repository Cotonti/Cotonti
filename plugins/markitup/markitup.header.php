<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
Tags=header.tpl:{HEADER_COMPOPUP}
[END_COT_EXT]
==================== */

/**
 * MarkItUp! connector for Seditio
 *
 * @package markitup
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (!defined('COT_INDEX') && !defined('COT_LIST') && !defined('COT_MESSAGE'))
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

	$out['compopup'] .= <<<HTM
<script type="text/javascript" src="$smile_lang"></script>
<script type="text/javascript" src="./images/smilies/set.js"></script>
<script type="text/javascript" src="{$cfg['plugins_dir']}/markitup/js/jquery.markitup.js"></script>
<script type="text/javascript" src="$mkup_lang"></script>
<script type="text/javascript" src="{$cfg['plugins_dir']}/markitup/js/jqModal.js"></script>
<script type="text/javascript" src="{$cfg['plugins_dir']}/markitup/js/set.js"></script>
<link rel="stylesheet" type="text/css" href="{$cfg['plugins_dir']}/markitup/skins/{$cfg['plugin']['markitup']['skin']}/style.css" />
<link rel="stylesheet" type="text/css" href="{$cfg['plugins_dir']}/markitup/style.css" />
HTM;
	if ($cfg['plugin']['markitup']['chili'])
	{
		$out['compopup'] .= '<script type="text/javascript" src="'.$cfg['plugins_dir'].'/markitup/js/chili.js"></script>';
	}
	$autorefresh = ($cfg['plugin']['markitup']['autorefresh']) ? 'true' : 'false';
	$out['compopup'] .= '
<script type="text/javascript">
//<![CDATA[
mySettings.previewAutorefresh = '.$autorefresh.';
mySettings.previewParserPath = "plug.php?r=markitup&'.cot_xg().'";
mini.previewAutorefresh = '.$autorefresh.';
mini.previewParserPath = mySettings.previewParserPath;
$(document).ready(function() {
$("textarea.editor").markItUp(mySettings);
$("textarea.minieditor").markItUp(mini);
});
//]]>
</script>';
}

?>