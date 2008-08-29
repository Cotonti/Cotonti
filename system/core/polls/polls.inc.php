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
<title>".$cfg['maintitle']."</title>".sed_htmlmetas()."
<script type=\"text/javascript\">
<!--
function help(rcode)
	{	window.open('plug.php?h='+rcode,'Help','toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=400,height=512,left=32,top=16'); }
function comments(rcode)
	{ window.open('comments.php?id='+rcode,'Comments','toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=400,height=512,left=16,top=16'); }
function help(rcode,c1,c2)
	{ window.open('plug.php?h='+rcode+'&c1='+c1+'&c2='+c2,'Help','toolbar=0,location=0,directories=0,menuBar=0,resizable=0,scrollbars=yes,width=480,height=512,left=512,top=16'); }
function pfs(id,c1,c2)
	{
	window.open('pfs.php?userid='+id+'&c1='+c1+'&c2='+c2,'PFS','status=1, toolbar=0,location=0,directories=0,menuBar=0,resizable=1,scrollbars=yes,width=754,height=512,left=32,top=16');
	}
//-->
</script>";

$polls_header2 = "</head><body>";
$polls_footer = "</body></html>";

$id = sed_import('id','G','ALP', 8);
$vote = sed_import('vote','G','INT');
$comments = sed_import('comments','G','BOL');
$ratings = sed_import('ratings','G','BOL');

if ($id=='viewall')
	{
	$sql = sed_sql_query("SELECT * FROM $db_polls WHERE poll_state=0 AND poll_type=0 ORDER BY poll_id DESC");
	}
else
	{
	$id = sed_import($id,'D','INT');
	$sql = sed_sql_query("SELECT * FROM $db_polls WHERE poll_id='$id' AND poll_state=0");

	if ($row = sed_sql_fetcharray($sql))
		{
		$poll_state = $row['poll_state'];
		$poll_minlevel = $row['poll_minlevel'];

		if ($usr['id']>0)
		 	{ $sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$id' AND (pv_userid='".$usr['id']."' OR pv_userip='".$usr['ip']."') LIMIT 1"); }
			else
		 	{ $sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$id' AND pv_userip='".$usr['ip']."' LIMIT 1"); }

		$alreadyvoted = (sed_sql_numrows($sql2)>0) ? 1 : 0;

		if ($a=='send' && empty($error_string) && !$alreadyvoted)
			{
			$sql2 = sed_sql_query("UPDATE $db_polls_options SET po_count=po_count+1 WHERE po_pollid='$id' AND po_id='$vote'");
			if (sed_sql_affectedrows()==1)
				{
				$sql2 = sed_sql_query("INSERT INTO $db_polls_voters (pv_pollid, pv_userid, pv_userip) VALUES (".(int)$id.", ".(int)$usr['id'].", '".$usr['ip']."')");
				$votecasted = TRUE;
				$alreadyvoted = TRUE;
				}
			}

		$sql2 = sed_sql_query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$id'");
		$totalvotes = sed_sql_result($sql2,0,"SUM(po_count)");

		$sql1 = sed_sql_query("SELECT po_id,po_text,po_count FROM $db_polls_options WHERE po_pollid='$id' ORDER by po_id ASC");
		$error_string = (sed_sql_numrows($sql1)<1) ? $L['wrongURL'] : '';
		}
       else
		{ $error_string = $L['wrongURL']; }

	}

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
elseif ($id=='viewall')
	{
	$result = "<table class=\"cells\">";

	if (sed_sql_numrows($sql)==0)
		{ $result .= "<tr><td>".$L['None']."</td></tr>"; }
       else
		{
		while ($row = sed_sql_fetcharray($sql))
			{
			$result .= "<tr>";
			$result .= "<td style=\"width:128px;\">".date($cfg['formatyearmonthday'], $row['poll_creationdate'] + $usr['timezone'] * 3600)."</td>";
			$result .= "<td><a href=\"polls.php?id=".$row['poll_id']."\"><img src=\"images/admin/polls.gif\" alt=\"\" /></a></td>";
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
	$result = "<table class=\"cells\">";

	while ($row1 = sed_sql_fetcharray($sql1))
		{
		$po_id = $row1['po_id'];
		$po_count = $row1['po_count'];
		$percent = @round(100 * ($po_count / $totalvotes),1);
		$percentbar = floor($percent * 2.24);

		$result .= "<tr><td>";
		$result .= ($alreadyvoted) ? sed_parse(sed_cc($row1['po_text']), 1, 1, 1) : "<a href=\"polls.php?a=send&amp;".sed_xg()."&amp;id=".$id."&amp;vote=".$po_id."\">".sed_parse(sed_cc($row1['po_text']), 1, 1, 1)."</a>";
		$result .= "</td><td><div style=\"width:256px;\"><div class=\"bar_back\"><div class=\"bar_front\" style=\"width:".$percent."%;\"></div></div></div></td><td>$percent%</td><td>(".$po_count.")</td></tr>";

		}

	$result .= "</table>";

	$item_code = 'v'.$id;
	list($comments_link, $comments_display) = sed_build_comments($item_code, "polls.php?id=".$id, $comments);

	$t->assign(array(
		"POLLS_VOTERS" => $totalvotes,
		"POLLS_SINCE" => date($cfg['dateformat'], $row['poll_creationdate'] + $usr['timezone'] * 3600),
		"POLLS_TITLE" => sed_parse(sed_cc($row['poll_text']), 1, 1, 1),
		"POLLS_RESULTS" => $result,
		"POLLS_COMMENTS" => $comments_link,
		"POLLS_COMMENTS_DISPLAY" => $comments_display,
		"POLLS_VIEWALL" => "<a href=\"polls.php?id=viewall\">".$L['polls_viewarchives']."</a>",
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
