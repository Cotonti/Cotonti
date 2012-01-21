<?php
/**
 * RSS root-level redirector for backwards compatibility
 *
 * @package Cotonti
 * @version 0.9.4
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2012
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

$_GET['e'] = 'rss';

require 'index.php';

?>