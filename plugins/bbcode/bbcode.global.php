<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Connects BBcode parser, loads data and registers parser function
 *
 * @package bbcode
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot_require('bbcode', true);

cot_bbcode_load();
cot_smilies_load();

$cot_parsers[] = 'cot_bbcode_parse';

?>
