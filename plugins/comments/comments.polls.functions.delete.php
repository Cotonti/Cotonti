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

require_once sed_incfile('config', 'comments', true);
require_once sed_incfile('functions', 'comments', true);

sed_comments_remove('polls', $id2);

?>