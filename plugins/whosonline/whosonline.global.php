<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
Order=8
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

Cot::$sys['whosonline_all_count'] = Cot::$sys['whosonline_reg_count'] + Cot::$sys['whosonline_vis_count'];
Cot::$out['whosonline'] = cot_declension(Cot::$sys['whosonline_reg_count'], $Ls['Members']) .
    (
        !Cot::$cfg['plugin']['whosonline']['disable_guests'] ?
            ', ' . cot_declension(Cot::$sys['whosonline_vis_count'], $Ls['Guests']) : ''
    );
