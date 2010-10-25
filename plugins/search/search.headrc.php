<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=headrc
[END_COT_EXT]
==================== */

/**
 * Static head resources for search
 *
 * @package search
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot_headrc_load_file($cfg['plugins_dir'].'/search/js/hl.js');
?>