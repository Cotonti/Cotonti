<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
Order=9
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

$sys['whosonline_all_count'] = $sys['whosonline_reg_count'] + $sys['whosonline_vis_count'];
$out['whosonline'] = ($cfg['disablewhosonline']) ? '' : cot_declension($sys['whosonline_reg_count'], $Ls['Members']).(!$cfg['plugin']['whosonline']['disable_guests'] ? ', '.cot_declension($sys['whosonline_vis_count'], $Ls['Guests']) : '');
