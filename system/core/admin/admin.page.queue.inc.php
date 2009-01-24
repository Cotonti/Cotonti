<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.queues.page.inc.php
Version=101
Updated=2006-mar-15
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=page'), $L['Page']);
$adminpath[] = array (sed_url('admin', 'm=page&s=queue'), $L['adm_valqueue']);
$adminhelp = $L['adm_queues_page'];

$id = sed_import('id','G','INT');

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

if ($a=='validate')
	{
	sed_check_xg();

	$sql = sed_sql_query("SELECT page_cat FROM $db_pages WHERE page_id='$id'");
	if ($row = sed_sql_fetcharray($sql))
		{
		$usr['isadmin_local'] = sed_auth('page', $row['page_cat'], 'A');
		sed_block($usr['isadmin_local']);
		$sql = sed_sql_query("UPDATE $db_pages SET page_state=0 WHERE page_id='$id'");
		$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='".$row['page_cat']."' ");
		sed_cache_clear('latestpages');
		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=page&s=queue&d='.$d, '', true));
		exit;
		}
	else
		{ sed_die(); }
	}

if ($a=='unvalidate')
	{
	sed_check_xg();

	$sql = sed_sql_query("SELECT page_cat FROM $db_pages WHERE page_id='$id'");
	if ($row = sed_sql_fetcharray($sql))
		{
		$usr['isadmin_local'] = sed_auth('page', $row['page_cat'], 'A');
		sed_block($usr['isadmin_local']);
		$sql = sed_sql_query("UPDATE $db_pages SET page_state=1 WHERE page_id='$id'");
		$sql = sed_sql_query("UPDATE $db_structure SET structure_pagecount=structure_pagecount-1 WHERE structure_code='".$row['page_cat']."' ");
		sed_cache_clear('latestpages');
		header("Location: " . SED_ABSOLUTE_URL . sed_url('list', "c=".$row['page_cat'], '', true));
		exit;
		}
	else
		{ sed_die(); }
	}

$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1"), 0, 0);
$pagnav = sed_pagination(sed_url('admin','m=page&s=queue'), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=page&s=queue'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

$sql = sed_sql_query("SELECT p.*, u.user_name
	FROM $db_pages as p
	LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
	WHERE page_state=1 ORDER by page_id DESC LIMIT $d,".$cfg['maxrowsperpage']);

$adminmain .= "<div class=\"pagnav\">".$pagination_prev." ".$pagnav." ".$pagination_next."</div>";

$ii = 0;

$adminmain .= "<ul>";
while ($row = sed_sql_fetcharray($sql))
	{
	$adminmain .= "<li><a href=\"".sed_url('page', "id=".$row['page_id'])."\">".sed_cc($row['page_title'])."</a><br />";
	$adminmain .= "#".$row['page_id']."<br />";
	$adminmain .= $L['Category']." : ".$sed_cat[$row['page_cat']]['title']." (".$row["page_cat"].")<br />";
	$adminmain .= $L['Description']." : ".sed_cc($row['page_desc'])."<br />";
	//$adminmain .= $L['Author']." : ".sed_cc($row['page_author'])."<br />";
	$adminmain .= $L['Owner']." : ".sed_build_user($row['page_ownerid'], sed_cc($row['user_name']))."<br />";
	$adminmain .= $L['Date']." : ".date($cfg['dateformat'], $row['page_date'] + $usr['timezone'] * 3600)."<br />";
	//$adminmain .= $L['File']." : ".$sed_yesno[$row['page_file']]."<br />";
	//$adminmain .= $L['URL']." : ".$row['page_url']."<br />";
	//$adminmain .= $L['Size']." : ".$row['page_size']."<br />";
	//$adminmain .= $L['Key']." : ".sed_cc($row['page_key'])."<br />";
	//$adminmain .= $L['Alias']." : ".sed_cc($row['page_alias'])."<br />";
	//$adminmain .= $L['Extrafield']." #1 : ".sed_cc($row['page_extra1'])."<br />";
	//$adminmain .= $L['Extrafield']." #2 : ".sed_cc($row['page_extra2'])."<br />";
	//$adminmain .= $L['Extrafield']." #3 : ".sed_cc($row['page_extra3'])."<br />";
	$adminmain .= "<a href=\"".sed_url('admin', "m=page&s=queue&a=validate&id=".$row['page_id']."&d=".$d."&".sed_xg())."\">".$L['Validate']."</a>";
	$adminmain .= " &nbsp; <a href=\"".sed_url('page', "m=edit&id=".$row["page_id"]."&r=adm")."\">".$L['Edit']."</a></li><hr />";
	$ii++;
	}
$adminmain .= "</ul>";
$adminmain .= "<div>".$L['Total']." : ".$totalitems.", ".$L['adm_polls_on_page'].": ".$ii."</div>";
$adminmain .= (sed_sql_numrows($sql)==0) ? "<p>".$L['None']."</p>" : '';

?>
