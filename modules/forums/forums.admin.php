<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin
[END_COT_EXT]
==================== */

/**
 * Statistics for the forums
 *
 * @package forumstats
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('forums', 'any');
cot_block($usr['isadmin']);

$t = new XTemplate(cot_skinfile('forums.admin', 'module'));

require_once cot_incfile('forums', 'module');

$adminpath[] = array(cot_url('admin', 'm=forums'), $L['Forums']);
$adminhelp = $L['adm_help_forums'];

/* === Hook  === */
foreach (cot_getextplugins('forums.admin.first') as $pl)
{
	include $pl;
}
/* ===== */


$sql = $db->query("SELECT * FROM $db_forum_topics WHERE 1 ORDER BY ft_creationdate DESC LIMIT 10");
$ii = 0;

while ($row = $sql->fetch())
{
	$ii++;
	$t->assign(array(
		'ADMIN_FORUMS_ROW_II' => $ii,
		'ADMIN_FORUMS_ROW_FORUMS' => cot_build_forumpath($row['ft_cat']),
		'ADMIN_FORUMS_ROW_URL' => cot_url('forums', 'm=posts&q='.$row['ft_id']),
		'ADMIN_FORUMS_ROW_TITLE' => htmlspecialchars($row['ft_title']),
		'ADMIN_FORUMS_ROW_POSTCOUNT' => $row['ft_postcount']
	));
	$t->parse('MAIN.ADMIN_FORUMS_ROW_USER');
}

$t->assign(array(
	'ADMIN_FORUMS_URL_CONFIG' => cot_url('admin', 'm=config&n=edit&o=module&p=forums'),
	'ADMIN_FORUMS_URL_STRUCTURE' => cot_url('admin', 'm=structure&n=forums'),
	'ADMIN_FORUMS_TOTALTOPICS' => $db->countRows($db_forum_topics),
	'ADMIN_FORUMS_TOTALPOSTS' => $db->countRows($db_forum_posts),
	'ADMIN_FORUMS_TOTALVIEWS' => $db->query("SELECT SUM(fs_viewcount) FROM $db_forum_stats")->fetchColumn()
));

/* === Hook  === */
foreach (cot_getextplugins('forums.admin.tags') as $pl)
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