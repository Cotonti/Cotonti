<?php
/**
 * User Details
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$y = sed_import('y','P','TXT');
$id = sed_import('id','G','INT');
$s = sed_import('s','G','ALP',13);
$w = sed_import('w','G','ALP',4);
$d = sed_import('d','G','INT');
$f = sed_import('f','G','TXT');
$u = sed_import('u','G','TXT');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['auth_read']);

/* === Hook === */
$extp = sed_getextplugins('users.details.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if(!empty($u) && !empty($id))
{
	$sql = sed_sql_query("SELECT user_id FROM $db_users WHERE user_name='".sed_sql_prep($u)."' AND user_id ='$id' LIMIT 1");
	$u = sed_sql_fetcharray($sql);
	$id = $u['user_id'];
}
elseif(!empty($u))
{

	$sql = sed_sql_query("SELECT user_id FROM $db_users WHERE user_name='".sed_sql_prep($u)."' LIMIT 1");
	$u = sed_sql_fetcharray($sql);
	$id = $u['user_id'];
}
elseif(empty($id) && $usr['id']>0)
{
	$id = $usr['id'];
}


$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$id' LIMIT 1");
sed_die(sed_sql_numrows($sql)==0);
$urr = sed_sql_fetcharray($sql);

$title_params = array(
	'USER' => $L['User'],
	'NAME' => $urr['user_name']
);
$out['subtitle'] = sed_title('title_users_details', $title_params);

/* === Hook === */
$extp = sed_getextplugins('users.details.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('users', 'details'));
$t = new XTemplate($mskin);

require_once sed_incfile('resources', 'users');

$bhome = $cfg['homebreadcrumb'] ? sed_rc_link($cfg['mainurl'], htmlspecialchars($cfg['maintitle'])).' '.$cfg['separator'].' ' : '';

$t->assign(array(
	"USERS_DETAILS_TITLE" => $bhome . sed_rc_link(sed_url('users'), $L['Users']).' '.$cfg['separator'].' '.sed_build_user($urr['user_id'], htmlspecialchars($urr['user_name'])),
	"USERS_DETAILS_SUBTITLE" => $L['use_subtitle'],
));

$t->assign(sed_generate_usertags($urr, "USERS_DETAILS_", '', true));

/* === Hook === */
$extp = sed_getextplugins('users.details.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if ($usr['isadmin'])
{
	$t-> assign(array(
		"USERS_DETAILS_ADMIN_EDIT" => sed_rc_link(sed_url('users', 'm=edit&id='.$urr['user_id']), $L['Edit'])
	));

	$t->parse("MAIN.USERS_DETAILS_ADMIN");
}

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>