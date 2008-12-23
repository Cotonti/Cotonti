<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/comedit/comedit.form.php
Version=120
Updated=2007-mar-01
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=comedit
Part=form
File=comedit.form
Hooks=comments.newcomment.tags
Tags=
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]

==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

if (@file_exists($cfg['plugins_dir'].'/comedit/lang/comedit.'.$usr['lang'].'.lang.php'))
{
	require_once($cfg['plugins_dir'].'/comedit/lang/comedit.'.$usr['lang'].'.lang.php');
}
else
{
	require_once($cfg['plugins_dir'].'/comedit/lang/comedit.en.lang.php');
}

$allowed_time = sed_build_timegap($sys['now_offset']-$cfg['plugin']['comedit']['time']*60,$sys['now_offset']);
$com_hint = sprintf($L['plu_comhint'], $allowed_time);

$t->assign(array(
	"COMMENTS_FORM_HINT" => $com_hint,
));
?>