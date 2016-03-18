<?php
/* ====================
[BEGIN_COT_EXT]
Code=markitup
Name=MarkItUp!
Category=editor-parser
Description=Plain-source BBcode/HTML editor using jQuery
Version=1.2.1-1.1.14
Date=2015-11-07
Author=Jay Salvat, http://markitup.jaysalvat.com
Copyright=Copyright (C) 2007-2016 Jay Salvat
Notes=Dual licensed under the MIT and GPL licenses.
SQL=
Auth_guests=R
Lock_guests=12345A
Auth_members=RW
Lock_members=12345A
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
skin=01:string::markitup:Skin of editor (plugins/markitup/skins/xxxxx)
autorefresh=02:radio::0:Enable preview auto-refresh
chili=03:radio::0:Enable Chili tags
[END_COT_EXT_CONFIG]
==================== */

/**
 * jQuery BBcode editor
 *
 * @package MarItUp
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');
