<?PHP
/* ====================
[BEGIN_SED]
File=plugins/comedit/comedit.form.php
Version=0.0.2
Updated=2009-jan-03
Type=Plugin
Author=Asmo (Edited by motor2hg)
Description=Cotonti - Website engine http://www.cotonti.com Copyright (c) Cotonti Team 2009 BSD License
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=comedit
Part=form
File=comedit.form
Hooks=comments.newcomment.tags
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */
if(!defined('SED_CODE')) { die('Wrong URL.'); }

require_once(sed_langfile('comedit'));

$allowed_time = sed_build_timegap($sys['now_offset']-$cfg['plugin']['comedit']['time']*60,$sys['now_offset']);
$com_hint = sprintf($L['plu_comhint'], $allowed_time);

$t->assign(array("COMMENTS_FORM_HINT" => $com_hint));
?>