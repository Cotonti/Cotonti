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

if (Cot::$usr['id'] > 0) {
	Cot::$out['pms'] = cot_rc_link(cot_url('pm'), Cot::$L['Private_Messages']);

	require_once cot_incfile('pm', 'module');
    // If user has new messages
	if (Cot::$usr['newpm']) {
        $showLast = (((int) Cot::$cfg['pm']['showlast']) > 0);
        $messages = null;
        $db_pm = Cot::$db->pm;
        $db_users = Cot::$db->users;
        if (((int) Cot::$cfg['pm']['showlast']) > 0) {
            $messages = Cot::$db->query(
                "SELECT {$db_pm}.*, {$db_users}.*  FROM $db_pm " .
                "LEFT JOIN $db_users ON {$db_users}.user_id = {$db_pm}.pm_fromuserid ".
                ' WHERE pm_touserid=? AND pm_tostate='. COT_PM_STATE_UNREAD .
                ' ORDER BY pm_date DESC LIMIT ' . Cot::$cfg['pm']['showlast'],
                Cot::$usr['id']
            )->fetchAll();

            Cot::$out['pm_lastMessages'] = $messages;
        }

        Cot::$usr['messages'] = Cot::$db->query(
            'SELECT COUNT(*) FROM ' . Cot::$db->pm . ' WHERE pm_touserid=? AND pm_tostate=' . COT_PM_STATE_UNREAD,
            Cot::$usr['id']
        )->fetchColumn();
	}
    Cot::$out['pmreminder'] = cot_rc_link(cot_url('pm'),
		(Cot::$usr['messages'] > 0) ?
            cot_declension(Cot::$usr['messages'], $Ls['Privatemessages']) : Cot::$L['hea_noprivatemessages']
	);

	$t->assign(array(
		'HEADER_USER_PM_URL' => cot_url('pm'),
		'HEADER_USER_PMS' => Cot::$out['pms'],
		'HEADER_USER_PMREMINDER' => Cot::$out['pmreminder']
	));
}

if (Cot::$cfg['pm']['css'] && Cot::$env['ext'] == 'pm') {
	Resources::linkFile(Cot::$cfg['modules_dir'] . '/pm/tpl/pm.css');
}
