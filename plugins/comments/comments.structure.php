<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=structure
File=comments.structure
Hooks=structure
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$sed_cat[$row['structure_code']]['com'] = $row['structure_comments'];

?>