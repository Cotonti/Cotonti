<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=input
Order=10
[END_COT_EXT]
==================== */

/**
 * Who's online (part 1)
 *
 * @package WhosOnline
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('whosonline', 'plug');

$not_counted_vis = $not_counted_usr = 0;

if (Cot::$usr['id'] > 0) {
	$sql = Cot::$db->query("SELECT * FROM $db_online WHERE online_userid=".$usr['id']);

	if ($sql->rowCount() == 1) {
		$online_row = $sql->fetch();
		$online_count = 1;
		$sys['online_location'] = $online_row['online_location'];
		$sys['online_subloc'] = $online_row['online_subloc'];

	} else {
		$not_counted_usr = 1;
	}
	$sql->closeCursor();

} elseif(!Cot::$cfg['plugin']['whosonline']['disable_guests']) {
	$sql = Cot::$db->query("SELECT * FROM $db_online WHERE online_ip='".$usr['ip']."' AND online_userid < 0 LIMIT 1");

	if ($sql->rowCount() > 0) {
		$online_row = $sql->fetch();
		$sys['online_location'] = $online_row['online_location'];
		$sys['online_subloc'] = $online_row['online_subloc'];

	} else {
		$not_counted_vis = 1;
	}
	$sql->closeCursor();
}

$cache_type = !empty(Cot::$cache->mem) ? Cot::$cache->mem : null;
$out['whosonline_reg_list'] = '';
if (Cot::$cache && $cache_type && $cache_type->exists('whosonline', 'system')) {
	$whosonline_data = $cache_type->get('whosonline', 'system');
    Cot::$sys['whosonline_vis_count'] = $whosonline_data['vis_count'];
    Cot::$sys['whosonline_reg_count'] = $whosonline_data['reg_count'];
    if (Cot::$sys['whosonline_reg_count'] === 0 && Cot::$usr['id'] > 0) {
        Cot::$sys['whosonline_reg_count'] = 1;
        $not_counted_usr = 1;
    }
    Cot::$out['whosonline_reg_list'] = $whosonline_data['reg_list'];
	$cot_usersonline = $whosonline_data['user_list'];
	unset($whosonline_data);

} else {
	$online_timedout = $sys['now'] - Cot::$cfg['timedout'];
    Cot::$db->delete($db_online, "online_lastseen < $online_timedout");

	if (!Cot::$cfg['plugin']['whosonline']['disable_guests']) {
        $sys['whosonline_vis_count'] = Cot::$db->query(
                "SELECT COUNT(*) FROM $db_online WHERE online_name='v'"
            )->fetchColumn() + $not_counted_vis;
    }

	$sql_o = Cot::$db->query("SELECT DISTINCT o.online_name, o.online_userid FROM $db_online o WHERE o.online_name != 'v' ORDER BY online_name ASC");
    Cot::$sys['whosonline_reg_count'] = $sql_o->rowCount() + $not_counted_usr;
    if (Cot::$sys['whosonline_reg_count'] === 0 && Cot::$usr['id'] > 0) {
        Cot::$sys['whosonline_reg_count'] = 1;
        $not_counted_usr = 1;
    }

	$ii_o = 0;
	$cot_usersonline = array();
	while ($row_o = $sql_o->fetch()) {
		$out['whosonline_reg_list'] .= ($ii_o > 0) ? ', ' : '';
		$out['whosonline_reg_list'] .= cot_build_user($row_o['online_userid'], $row_o['online_name']);
		$cot_usersonline[] = $row_o['online_userid'];
		$ii_o++;
	}
	$sql_o->closeCursor();
	if ($not_counted_usr) {
		$out['whosonline_reg_list'] .= (!empty($out['whosonline_reg_list']) ? ', ' : '') .
            cot_build_user($usr['id'], $usr['name']);
		$cot_usersonline[] = $usr['id'];
	}
	unset($ii_o, $sql_o, $row_o, $not_counted_usr, $not_counted_vis);

	if (Cot::$cache && $cache_type) {
		$whosonline_data = array(
			'vis_count' => $sys['whosonline_vis_count'],
			'reg_count' => $sys['whosonline_reg_count'],
			'reg_list' => $out['whosonline_reg_list'],
			'user_list' => $cot_usersonline
		);
		$cache_type->store('whosonline', $whosonline_data, 'system', 30);
	}
}
