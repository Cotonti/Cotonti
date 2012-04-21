<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
 * Static head resources for search
 *
 * @package search
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
if ($cfg['jquery'])
{
	cot_rc_add_file($cfg['plugins_dir'].'/search/js/hl.min.js');
}
?>