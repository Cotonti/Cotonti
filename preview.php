<?php
/**
 * Simple AJAX previewer for MarkItUp!
 *
 * @package Seditio-N
 * @version 0.0.1
 * @author Trustmaster
 * @copyright (c) 2008 Cotonti Team
 * @license BSD license
 */

define('SED_CODE', true);
require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
define('SED_NO_ANTIXSS', true);
require_once $cfg['system_dir'].'/common.php';

header('Content-type: text/html; charset='.$cfg['charset']);

// Preview contents
$text = sed_import('text', 'P', 'HTM');
$style = '<link rel="stylesheet" type="text/css" href="skins/'.$skin.'/'.$skin.'.css" />'."\n";
echo $style.sed_post_parse(sed_parse($text));
ob_end_flush();
?>