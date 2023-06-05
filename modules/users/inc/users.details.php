<?php
/**
 * User Details
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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

if(!empty($u) && empty($id))
{
	$u = $db->query("SELECT user_id FROM $db_users WHERE user_name=".$db->quote($u)." LIMIT 1")->fetch();
	$id = $u['user_id'];
}
elseif(empty($id) && empty($u) && $usr['id']>0)
{
	$id = $usr['id'];
}
cot_die(empty($id), true);

$sql = $db->query("SELECT * FROM $db_users WHERE user_id=$id LIMIT 1");
cot_die($sql->rowCount()==0, true);
$urr = $sql->fetch();

$title_params = array(
	'USER' => $L['User'],
	'NAME' => $urr['user_name']
);
$out['subtitle'] = cot_title('title_users_details', $title_params);

$mskin = cot_tplfile(array('users', 'details'), 'module');

/* === Hook === */
foreach (cot_getextplugins('users.details.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate($mskin);

$t->assign(cot_generate_usertags($urr, 'USERS_DETAILS_', '', true));

$t->assign(array(
	'USERS_DETAILS_TITLE' => cot_breadcrumbs(array(array(cot_url('users'), $L['Users']), array(cot_url('users', 'm=details&id='.$urr['user_id'].'&u='.$urr['user_name']), $urr['user_name'])), $cfg['homebreadcrumb']),
	'USERS_DETAILS_SUBTITLE' => $L['use_subtitle'],
));

/* === Hook === */
foreach (cot_getextplugins('users.details.tags') as $pl)
{
	include $pl;
}
/* ===== */

if ($usr['isadmin'])
{
	$t-> assign(array(
		'USERS_DETAILS_ADMIN_EDIT' => cot_rc_link(cot_url('users', 'm=edit&id='.$urr['user_id']), $L['Edit']),
		'USERS_DETAILS_ADMIN_EDIT_URL' => cot_url('users', 'm=edit&id='.$urr['user_id'])
	));

	$t->parse('MAIN.USERS_DETAILS_ADMIN');
}

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';
