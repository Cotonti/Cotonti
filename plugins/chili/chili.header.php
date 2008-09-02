<?php
/* ====================
[BEGIN_SED]
File=plugins/chili/chili.header.php
Version=121
Updated=2008-aug-30
Type=Plugin
Author=Trustmaster
Description=
[END_SED]
[BEGIN_SED_EXTPLUGIN]
Code=chili
Part=header
File=chili.header
Hooks=header.main
Tags=header.tpl:{HEADER_COMPOPUP}
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * chili connector for Seditio
 *
 * @package Seditio-N
 * @version 0.0.1
 * @author Trustmaster
 * @copyright (c) 2008 Cotonti Team
 * @license BSD license
 */

$out['compopup'] .= <<<HTM
<script type="text/javascript" src="{$cfg['plugins_dir']}/chili/js/jquery.chili.js"></script>
<script type="text/javascript" src="{$cfg['plugins_dir']}/chili/js/jquery.chili.toolbar.js"></script>
<script type="text/javascript" src="{$cfg['plugins_dir']}/chili/lang/jquery.chili.toolbar.{$lang}.lang.js"></script>
<link rel="stylesheet" type="text/css" href="{$cfg['plugins_dir']}/chili/skins/jquery.chili.toolbar.css" />
<script type="text/javascript" >
ChiliBook.recipeFolder = "{$cfg['plugins_dir']}/chili/js/";
ChiliBook.lineNumbers = true;
ChiliBook.automaticSelector = ".highlight PRE";
ChiliBook.Toolbar.Clipboard.Swf = "{$cfg['plugins_dir']}/chili/skins/jquery.chili.toolbar.swf";
ChiliBook.Toolbar.Utils.PopUpTarget = "jd73kjd9";
</script>

HTM;
?>