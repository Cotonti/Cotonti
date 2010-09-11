<?php
/* ====================
[BEGIN_COT_EXT]
Name=RSS
Description=Provides RSS/Atom feeds for your site
Version=0.7.0.1
Date=2010-jun-22
Author=Neocrome & Cotonti Team
Copyright=(c) Cotonti Team 2008-2010
Notes=BSD License
Auth_guests=R
Lock_guests=A
Auth_members=R
Lock_members=
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
disable_rss=01:radio::0:
rss_timetolive=02:select:0,10,20,30,40,50,60,120,180,140,200:30:
rss_maxitems=03:select:5,10,15,20,25,30,35,40,45,50,60,70,75,80,90,100,150,200:40:
rss_charset=04:string::UTF-8:
rss_pagemaxsymbols=05:string:::
rss_postmaxsymbols=06:string:::
[END_COT_EXT_CONFIG]
==================== */

/**
 * RSS setup file
 *
 * @package rss
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */
?>