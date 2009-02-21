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

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('ratings', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=ratings'), $L['Ratings']);
$adminhelp = $L['adm_help_ratings'];

$id = sed_import('id','G','TXT');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

$adminmain .= "<ul><li><a href=\"".sed_url('admin', "m=config&n=edit&o=core&p=ratings")."\">".$L['Configuration']." : <img src=\"images/admin/config.gif\" alt=\"\" /></a></li></ul>";

if ($a=='delete')
	{
	sed_check_xg();
	$sql = sed_sql_query("DELETE FROM $db_ratings WHERE rating_code='$id' ");
	$sql = sed_sql_query("DELETE FROM $db_rated WHERE rated_code='$id' ");
	header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=ratings&d='.$d, '', true));
	exit;
	}

$totalitems = sed_sql_rowcount($db_ratings);
$pagnav = sed_pagination(sed_url('admin','m=ratings'), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=ratings'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

$sql = sed_sql_query("SELECT * FROM $db_ratings WHERE 1 ORDER by rating_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);

$adminmain .= "<div class=\"pagnav\">".$pagination_prev." ".$pagnav." ".$pagination_next."</div>";
$adminmain .= "<table class=\"cells\"><tr>";
$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Delete']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Code']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Date']." (GMT)</td>";
$adminmain .= "<td class=\"coltop\">".$L['Votes']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Rating']."</td>";
$adminmain .= "<td class=\"coltop\" style=\"width:64px;\">".$L['Open']."</td></tr>";

$ii=0;
$jj=0;

while ($row = sed_sql_fetcharray($sql))
	{
	$id2 = $row['rating_code'];
	$sql1 = sed_sql_query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$id2'");
	$votes = sed_sql_result($sql1,0,"COUNT(*)");

	$rat_type = mb_substr($row['rating_code'], 0, 1);
	$rat_value = mb_substr($row['rating_code'], 1);

	switch($rat_type)
		{
		case 'p':
			$rat_url = sed_url('page', "id=".$rat_value);
		break;

		default:
			$rat_url = '';
		break;
		}

	$adminmain .= "<tr><td style=\"text-align:center;\">[<a href=\"".sed_url('admin', "m=ratings&a=delete&id=".$row['rating_code']."&d=".$d."&".sed_xg())."\">x</a>]</td>";
	$adminmain .= "<td style=\"text-align:center;\">".$row['rating_code']."</td>";
	$adminmain .= "<td style=\"text-align:center;\">".date($cfg['dateformat'], $row['rating_creationdate'])."</td>";
	$adminmain .= "<td style=\"text-align:center;\">".$votes."</td>";
	$adminmain .= "<td style=\"text-align:center;\">".$row['rating_average']."</td>";
	$adminmain .= "<td style=\"text-align:center;\"><a href=\"".$rat_url."\"><img src=\"images/admin/jumpto.gif\" alt=\"\" /></a></td></tr>";
	$ii++;
	$jj = $jj + $votes;
	}
$adminmain .= "<tr><td colspan=\"8\">".$L['adm_ratings_totalitems']." : ".$totalitems.", ".$L['adm_polls_on_page'].": ".$ii."<br />";
$adminmain .= $L['adm_ratings_totalvotes']." : ".$jj."</td></tr></table>";

?>