<?php
/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/search/search.forums.posts.first.php
Version=125
Updated=2008-may-15
Type=Plugin
Author=oc
Description=
[END_SED]

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
 * @package Seditio-N
 * @version 0.0.1
 * @author oc
 * @copyright Partial copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

$highlight = sed_import('highlight','G','TXT');



?>