<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Administration panel - Manager of ratings
 *
 * @package ratings
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'ratings');
cot_block($usr['isadmin']);

require_once cot_incfile('ratings', 'plug');

$t = new XTemplate(cot_tplfile('ratings.admin', 'plug'));

$adminpath[] = array(cot_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(cot_url('admin', 'm=ratings'), $L['Ratings']);
$adminhelp = $L['adm_help_ratings'];

$id = cot_import('id','G','TXT');
list($pg, $d) = cot_import_pagenav('d', $cfg['maxrowsperpage']);

/* === Hook  === */
foreach (cot_getextplugins('admin.ratings.first') as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'delete')
{
	cot_check_xg();
	$db->delete($db_ratings, "rating_code = '$id'");
	$db->delete($db_rated, "rated_code = '$id'");

	cot_message('adm_ratings_already_del');
}


$totalitems = $db->countRows($db_ratings);
$pagenav = cot_pagenav('admin', 'm=ratings', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql = $db->query("SELECT * FROM $db_ratings WHERE 1 ORDER by rating_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
$jj = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('admin.ratings.loop');
/* ===== */
while($row = $sql->fetch())
{
	$id2 = $row['rating_code'];
	$sql1 = $db->query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$id2'");
	$votes = $sql1->fetchColumn();

	$rat_type = mb_substr($row['rating_code'], 0, 1);
	$rat_value = mb_substr($row['rating_code'], 1);

	switch($rat_type)
	{
		case 'p':
			$rat_url = cot_url('page', 'id='.$rat_value);
		break;
		default:
			$rat_url = '';
		break;
	}

	$t->assign(array(
		'ADMIN_RATINGS_ROW_URL_DEL' => cot_url('admin', 'm=ratings&a=delete&id='.$row['rating_code'].'&d='.$d.'&'.cot_xg()),
		'ADMIN_RATINGS_ROW_RATING_CODE' => $row['rating_code'],
		'ADMIN_RATINGS_ROW_CREATIONDATE' => date($cfg['dateformat'], $row['rating_creationdate']),
		'ADMIN_RATINGS_ROW_VOTES' => $votes,
		'ADMIN_RATINGS_ROW_RATING_AVERAGE' => $row['rating_average'],
		'ADMIN_RATINGS_ROW_RAT_URL' => $rat_url,
		'ADMIN_RATINGS_ROW_ODDEVEN' => cot_build_oddeven($ii)
	));
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN.RATINGS_ROW');
	$ii++;
	$jj = $jj + $votes;
}

$t->assign(array(
	'ADMIN_RATINGS_URL_CONFIG' => cot_url('admin', 'm=config&n=edit&o=core&p=ratings'),
	'ADMIN_RATINGS_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_RATINGS_PAGNAV' => $pagenav['main'],
	'ADMIN_RATINGS_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_RATINGS_TOTALITEMS' => $totalitems,
	'ADMIN_RATINGS_ON_PAGE' => $ii,
	'ADMIN_RATINGS_TOTALVOTES' => $jj
));

cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('admin.ratings.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (COT_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>
