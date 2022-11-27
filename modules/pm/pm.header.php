<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
Tags=header.tpl:{HEADER_USER_PMS},{HEADER_USER_PMREMINDER}
[END_COT_EXT]
==================== */

/**
 * PM header notices
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @todo Cache data
 * @todo в конфиг добавить опцию вывода последних сообщений в хеадер. Результаты выборки кешировать!
 */

defined('COT_CODE') or die('Wrong URL.');

if (cot::$usr['id'] > 0) {
	cot::$out['pms'] = cot_rc_link(cot_url('pm'), cot::$L['Private_Messages']);

	require_once cot_incfile('pm', 'module');
    // If user has new messages
	if (cot::$usr['newpm']) {
        $showLast = (((int) cot::$cfg['pm']['showlast']) > 0);
        $messages = null;
        $db_pm = cot::$db->pm;
        $db_users = cot::$db->users;
        if (((int) cot::$cfg['pm']['showlast']) > 0) {
            $messages = cot::$db->query(
                "SELECT {$db_pm}.*, {$db_users}.*  FROM $db_pm " .
                "LEFT JOIN $db_users ON {$db_users}.user_id = {$db_pm}.pm_fromuserid ".
                ' WHERE pm_touserid=? AND pm_tostate='. COT_PM_STATE_UNREAD .
                ' ORDER BY pm_date DESC LIMIT ' . cot::$cfg['pm']['showlast'],
                cot::$usr['id']
            )->fetchAll();

            cot::$out['pm_lastMessages'] = $messages;
            echo '<pre>';
            //var_dump(cot::$out['pm_lastMessages']);

            echo '</pre>';
        }

        cot::$usr['messages'] = cot::$db->query(
            'SELECT COUNT(*) FROM ' . cot::$db->pm . ' WHERE pm_touserid=? AND pm_tostate=' . COT_PM_STATE_UNREAD,
            cot::$usr['id']
        )->fetchColumn();
	}
    cot::$out['pmreminder'] = cot_rc_link(cot_url('pm'),
		(cot::$usr['messages'] > 0) ?
            cot_declension(cot::$usr['messages'], $Ls['Privatemessages']) : cot::$L['hea_noprivatemessages']
	);

	$t->assign(array(
		'HEADER_USER_PM_URL' => cot_url('pm'),
		'HEADER_USER_PMS' => cot::$out['pms'],
		'HEADER_USER_PMREMINDER' => cot::$out['pmreminder']
	));
}

if (cot::$cfg['pm']['css'] && cot::$env['ext'] == 'pm') {
	Resources::linkFile(cot::$cfg['modules_dir'] . '/pm/tpl/pm.css');
}
