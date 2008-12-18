<?php
/* ====================
[BEGIN_SED]
File=plugins/markitup/markitup.ajax.php
Version=121
Updated=2008-aug-26
Type=Plugin
Author=Trustmaster
Description=
[END_SED]
[BEGIN_SED_EXTPLUGIN]
Code=markitup
Part=preview
File=markitup.ajax
Hooks=ajax
Tags=
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Simple AJAX previewer for MarkItUp!
 *
 * @package Seditio-N
 * @version 0.0.1
 * @author Trustmaster
 * @copyright (c) 2008 Cotonti Team
 * @license BSD license
 */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

header('Content-type: text/html; charset='.$cfg['charset']);

// Preview contents
$text = sed_import('text', 'P', 'HTM');
$style = '<link rel="stylesheet" type="text/css" href="skins/'.$skin.'/'.$skin.'.css" />'."\n";
echo $style.sed_post_parse(sed_parse($text));
ob_end_flush();
?>