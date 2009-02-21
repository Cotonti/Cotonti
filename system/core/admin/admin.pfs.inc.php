<?PHP
/**
 * Administration panel
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pfs', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=pfs'), $L['PFS']);
$adminhelp = $L['adm_help_pfs'];

$adminmain .= "<ul><li><a href=\"".sed_url('admin', "m=config&n=edit&o=core&p=pfs")."\">".$L['Configuration']." : <img src=\"images/admin/config.gif\" alt=\"\" /></a></li>";
$adminmain .= "<li><a href=\"".sed_url('admin', "m=pfs&s=allpfs")."\">".$L['adm_allpfs']."</a></li>";
$adminmain .= "<li><a href=\"".sed_url('pfs', "userid=0")."\">".$L['SFS']."</a></li></ul>";


$adminmain .= "<h4>".$L['adm_gd']." :</h4>";

if (!function_exists('gd_info'))
	{
	$adminmain .= "<p>".$L['adm_nogd']."</p>";
	}
   else
	{
	$gd_datas = gd_info();
	$adminmain .= "<p>";
	foreach ($gd_datas as $k => $i)
		{
		$adminmain .= $k." : ";
		if (mb_strlen($i)<2) { $i = $sed_yesno[$i]; }
		$adminmain .= $i."<br />";
		}
	$adminmain .= "</p>";
	}

?>