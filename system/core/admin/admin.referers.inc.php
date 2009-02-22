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

if(!defined('SED_CODE') || !defined('SED_ADMIN')){die('Wrong URL.');}

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['auth_read']);

$t = new XTemplate(sed_skinfile('admin.referers.inc', false, true));

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=referers'), $L['Referers']);
$adminhelp = $L['adm_help_referers'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

if($a=='prune' && $usr['isadmin'])
{
	$sql = sed_sql_query("TRUNCATE $db_referers");
	$adminref = ($sql) ? $L['adm_ref_prune'] : $L['Error'];

}
elseif($a=='prunelowhits' && $usr['isadmin'])
{
	$sql = sed_sql_query("DELETE FROM $db_referers WHERE ref_count<6");
	$adminref = ($sql) ? $L['adm_ref_prunelowhits'] : $L['Error'];
}

if(!empty($adminref))
{
	$t -> assign(array("ADMIN_REFERERS_MESAGE" => $adminref));
	$t -> parse("REFERERS.MESAGE");
}

$totalitems = sed_sql_rowcount($db_referers);
$pagnav = sed_pagination(sed_url('admin','m=referers'), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=referers'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

$sql = sed_sql_query("SELECT * FROM $db_referers ORDER BY ref_count DESC LIMIT $d, ".$cfg['maxrowsperpage']);

if($usr['isadmin'])
{
	$t -> assign(array(
		"ADMIN_REFERERS_URL_PRUNE" => sed_url('admin', "m=referers&a=prune&".sed_xg()),
		"ADMIN_REFERERS_URL_PRUNELOWHITS" => sed_url('admin', "m=referers&a=prunelowhits&".sed_xg())
	));
	$t -> parse("REFERERS.REFERERS_IS_ADMIN");
}

if(sed_sql_numrows($sql)>0)
{
	while($row = mysql_fetch_array($sql))
	{
		preg_match_all("|//([^/]+)/|", $row['ref_url'], $a);
		$referers[$a[1][0]][$row['ref_url']] = $row['ref_count'];
	}

	$ii = 0;

	foreach($referers as $referer => $url)
	{

		$t -> assign(array("ADMIN_REFERERS_REFERER" => htmlspecialchars($referer)));
		$t -> parse("REFERERS.REFERERS_NOT_EMPTY.REFERERS_ROW");

		foreach($url as $uri=>$count)
		{
			$t -> assign(array(
				"ADMIN_REFERERS_URI" => htmlspecialchars(sed_cutstring($uri, 48)),
				"ADMIN_REFERERS_COUNT" => $count
			));
			$t -> parse("REFERERS.REFERERS_NOT_EMPTY.REFERERS_ROW.REFERERS_URI");
		}
		$ii++;
	}

	$t -> assign(array(
		"ADMIN_REFERERS_PAGINATION_PREV" => $pagination_prev,
		"ADMIN_REFERERS_PAGNAV" => $pagnav,
		"ADMIN_REFERERS_PAGINATION_NEXT" => $pagination_next,
		"ADMIN_REFERERS_TOTALITEMS" => $totalitems,
		"ADMIN_REFERERS_ON_PAGE" => $ii
	));
	$t -> parse("REFERERS.REFERERS_NOT_EMPTY");
}
else
{
	$t -> parse("REFERERS.REFERERS_EMPTY");
}

$t -> parse("REFERERS");
$adminmain = $t -> text("REFERERS");

?>