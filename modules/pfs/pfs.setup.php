<?php
/* ====================
[BEGIN_COT_EXT]
Name=PFS
Description=Personal File Space
Version=1.0.11
Date=2015-08-01
Author=Neocrome & Cotonti Team
Copyright=(c) Cotonti Team 2008-2016
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
pfsmaxuploads=05:string::8:
maxpfsperpage=06:string::15:
pfs_winclose=07:radio::0:
th_separator=08:separator:::
th_amode=09:select:Disabled,GD1,GD2:GD2:
th_x=10:string::112:
th_y=11:string::84:
th_border=12:string::4:
th_dimpriority=13:select:Width,Height:Width:
th_keepratio=14:radio::1:
th_jpeg_quality=15:select:0,5,10,20,30,40,50,60,70,75,80,85,90,95,100:85:
th_colorbg=16:string::000000:
th_colortext=17:string::FFFFFF:
th_textsize=18:range:0,5:1:
[END_COT_EXT_CONFIG]
==================== */

/**
 * PFS setup file
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
