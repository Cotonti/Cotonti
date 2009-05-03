<?PHP
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
 * @version 0.0.3
 * @author oc
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

$highlight = sed_import('highlight','G','TXT');

?>