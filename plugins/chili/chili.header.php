<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=chili
Part=header
File=chili.header
Hooks=header.main
Tags=header.tpl:{HEADER_COMPOPUP}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * chili connector for Cotonti
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Trustmaster
 * @copyright (c) 2008-2009 Cotonti Team
 * @license BSD license
 */

defined('SED_CODE') or die('Wrong URL');

if(!defined('SED_MESSAGE') && !defined('SED_PFS') && !defined('SED_POLLS') && !defined('SED_USERS'))
{
	$out['compopup'] .= <<<HTM
<script type="text/javascript" src="{$cfg['plugins_dir']}/chili/js/jquery.chili.js"></script>
<script type="text/javascript" src="{$cfg['plugins_dir']}/chili/js/jquery.chili.toolbar.js"></script>
<script type="text/javascript" src="{$cfg['plugins_dir']}/chili/lang/jquery.chili.toolbar.{$lang}.lang.js"></script>
<link rel="stylesheet" type="text/css" href="{$cfg['plugins_dir']}/chili/skins/jquery.chili.toolbar.css" />
<script type="text/javascript" >
//<![CDATA[
ChiliBook.recipeFolder = "{$cfg['plugins_dir']}/chili/js/";
ChiliBook.lineNumbers = true;
ChiliBook.automaticSelector = ".highlight PRE";
ChiliBook.Toolbar.Clipboard.Swf = "{$cfg['plugins_dir']}/chili/skins/jquery.chili.toolbar.swf";
ChiliBook.Toolbar.Utils.PopUpTarget = "jd73kjd9";
delete ChiliBook.Toolbar.Command.CopyToClipboard;
delete ChiliBook.Toolbar.Command.PrintSource;
delete ChiliBook.Toolbar.Command.About;
//]]>
</script>
HTM;
}

?>