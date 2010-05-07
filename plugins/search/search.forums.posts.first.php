<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=search
Part=forums
File=search.forums.posts.first
Hooks=forums.posts.first
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * @package Cotonti
 * @version 0.7.0
 * @author oc
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$highlight = sed_import('highlight', 'G', 'TXT');

?>