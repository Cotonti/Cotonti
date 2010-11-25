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

defined('COT_CODE') or die('Wrong URL');

$y = cot_import('y','P','TXT');
$id = cot_import('id','G','INT');
$s = cot_import('s','G','ALP',13);
$w = cot_import('w','G','ALP',4);
$d = cot_import('d','G','INT');
$f = cot_import('f','G','TXT');
$u = cot_import('u','G','TXT');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
cot_block($usr['auth_read']);

/* === Hook === */
foreach (cot_getextplugins('users.details.first') as $pl)
{
	include $pl;
}
/* ===== */

if(!empty($u) && !empty($id))
{
	$sql = $db->query("SELECT user_id FROM $db_users WHERE user_name='".$db->prep($u)."' AND user_id ='$id' LIMIT 1");
	$u = $sql->fetch();
	$id = $u['user_id'];
}
elseif(!empty($u))
{

	$sql = $db->query("SELECT user_id FROM $db_users WHERE user_name='".$db->prep($u)."' LIMIT 1");
	$u = $sql->fetch();
	$id = $u['user_id'];
}
elseif(empty($id) && $usr['id']>0)
{
	$id = $usr['id'];
}


$sql = $db->query("SELECT * FROM $db_users WHERE user_id='$id' LIMIT 1");
cot_die($sql->rowCount()==0);
$urr = $sql->fetch();

$title_params = array(
	'USER' => $L['User'],
	'NAME' => $urr['user_name']
);
$out['subtitle'] = cot_title('title_users_details', $title_params);

/* === Hook === */
foreach (cot_getextplugins('users.details.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('users', 'details', 'core'));
$t = new XTemplate($mskin);

$bhome = $cfg['homebreadcrumb'] ? cot_rc_link($cfg['mainurl'], htmlspecialchars($cfg['maintitle'])).' '.$cfg['separator'].' ' : '';

$t->assign(array(
	"USERS_DETAILS_TITLE" => $bhome . cot_rc_link(cot_url('users'), $L['Users']).' '.$cfg['separator'].' '.cot_build_user($urr['user_id'], htmlspecialchars($urr['user_name'])),
	"USERS_DETAILS_SUBTITLE" => $L['use_subtitle'],
));

$t->assign(cot_generate_usertags($urr, "USERS_DETAILS_", '', true));

/* === Hook === */
foreach (cot_getextplugins('users.details.tags') as $pl)
{
	include $pl;
}
/* ===== */

if ($usr['isadmin'])
{
	$t-> assign(array(
		"USERS_DETAILS_ADMIN_EDIT" => cot_rc_link(cot_url('users', 'm=edit&id='.$urr['user_id']), $L['Edit'])
	));

	$t->parse("MAIN.USERS_DETAILS_ADMIN");
}

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>