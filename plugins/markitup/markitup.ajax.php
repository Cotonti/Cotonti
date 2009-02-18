<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=markitup
Part=preview
File=markitup.ajax
Hooks=ajax
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Simple AJAX previewer for MarkItUp!
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Trustmaster
 * @copyright (c) 2008-2009 Cotonti Team
 * @license BSD
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

// Preview contents
$text = sed_import('text', 'P', 'HTM');
$style = '<link rel="stylesheet" type="text/css" href="skins/'.$skin.'/'.$skin.'.css" />'."\n";
sed_sendheaders();
echo $style.sed_post_parse(sed_parse($text));
ob_end_flush();

?>