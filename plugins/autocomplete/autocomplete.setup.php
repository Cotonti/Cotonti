<?php
/* ====================
[BEGIN_COT_EXT]
Code=autocomplete
Name=Autocomplete
Category=misc-ext
Description=Autocomplete for user names in some forms
Version=0.8.3
Date=2011-jan-8
Author=esclkm
Copyright=Copyright (c) Cotonti Team 2008-2014
Notes=BSD License
SQL=
Auth_guests=R
Lock_guests=12345A
Auth_members=RW
Lock_members=12345A
Recommends_plugins=htmlpurifier
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
autocomplete=23:select:0,1,2,3,4,5,6:3:Min. chars for autocomplete
css=99:radio:0,1:1:Enable plugin CSS
[END_COT_EXT_CONFIG]
==================== */

/**
 * Setup file for Autocomplete plugin
 *
 * @package Autocomplete
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');
