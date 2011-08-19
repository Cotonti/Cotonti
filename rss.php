<?php
/**
 * RSS module
 *
 * @package Cotonti
 * @version 0.9.4
 * @author medar, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2011
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

/*
Example of feeds:

rss.php?c=topics&id=XX			=== Show posts from topic "XX" ===							=== Where XX - is code of topic ===

rss.php?c=section&id=XX 		=== Show posts from all topics of section "XX" ===			=== Where XX - is code of section (this and all subsections) forum ===

rss.php?c=forums				=== Show posts from all topics of all sections forum ===

rss.php?c=pages&id=XX			=== Show pages from category "XX" ===						=== Where XX - is code of category pages ===

rss.php
	OR rss.php?c=pages			=== Show pages from category "news" ===
*/

// Environment setup
define('COT_CODE', true);
define('COT_MODULE', true);
$env['ext'] = 'rss';

// Basic requirements
require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

require_once $cfg['modules_dir'] . '/rss/rss.php';

?>