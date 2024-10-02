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

[$usr['auth_read'], $usr['auth_write'], $usr['isadmin']] = cot_auth('users', 'a');
cot_block($usr['auth_read']);

/* === Hook === */
foreach (cot_getextplugins('users.details.first') as $pl) {
	include $pl;
}
/* ===== */

if (!empty($u) && empty($id)) {
	$u = Cot::$db->query(
        'SELECT user_id FROM ' . Cot::$db->users . ' WHERE user_name = :name  LIMIT 1',
        ['name' => $u]
    )->fetch();
	$id = !empty($u) ? $u['user_id'] : null;
} elseif(empty($id) && empty($u) && Cot::$usr['id'] > 0) {
	$id = Cot::$usr['id'];
}
cot_die(empty($id), true);

$sql = Cot::$db->query('SELECT * FROM ' . Cot::$db->users . ' WHERE user_id = ? LIMIT 1', $id);
cot_die($sql->rowCount() == 0, true);
$urr = $sql->fetch();

$title_params = array(
	'USER' => Cot::$L['User'],
	'NAME' => $urr['user_name']
);
Cot::$out['subtitle'] = cot_title('title_users_details', $title_params);

$mskin = cot_tplfile(['users', 'details'], 'module');

/* === Hook === */
foreach (cot_getextplugins('users.details.main') as $pl) {
	include $pl;
}
/* ===== */

Cot::$out['canonical_uri'] = cot_url(
    'users',
    ['m' => 'details', 'id' => $urr['user_id'], 'u' => $urr['user_name']]
);

require_once Cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate($mskin);

$t->assign(cot_generate_usertags($urr, 'USERS_DETAILS_', '', true));

if ((int) $urr['user_id'] === (int) Cot::$usr['id']) {
    $breadCrumbs = [[cot_url('users', ['m' => 'details']), Cot::$L['users_myProfile']]];
} else {
    $breadCrumbs = [
        [cot_url('users'), Cot::$L['Users']],
        [
            cot_url('users', ['m' => 'details', 'id' => $urr['user_id'], 'u' => $urr['user_name']]),
            cot_user_full_name($urr),
        ],
    ];
}

$t->assign([
    'USERS_DETAILS_TITLE' => htmlspecialchars(cot_user_full_name($urr)),
    'USERS_DETAILS_SUBTITLE' => Cot::$L['use_subtitle'],
    'USERS_DETAILS_BREADCRUMBS' => cot_breadcrumbs($breadCrumbs, Cot::$cfg['homebreadcrumb']),
]);

/* === Hook === */
foreach (cot_getextplugins('users.details.tags') as $pl) {
	include $pl;
}
/* ===== */

if ($usr['isadmin']) {
	$t-> assign(array(
		'USERS_DETAILS_ADMIN_EDIT' => cot_rc_link(cot_url('users', 'm=edit&id='.$urr['user_id']), $L['Edit']),
		'USERS_DETAILS_ADMIN_EDIT_URL' => cot_url('users', 'm=edit&id='.$urr['user_id'])
	));

	$t->parse('MAIN.USERS_DETAILS_ADMIN');
}

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';
