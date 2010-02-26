<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=global
File=comments.global
Hooks=global
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

require_once sed_langfile('comments', 'plug');
require_once $cfg['plugins_dir'] . '/comments/inc/config.php';
require_once $cfg['plugins_dir'] . '/comments/inc/resources.php';
require_once $cfg['plugins_dir'] . '/comments/inc/functions.php';

?>