<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
Order=8
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

$whosonlineRegisteredCount = Cot::$sys['whosonline_reg_count'] ?? 0;
$whosonlineVisitorsCount = Cot::$sys['whosonline_vis_count'] ?? 0;

Cot::$sys['whosonline_all_count'] = $whosonlineRegisteredCount + $whosonlineVisitorsCount;
Cot::$out['whosonline'] = cot_declension($whosonlineRegisteredCount, $Ls['Members'])
    . (
        !Cot::$cfg['plugin']['whosonline']['disable_guests']
            ? ', ' . cot_declension($whosonlineVisitorsCount, $Ls['Guests'])
            : ''
    );
