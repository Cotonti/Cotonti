<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=polls.php
Version=120
Updated=2007-mar-03
Type=Core
Author=Neocrome
Description=Polls
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

$polls_header1 = $cfg['doctype']."<html><head>
<title>".$cfg['maintitle']."</title>".sed_htmlmetas();

$polls_header2 = "</head><body>";
$polls_footer = "</body></html>";

$id = sed_import('id','G','ALP', 8);
$vote = sed_import('vote','G','TXT');
if (!empty($vote))
{$vote=explode(" ", $vote);}
if (empty($vote))
{$vote = sed_import('vote','P','ARR');}

$comments = sed_import('comments','G','BOL');
$ratings = sed_import('ratings','G','BOL');

require_once($cfg['system_dir'].'/core/polls/polls.functions.php');

$out['subtitle'] = $L['Polls'];

/* === Hook === */
$extp = sed_getextplugins('polls.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t = new XTemplate(sed_skinfile('polls'));

$t->assign(array(
	"POLLS_HEADER1" => $polls_header1,
	"POLLS_HEADER2" => $polls_header2,
	"POLLS_FOOTER" => $polls_footer,
));

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
			$result .= "<td><a href=\"".sed_url('polls', 'id='.$row['poll_id'])."\"><img src=\"images/admin/polls.gif\" alt=\"\" /></a></td>";
			$result .= "<td>".sed_parse(sed_cc($row['poll_text']),1 ,1 ,1)."</td>";
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
	list($polltext, $polldate, $totalvotes, $polloptions, $polloptions_bar, $polloptions_per, $polloptions_count, $pollbutton, $alreadyvoted)=sed_new_poll($id);
		$result = (!$alreadyvoted) ? "<form action=\"".sed_url('polls', "id=".$id."")."\" method=\"post\">" :"";
	$result .= "<table class=\"cells\">";

	$option_count = (count($polloptions) ? count($polloptions) : 0);
	
	for($i = 0; $i < $option_count; $i++) {

		$result .= "<tr><td>";
		$result .= $polloptions[$i];
		$result .= "</td><td>".$polloptions_bar[$i]."</td><td>".$polloptions_per[i]."</td><td>(".$polloptions_count[$i].")</td></tr>";

	}
	$result .= (!$alreadyvoted) ? "<tr><td colspan=\"4\">".$pollbutton."</td></tr></table></form>" :"</table>";

	$item_code = 'v'.$id;
	list($comments_link, $comments_display) = sed_build_comments($item_code, sed_url('polls', 'id='.$id), $comments);

	$t->assign(array(
		"POLLS_VOTERS" => $totalvotes,
		"POLLS_SINCE" => $polldate,
		"POLLS_TITLE" => $polltext,
		"POLLS_RESULTS" => $result,
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

sed_sendheaders();
@ob_end_flush();
@ob_end_flush();

?>
