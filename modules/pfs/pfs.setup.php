<?php
/* ====================
[BEGIN_COT_EXT]
Name=PFS
Description=Personal File Space
Version=0.7.0.5
Date=2010-oct-05
Author=Neocrome & Cotonti Team
Copyright=(c) Cotonti Team 2008-2010
Notes=BSD License
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
pfsuserfolder=01:radio::0:
pfstimename=02:radio::0:
pfsfilecheck=03:radio::1:
pfsnomimepass=04:radio::1:
maxpfsperpage=05:select:5,10,15,20,25,30,40,50,60,70,100,200,500:15:
pfs_winclose=05:radio::0:
th_separator=06:separator:::
th_amode=07:select:Disabled,GD1,GD2:GD2:
th_x=08:string::112:
th_y=09:string::84:
th_border=10:string::4:
th_dimpriority=11:select:Width,Height:Width:
th_keepratio=12:radio::1:
th_jpeg_quality=13:select:0,5,10,20,30,40,50,60,70,75,80,85,90,95,100:85:
th_colorbg=14:string::000000:
th_colortext=15:string::FFFFFF:
th_textsize=16:range:0,5:1:
[END_COT_EXT_CONFIG]
==================== */

/**
 * PFS setup file
 *
 * @package pfs
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */
?>
