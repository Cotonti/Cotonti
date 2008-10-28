<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.page.catorder.inc.php
Version=110
Updated=2006-jun-06
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=page'), $L['Pages']);
$adminpath[] = array (sed_url('admin', 'm=page&s=catorder'), $L['adm_sortingorder']);
$adminhelp = $L['adm_help_catorder'];

$options_sort = array(
	'id' => $L['Id'],
	'type'	=> $L['Type'],
	'key' => $L['Key'],
	'title' => $L['Title'],
	'desc'	=> $L['Description'],
	'text'	=> $L['Body'],
	'author' => $L['Author'],
	'owner' => $L['Owner'],
	'date'	=> $L['Date'],
	'begin'	=> $L['Begin'],
	'expire'	=> $L['Expire'],
	'count' => $L['Count'],
	'file' => $L['adm_fileyesno'],
	'url' => $L['adm_fileurl'],
	'size' => $L['adm_filesize'],
	'filecount' => $L['adm_filecount']
	);

$options_way = array (
	'asc' => $L['Ascending'],
	'desc' => $L['Descending']
	);

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

if ($a=='update')
	{
	$s = sed_import('s', 'P', 'ARR');

	foreach($s as $i => $k)
		{
		$order = $s[$i]['order'].'.'.$s[$i]['way'];
		$sql = sed_sql_query("UPDATE $db_structure SET structure_order='$order' WHERE structure_id='$i'");
		}
	sed_cache_clear('sed_cat');
   	header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=page&s=catorder&d='.$d, '', true));
	}

$totalitems = sed_sql_rowcount($db_structure);
$pagnav = sed_pagination(sed_url('admin','m=page&s=catorder'), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=page&s=catorder'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

$sql = sed_sql_query("SELECT * FROM $db_structure ORDER by structure_path, structure_code LIMIT $d,".$cfg['maxrowsperpage']);

$adminmain .= "<form id=\"chgorder\" action=\"".sed_url('admin', "m=page&s=catorder&a=update&d=".$d)."\" method=\"post\">";
$adminmain .= "<div class=\"pagnav\">".$pagination_prev." ".$pagnav." ".$pagination_next."</div>";
$adminmain .= "<table class=\"cells\"><tr><td class=\"coltop\">".$L['Code']."</td><td class=\"coltop\">".$L['Path']."</td>";
$adminmain .= "<td class=\"coltop\">".$L['Title']."</td><td class=\"coltop\">".$L['Order']."</td></tr>";

$ii = 0;

while ($row = sed_sql_fetcharray($sql))
	{
	$structure_id = $row['structure_id'];
	$structure_code = $row['structure_code'];
	$structure_path = $row['structure_path'];
	$structure_title = $row['structure_title'];
	$structure_desc = $row['structure_desc'];
	$structure_order = $row['structure_order'];

	$adminmain .= "<tr><td>".$structure_code."</td><td>".$structure_path."</td><td>".sed_cc($structure_title)."</td>";

	$raw = explode('.',$structure_order);
	$sort = $raw[0];
	$way = $raw[1];

	reset($options_sort);
	reset($options_way);

	$form_sort = "<select name=\"s[".$structure_id."][order]\" size=\"1\">";
	while( list($i,$x) = each($options_sort) )
		{
		$selected = ($i==$sort) ? 'selected="selected"' : '';
		$form_sort .= "<option value=\"$i\" $selected>".$x."</option>";
		}
	$form_sort .= "</select> ";

	$form_way = "<select name=\"s[".$structure_id."][way]\" size=\"1\">";
	while( list($i,$x) = each($options_way) )
		{
		$selected = ($i==$way) ? 'selected="selected"' : '';
		$form_way .= "<option value=\"$i\" $selected>".$x."</option>";
		}
	$form_way .= "</select> ";

	$adminmain  .= "<td>".$form_sort.' '.$form_way."</td></tr>";

	$ii++;
	}

$adminmain  .= "<tr><td colspan=\"4\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr>";
$adminmain  .= "<tr><td colspan=\"4\">&nbsp;</td></tr>";
$adminmain .= "<tr><td colspan=\"4\">".$L['Total']." : ".$totalitems.", ".$L['adm_polls_on_page'].": ".$ii."</td></tr></table>";
$adminmain  .= "</form>";

?>
