<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.rights.modules
[END_COT_EXT]
==================== */

/**
 * Forum sections permissions
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

cot_require('forums');

$sql = $db->query("SELECT a.*, u.user_name FROM $db_auth as a
	LEFT JOIN $db_users AS u ON u.user_id=a.auth_setbyuserid
	WHERE auth_groupid='$g' AND auth_code='forums' AND auth_option != 'a'
	ORDER BY fn_path ASC, fs_order ASC, fs_title ASC");
while ($row = $sql->fetch())
{
	$link = cot_url('admin', 'm=forums&n=edit&id='.$row['auth_option']);
	$title = htmlspecialchars(cot_build_forumpath($row['fs_cat']));
	cot_rights_parseline($row, $title, $link);
}
$sql->closeCursor();
$t->assign('RIGHTS_SECTION_TITLE', $L['Forums']);
$t->parse('MAIN.RIGHTS_SECTION');

?>
