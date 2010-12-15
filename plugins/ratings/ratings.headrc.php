<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=headrc
[END_COT_EXT]
==================== */

/**
 * Ratings JavaScript loader
 *
 * @package ratings
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot_headrc_load_file($cfg['plugins_dir'] . '/ratings/js/jquery.rating.js');
cot_headrc_load_file($cfg['plugins_dir'] . '/ratings/js/ratings.js');
?>
