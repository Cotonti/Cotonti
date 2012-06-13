<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.filters
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package hiddengroups
 * @version 1.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('hiddengroups', 'plug');

$grpfilters_titles = array($L['Maingroup']);
$grpfilters_group_values = array(cot_url('users'));
$grpfilters_maingrp_values = array(cot_url('users'));
foreach($cot_groups as $k => $i)
{
	if(!in_array($k, cot_hiddengroups_get(cot_hiddengroups_mode())) || cot_auth('plug', 'hiddengroups', '1'))
	{
		$grpfilters_titles[] = $cot_groups[$k]['name'];
		$grpfilters_maingrp_values[] = cot_url('users', 'g='.$k);
		$grpfilters_group_values[] = cot_url('users', 'gm='.$k);
	}
}
$maingrpfilters = cot_selectbox($g, 'bymaingroup', $grpfilters_maingrp_values, $grpfilters_titles, false, array('onchange' => 'redirect(this)'));

$grpfilters_titles[0] = $L['Group'];
$grpfilters = cot_selectbox($g, 'bygroupms', $grpfilters_group_values, $grpfilters_titles, false, array('onchange' => 'redirect(this)'));

?>