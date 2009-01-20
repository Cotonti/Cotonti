<?PHP

/* ====================

[BEGIN_SED]
File=polls.php
Version=0.0.2
Updated=2009-jan-21
Type=Core
Author=Neocrome & Cotonti Team
Description=polls (Cotonti - Website engine http://www.cotonti.com Copyright (c) Cotonti Team 2009 BSD License)
[END_SED]

==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

/* === Hook === */
$extp = sed_getextplugins('polls.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('polls', 'a');
sed_block($usr['auth_read']);

$mode = sed_import('mode','G','ALP');
require_once($cfg['system_dir'].'/core/polls/polls.functions.php');

if ($mode=='ajax')
{
$skin = sed_import('poll_skin','P','TXT');
$id = sed_import('poll_id','P','INT');
sed_sendheaders();
		sed_poll_vote();
		list($polltitle, $poll_form)=sed_poll_form($id, '', $skin);
		echo $poll_form;
	
	exit;

}

$id = sed_import('id','G','ALP', 8);
$vote = sed_import('vote','G','TXT');
if (!empty($vote))
{$vote=explode(" ", $vote);}
if (empty($vote))
{$vote = sed_import('vote','P','ARR');}

$comments = sed_import('comments','G','BOL');
$ratings = sed_import('ratings','G','BOL');

$out['subtitle'] = $L['Polls'];

/* === Hook === */
$extp = sed_getextplugins('polls.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate(sed_skinfile('polls'));

if (!empty($error_string))
{
	$t->assign("POLLS_EXTRATEXT",$error_string);
	$t->parse("MAIN.POLLS_EXTRA");
}
elseif ($id=='viewall' || $id=='')
{
		$sql = sed_sql_query("SELECT * FROM $db_polls WHERE poll_state=0 AND poll_type=0 ORDER BY poll_id DESC");

	$result = "<table class=\"cells\">";

	if (sed_sql_numrows($sql)==0)
	{ $result .= "<tr><td>".$L['None']."</td></tr>"; }
	else
	{
		while ($row = sed_sql_fetcharray($sql))
		{
			$result .= "<tr>";
			$result .= "<td style=\"width:128px;\">".date($cfg['formatyearmonthday'], $row['poll_creationdate'] + $usr['timezone'] * 3600)."</td>";
			$result .= "<td><a href=\"".sed_url('polls', 'id='.$row['poll_id'])."\"><img src=\"images/admin/polls.gif\" alt=\"\" />";
			$result .= sed_parse(sed_cc($row['poll_text']),1 ,1 ,1)."</a></td>";
			$result .= "</tr>";
		}
	}
	$result .= "</table>";

	$t->assign(array(
		"POLLS_LIST" => $result,
	));

	$t->parse("MAIN.POLLS_VIEWALL");
}
else
{
		$id = sed_import($id,'D','INT');
		sed_poll_vote();
		list($polltitle, $poll_form)=sed_poll_form($id);
	$item_code = 'v'.$id;
	list($comments_link, $comments_display) = sed_build_comments($item_code, sed_url('polls', 'id='.$id), $comments);
	$t->assign(array(
		"POLLS_TITLE" => $polltitle,
		"POLLS_FORM" => $poll_form,
		"POLLS_COMMENTS" => $comments_link,
		"POLLS_COMMENTS_DISPLAY" => $comments_display,
		"POLLS_VIEWALL" => "<a href=\"".sed_url('polls', 'id=viewall')."\">".$L['polls_viewarchives']."</a>",
	));

	$t->parse("MAIN.POLLS_VIEW");

	if ($alreadyvoted)
	{ $extra = ($votecasted) ? $L['polls_votecasted'] : $L['polls_alreadyvoted']; }
	else
	{ $extra = $L['polls_notyetvoted']; }

	$t->assign(array(
		"POLLS_EXTRATEXT" => $extra,
	));

	$t->parse("MAIN.POLLS_EXTRA");

}

/* === Hook === */
$extp = sed_getextplugins('polls.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");
require_once $cfg['system_dir'] . '/footer.php';

//sed_sendheaders();
?>
