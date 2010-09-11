<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.rightsbyitem.case
[END_COT_EXT]
==================== */

/**
 * Forum rights by item
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

sed_require('forums');

$forum = sed_forum_info($io);
$title = ' : '.htmlspecialchars($forum['fs_title'])." (#".$io.")";

?>
