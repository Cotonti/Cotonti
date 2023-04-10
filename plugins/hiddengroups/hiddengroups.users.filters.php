<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.filters
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package HiddenGroups
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var array<int, array<string, mixed>> $cot_groups
 * @var ?int $g Selected filter by main group. Group ID
 * @var ?int $gm Selected filter by group. Group ID
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('hiddengroups', 'plug');

$grpfilters_titles = [Cot::$R['users_sel_def_l'] . Cot::$L['Maingroup'] . Cot::$R['users_sel_def_r']];
$grpfilters_group_values = [cot_url('users')];
$grpfilters_maingrp_values = [cot_url('users')];

foreach ($cot_groups as $k => $i) {
	if (
        !in_array($k, cot_hiddengroups_get(cot_hiddengroups_mode()))
        || cot_auth('plug', 'hiddengroups', '1')
    ) {
		$grpfilters_titles[] = $cot_groups[$k]['name'];
		$grpfilters_maingrp_values[] = cot_url('users', 'g=' . $k, '', true);
		$grpfilters_group_values[] = cot_url('users', 'gm=' . $k, '', true);
	}
}

$maingrpfilters = cot_selectbox(
    cot_url('users', 'g=' . $g, '', true),
    'bymaingroup',
    $grpfilters_maingrp_values,
    $grpfilters_titles,
    false,
    ['onchange' => 'redirect(this)',],
    '',
    true
);

$grpfilters_titles[0] = Cot::$R['users_sel_def_l'] . Cot::$L['Group'] . Cot::$R['users_sel_def_r'];
$grpfilters = cot_selectbox(
    cot_url('users', 'gm=' . $gm, '', true),
    'bygroupms',
    $grpfilters_group_values,
    $grpfilters_titles,
    false,
    ['onchange' => 'redirect(this)',],
    '',
    true
);