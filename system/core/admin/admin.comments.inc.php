<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.comments.inc.php
Version=122
Updated=2007-nov-29
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('comments', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=comments'), $L['Comments']);
$adminhelp = $L['adm_help_comments'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

$adminmain .= "<ul><li><a href=\"".sed_url('admin', 'm=config&n=edit&o=core&p=comments')."\">".$L['Configuration']." : <img src=\"images/admin/config.gif\" alt=\"\" /></a></li></ul>";

if ($a=='delete')
	{
	sed_check_xg();
	$sql = sed_sql_query("DELETE FROM $db_com WHERE com_id='$id'");
	}

$totalitems = sed_sql_rowcount($db_com);
$pagnav = sed_pagination(sed_url('admin','m=comments'), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=comments'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

$sql = sed_sql_query("SELECT * FROM $db_com WHERE 1 ORDER BY com_id DESC LIMIT $d,".$cfg['maxrowsperpage']);

$adminmain .= "<h4>".$L['viewdeleteentries']." :</h4>";
$adminmain .= "<div class=\"pagnav\">".$pagination_prev." ".$pagnav." ".$pagination_next."</div>";
$adminmain .= "<table class=\"cells\"><tr>";
$adminmain .= "<td style=\"width:40px;\" class=\"coltop\">".$L['Delete']."</td>";
$adminmain .= "<td style=\"width:40px;\" class=\"coltop\">#</td>";
$adminmain .= "<td style=\"width:40px;\" class=\"coltop\">".$L['Code']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Author']."</td>";
$adminmain .= "<td style=\"width:128px;\" class=\"coltop\">".$L['Date']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Comment']."</td>";
$adminmain .= "<td style=\"width:64px;\" class=\"coltop\">".$L['Open']."</td></tr>";

$ii = 0;

while ($row = sed_sql_fetcharray($sql))
	{
	$row['com_text'] = sed_cc(sed_cutstring($row['com_text'], 40));
	$row['com_type'] = mb_substr($row['com_code'], 0, 1);
	$row['com_value'] = mb_substr($row['com_code'], 1);

	switch($row['com_type'])
		{
		case 'p':
			$row['com_url'] = sed_url('page', "id=".$row['com_value'], "#c".$row['com_id']);
		break;

		case 'j':
			$row['com_url'] = sed_url('plug', 'e=weblogs&m=page&id='.$row['com_value'], '#c'.$row['com_id']);
		break;

		case 'g':
			$row['com_url'] = sed_url('plug', 'e=gal&pic='.$row['com_value'], '#c'.$row['com_id']);
		break;

		case 'u':
			$row['com_url'] = sed_url('users', 'm=details&id='.$row['com_value'], '#c'.$row['com_id']);
		break;

		case 'v':
			$row['com_url'] = sed_url('polls', 'id='.$row['com_value'], '#c'.$row['com_id']);
		break;

		case 's':
			$row['com_url'] = sed_url('plug', 'e=e_shop&sh=product&productID='.$row['com_value'], '#c'.$row['com_id']);
		break;

		default:
			$row['com_url'] = '';
		break;
		}

	$adminmain .= "<tr><td style=\"text-align:center;\">";
	$adminmain .= "[<a href=\"".sed_url('admin', "m=comments&a=delete&id=".$row['com_id']."&d=".$d."&".sed_xg())."\">x</a>]</td>";
	$adminmain .= "<td style=\"text-align:center;\">".$row['com_id']."</td>";
	$adminmain .= "<td style=\"text-align:center;\">".$row['com_code']."</td>";
	$adminmain .= "<td>".$row['com_author']."</td>";
	$adminmain .= "<td style=\"text-align:center;\">".date($cfg['dateformat'], $row['com_date'])."</td>";
	$adminmain .= "<td>".$row['com_text']."</td>";
	$adminmain .= "<td style=\"text-align:center;\"><a href=\"".$row['com_url']."\"><img src=\"images/admin/jumpto.gif\" alt=\"\" /></a></td></tr>";
	$ii++;
	}
$adminmain .= "<tr><td colspan=\"7\">".$L['Total']." : ".$totalitems.", ".$L['adm_polls_on_page'].": ".$ii."</td></tr></table>";

?>