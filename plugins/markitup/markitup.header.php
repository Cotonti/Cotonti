<?php
/* ====================
[BEGIN_SED]
File=plugins/markitup/markitup.header.php
Version=121
Updated=2008-aug-26
Type=Plugin
Author=Trustmaster
Description=
[END_SED]
[BEGIN_SED_EXTPLUGIN]
Code=markitup
Part=header
File=markitup.header
Hooks=header.main
Tags=header.tpl:{HEADER_COMPOPUP}
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * MarkItUp! connector for Seditio
 *
 * @package Seditio-N
 * @version 0.0.1
 * @author Trustmaster
 * @copyright (c) 2008 Cotonti Team
 * @license BSD license
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$mkup_lang = $cfg['plugins_dir']."/markitup/lang/$lang.lang.js";
if(!file_exists($mkup_lang))
{
	$mkup_lang = $cfg['plugins_dir'].'/markitup/lang/en.lang.js';
}

$out['compopup'] .= <<<HTM
<script type="text/javascript" src="{$cfg['plugins_dir']}/markitup/js/jquery.markitup.js"></script>
<script type="text/javascript" src="$mkup_lang"></script>
<script type="text/javascript" src="{$cfg['plugins_dir']}/markitup/js/set.js"></script>
<link rel="stylesheet" type="text/css" href="{$cfg['plugins_dir']}/markitup/skins/markitup/style.css" />
<link rel="stylesheet" type="text/css" href="{$cfg['plugins_dir']}/markitup/style.css" />
HTM;
if($cfg['plugin']['markitup']['chili'])
{
	$out['compopup'] .= '<script type="text/javascript" src="'.$cfg['plugins_dir'].'/markitup/js/chili.js"></script>';
}
$autorefresh = ($cfg['plugin']['markitup']['autorefresh']) ? 'true' : 'false';
$out['compopup'] .= '
<script type="text/javascript" >
mySettings.previewAutorefresh = '.$autorefresh.';
mySettings.previewParserPath = "plug.php?r=markitup&'.sed_xg().'";
mini.previewAutorefresh = '.$autorefresh.';
mini.previewParserPath = "plug.php?r=markitup&'.sed_xg().'";
$(document).ready(function() {
$("textarea.editor").markItUp(mySettings);
$("textarea.minieditor").markItUp(mini);
});
</script>';

?>