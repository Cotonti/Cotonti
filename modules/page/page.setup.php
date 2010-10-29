<?php
/* ====================
[BEGIN_COT_EXT]
Name=Pages
Description=Pages and Categories
Version=0.7.0.5
Date=2010-oct-05
Author=Neocrome & Cotonti Team
Copyright=(c) Cotonti Team 2008-2010
Notes=BSD License
Auth_guests=R
Lock_guests=A
Auth_members=RW1
Lock_members=
Admin_icon=img/adminmenu_page.png
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
markup=01:radio::1:
count_admin=03:radio::0:
autovalidate=04:radio::1:
maxrowsperpage=05:select:5,10,15,20,25,30,40,50,60,70,100,200,500:30:
maxlistsperpage=06:select:5,10,15,20,25,30,40,50,60,70,100,200,500:30:
[END_COT_EXT_CONFIG]

[BEGIN_COT_EXT_CONFIG_STRUCTURE]
order=01:callback:cot_page_config_order():title:
way=02:select:asc,desc:asc:
ratings=11:radio::1:
[END_COT_EXT_CONFIG_STRUCTURE]
==================== */

/**
 * Page module setup file
 *
 * @package page
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

?>