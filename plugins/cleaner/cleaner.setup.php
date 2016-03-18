<?php
/* ====================
[BEGIN_COT_EXT]
Code=cleaner
Name=Cleaner
Category=administration-management
Description=Will clean various things...
Version=1.7.1
Date=2015-jan-22
Author=Neocrome & Cotonti Team
Copyright=Partial copyright (c) Cotonti Team 2008-2016
Notes=BSD License
SQL=
Auth_guests=0
Lock_guests=RW12345A
Auth_members=R
Lock_members=W12345A
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
userprune=05:select:0,1,2,3,4,5,6,7:2:Delete the user accounts not activated within * days (0 to disable).
logprune=06:select:0,1,2,3,7,15,30,60:15:Delete the log entries older than * days (0 to disable).
refprune=04:select:0,15,30,60,120,180,365:30:Delete the referer entries older than * days (0 to disable).
pmnotread=05:select:0,15,30,60,120,180,365:120:Delete the private messages older than * days and not read by the recipient (0 to disable).
pmnotarchived=06:select:0,15,30,60,120,180,365:180:Delete the private messages older than * days and not archived (0 to disable).
pmold=07:select:0,15,30,60,120,180,365:365:Delete ALL the private messages older than * days (0 to disable).
[END_COT_EXT_CONFIG]
==================== */

/**
 * Will clean various things
 *
 * @package Cleaner
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');
