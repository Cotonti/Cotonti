<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.update.done
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package Cotonti
 * @version 0.9.6
 * @author Koradhil, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$cache && $cache->db->remove('hiddenusers', 'system');

?>