<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=polls.functions.delete
File=comments.polls.functions.delete
Hooks=polls.functions.delete
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

global $db_com;
$sql = sed_sql_query("DELETE FROM $db_com WHERE com_code = 'v".$id2."'");

?>