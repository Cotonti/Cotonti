<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * Polls functions.
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

function sed_poll_edit_form($id, $multiple=1)
{
	global $cfg, $L, $db_polls, $db_polls_options;
	global $error_string;
	$poll_options ='';
	if(!empty($error_string))
	{
		global $poll_id, $poll_option_text, $poll_option_id, $poll_multiple, $poll_text;

		$counter=0;
		$date="";
		$id=$poll_id;
		$poll_multiple=($poll_multiple) ? "checked='checked'":"";
		$option_count = (count($poll_option_id) ? count($poll_option_id) : 0);
		for($i = 0; $i < $option_count; $i++)
		{
			if ($poll_option_text[$i]!="" || ($poll_option_text[$i]=="" && $poll_option_id[$i]!='new'))
			{
				$counter++;
				$poll_options_x .="<div id ='ans_".$counter."'><input type=\"hidden\" name=\"poll_option_id[]\" value=\"".$poll_option_id[$i]."\" />
			<input  class='tbox' type='text' name='poll_option[]' size='40' value=\"".$poll_option_text[$i]."\" maxlength='128' /> <input  name=\"addoption\" value=\"x\" onclick=\"return removeAns(".$counter.")\" type=\"button\" /></div>";
			}
		}

	}
	elseif ($id!='new')
	{
		$sql = sed_sql_query("SELECT * FROM $db_polls WHERE poll_id='$id' ");
		$sql1 = sed_sql_query("SELECT * FROM $db_polls_options WHERE po_pollid='$id' ORDER by po_id ASC");
		$row = sed_sql_fetcharray($sql);
		$poll_text=sed_cc($row["poll_text"]);
		$poll_multiple=($row["poll_multiple"]) ? "checked='checked'":"";
		$date=date($cfg['dateformat'], $row["poll_creationdate"])." GMT";
		$counter=0;
		while ($row1 = sed_sql_fetcharray($sql1))
		{
			$counter++;
			$poll_options_x .="<div id ='ans_".$counter."'><input type=\"hidden\" name=\"poll_option_id[]\" value=\"".$row1['po_id']."\" />
				<input  class='tbox' type='text' name='poll_option[]' size='40' value=\"".sed_cc($row1['po_text'])."\" maxlength='128' /> <input  name=\"addoption\" value=\"x\" onclick=\"return removeAns(".$counter.")\" type=\"button\" />
</div>";

		}
	}
	else
	{
		$poll_text='';
		$date='';
		$poll_options_x="";
		$counter=0;
		$poll_multiple="";
	}
	if ($counter==0)
	{
		$poll_options_x="<div id ='ans_1'><input type=\"hidden\" name=\"poll_option_id[]\" value=\"new\" />
				<input class='tbox' type='text' name='poll_option[]' size='40' value=\"\" maxlength='128' /> <input  name=\"addoption\" value=\"x\" onclick=\"return removeAns(1)\" type=\"button\" />
				</div>";
		$counter++;
	}
	if ($counter==1)
	{
		$poll_options_x.="<div id ='ans_2'><input type=\"hidden\" name=\"poll_option_id[]\" value=\"new\" />
				<input class='tbox' type='text' name='poll_option[]' size='40' value=\"\" maxlength='128' /> <input  name=\"addoption\" value=\"x\" onclick=\"return removeAns(1)\" type=\"button\" />
				</div>";
		$counter++;
	}
	$poll_text="<input type=\"text\" class=\"text\" name=\"poll_text\" value=\"".$poll_text."\" size=\"64\" maxlength=\"255\" /><input type=\"hidden\" name=\"poll_id\" value=\"".$id."\" />";
	$poll_date=$date."<input type=\"hidden\" name=\"poll_date\" value=\"".$date."\" />";

	// Render rules table
	$poll_options .= <<<HTM
<script type="text/javascript">
//<![CDATA[

var ansCount = $counter+1;
var ansMax = {$cfg['max_options_polls']} + 1;
var ansCC = ansCount;
function removeAns(ii){
	$('#ans_' + (ii) + ' input[name="poll_option[]"]').attr("value", "");
	if (ansCC>3)
	{ 	ansMax++; ansCC--;
	if ($('#ans_' + (ii) + ' input[name="poll_option_id[]"]').value !="new")
	{ $('#ans_' + (ii)).hide(); }
	else { $('#ans_' + (ii)).remove();
	}
	}

	return false;
}

function addAns() {
	if (ansCount<ansMax)
	{
	$('#ans_' + (ansCount - 1)).after('<div id="ans_' + ansCount + '"><input type="hidden" name="poll_option_id[]" value="new" /><input  class="tbox" type="text" name="poll_option[]" size="40" value="" maxlength="128" /> <input class="delbutton" name="addoption" value="x" onclick="removeAns('+ansCount+')" type="button" /><\/div>');
	ansCount++;
	ansCC++
	}

	return false;
}
//]]>
</script>
HTM;

	$poll_options .= "<div id='ans_0'></div>";
	$poll_options .= $poll_options_x;
	if ($counter<$cfg['max_options_polls'])
	{
		$poll_options .= "<noscript><div id ='ans_no'><input type=\"hidden\" name=\"poll_option_id[]\" value=\"new\" />
				<input class='tbox' type='text' name='poll_option[]' size='40' value=\"\" maxlength='128' /></div></noscript>";
	}
	$poll_options .= "
	<input  name=\"addoption\" value=\"".$L['Add']."\" onclick=\"return addAns()\" type=\"button\" />
";

	if ($multiple==1)
	{$poll_settings .= "<label><input name=\"poll_multiple\" type=\"checkbox\" value=\"1\" $poll_multiple />".$L['polls_multiple']."</label>";
	}
	elseif ($multiple==0)
	{$poll_settings .= "<input type=\"hidden\" name=\"poll_multiple\" value=\"0\" />";
	}


	return(array($poll_text, $poll_options, $poll_date, $poll_settings));
}

/* ------------------ */

function sed_poll_check()
{
	global $cfg, $L;
	global $error_string;
	global $poll_id, $poll_text, $poll_option_id, $poll_multiple, $poll_option_text;
	$poll_id = sed_import('poll_id','P','TXT');

	if (!empty($poll_id))
	{
		$poll_text = trim(sed_import('poll_text','P','HTM'));
		$poll_option_id = sed_import('poll_option_id', 'P', 'ARR');
		$poll_multiple = sed_import('poll_multiple', 'P', 'BOL');
		$option_count = (count($poll_option_id) ? count($poll_option_id) : 0);
		$poll_option_text = sed_import('poll_option', 'P', 'ARR');

		$option_count = (count($poll_option_id) ? count($poll_option_id) : 0);
		$counter=0;
		$error_string .= (mb_strlen($poll_text)<4) ? $L['polls_error_title']."<br/>" : '';

		for($i = 0; $i<$option_count; $i++)
		{   $poll_option_text[$i]=trim($poll_option_text[$i]);
		if ($poll_option_text[$i] != "")
		{
			for ($j = $i+1; $j<$option_count; $j++)
			{
				$poll_option_text[$j]=trim($poll_option_text[$j]);
				if($poll_option_text[$i] == $poll_option_text[$j] && ($poll_option_id[$j]=='new' || $cfg['del_dup_options']) ){
					$poll_option_text[$j]=""; }
			}
			$counter++;
			if (MQGPC) {$poll_option_text[$i]=stripcslashes($poll_option_text[$i]);}
		}
		}
		$error_string .= ($counter<2) ? $L['polls_error_count']."<br/>" : '';
	}
}


/* ------------------ */

function sed_poll_save($type='index', $state='0')
{

	global $cfg, $L, $sys, $db_polls, $db_polls_options;
	global $error_string;
	global $poll_id, $poll_text, $poll_option_id, $poll_multiple, $poll_option_text;

	if (!empty($poll_id) && empty($error_string))
	{
		$option_count = (count($poll_option_id) ? count($poll_option_id) : 0);
		if ($poll_id=='new') {
			$sql = sed_sql_query("INSERT INTO $db_polls (poll_type, poll_state, poll_creationdate, poll_text, poll_multiple) VALUES ('".$type."', ".(int)$state.", ".(int)$sys['now_offset'].", '".sed_sql_prep($poll_text)."', '".(int)$poll_multiple."')");
			$newpoll_id = sed_sql_insertid(); }
			else {
				$sql = sed_sql_query("UPDATE $db_polls SET poll_text='".sed_sql_prep($poll_text)."', poll_multiple='".(int)$poll_multiple."' WHERE poll_id='$poll_id'");
				$newpoll_id = $poll_id; }
				// Dinamic adding polloptions
				for($count = 0; $count < $option_count; $count++) {
					if ($poll_option_id[($count)]!="new")
					{
						if ($poll_option_text[($count)]=="")// drop
						{ $sql2 = sed_sql_query("DELETE FROM $db_polls_options WHERE po_id='".(int)$poll_option_id[($count)]."'"); }
						else // edit
						{ $sql2 = sed_sql_query("UPDATE $db_polls_options SET po_text='".sed_sql_prep($poll_option_text[($count)])."' WHERE po_id='".(int)$poll_option_id[($count)]."'"); }
					}
					else //insert
					{
						if ($poll_option_text[($count)]!="")// insert not empty
						{ $sql2 = sed_sql_query("INSERT into $db_polls_options ( po_pollid, po_text, po_count) VALUES ('$newpoll_id', '".sed_sql_prep($poll_option_text[($count)])."', '0')"); }
					}
				}
				return($newpoll_id);
	}
	return 0;
}

/* ------------------ */


function sed_poll_vote()
{
	global $cfg, $L, $db_polls, $db_polls_options, $db_polls_voters, $usr;
	global $error_string;
	global $vote;

	$vote = sed_import('vote','P','ARR');
	$id= sed_import('poll_id','P','INT');

	if (!empty($vote))
	{
	$sql = sed_sql_query("SELECT * FROM $db_polls WHERE poll_id='$id'");

	if ($row = sed_sql_fetcharray($sql))
	{
		if ($cfg['ip_id_polls']=='id' && $usr['id']>0)
		{
			$sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$id' AND pv_userid='".$usr['id']."' LIMIT 1");
			$alreadyvoted = (sed_sql_numrows($sql2)==1) ? 1 : 0;
		}
		elseif($cfg['ip_id_polls']=='ip')
		{
			if ($usr['id']>0)
			{$sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$id' AND (pv_userid='".$usr['id']."' OR pv_userip='".$usr['ip']."') LIMIT 1");}
			else
			{$sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$id' AND pv_userip='".$usr['ip']."' LIMIT 1");}

			$alreadyvoted = (sed_sql_numrows($sql2)==1) ? 1 : 0;
		}
		else
		{
			$alreadyvoted = 0;
		}

		if (!empty($vote) && $alreadyvoted!=1)
		{
			for($i = 0; $i<count($vote); $i++)
			{$sql2 = sed_sql_query("UPDATE $db_polls_options SET po_count=po_count+1 WHERE po_pollid='$id' AND po_id='".(int)$vote[$i]."'");}
			if (sed_sql_affectedrows()>0)
			{
				$sql2 = sed_sql_query("INSERT INTO $db_polls_voters (pv_pollid, pv_userid, pv_userip) VALUES (".(int)$id.", ".(int)$usr['id'].", '".$usr['ip']."')");
			}
		}
	}
	else
	{ $error_string .= $L['wrongURL']; }
	}
}

/*---------------*/

function sed_poll_form($id, $formlink='', $skin='')
{
	global $cfg, $L, $db_polls, $db_polls_options, $db_polls_voters, $usr;
	global $error_string;
	$canvote = true;

	$sql = sed_sql_query("SELECT * FROM $db_polls WHERE poll_id='$id'");

	if ($row = sed_sql_fetcharray($sql))
	{
		if ($cfg['ip_id_polls']=='id' && $usr['id']>0)
		{
			$sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$id' AND pv_userid='".$usr['id']."' LIMIT 1");
			$alreadyvoted = (sed_sql_numrows($sql2)==1) ? 1 : 0;
		}
		elseif($cfg['ip_id_polls']=='ip')
		{
			if ($usr['id']>0)
			{$sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$id' AND (pv_userid='".$usr['id']."' OR pv_userip='".$usr['ip']."') LIMIT 1");}
			else
			{$sql2 = sed_sql_query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid='$id' AND pv_userip='".$usr['ip']."' LIMIT 1");}

			$alreadyvoted = (sed_sql_numrows($sql2)==1) ? 1 : 0;
		}
		else
		{
			$alreadyvoted = 0;
			$canvote	=false;
		}

		$sql2 = sed_sql_query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$id'");
		$totalvotes = sed_sql_result($sql2,0,"SUM(po_count)");

		$sql1 = sed_sql_query("SELECT po_id,po_text,po_count FROM $db_polls_options WHERE po_pollid='$id' ORDER by po_id ASC");
		$error_string .= (sed_sql_numrows($sql1)<1) ? $L['wrongURL'] : '';
	}
	else
	{ $error_string .= $L['wrongURL']; }

$skininput=$skin;

if (empty($skin)) {$skin=sed_skinfile('polls');}
else			  {$skin=sed_skinfile($skin, true);}

$poll_form = new XTemplate($skin);
$poll_block = (!$alreadyvoted) ? "POLL_VIEW" : "POLL_VIEW_VOTED";
if (!$canvote) $poll_block = "POLL_VIEW_DISABLED";
	while ($row1 = sed_sql_fetcharray($sql1))
	{
		$po_id = $row1['po_id'];
		$po_count = $row1['po_count'];
		$percent = @round(100 * ($po_count / $totalvotes),1);

		$input_type=$row['poll_multiple'] ? "checkbox" : "radio";
		$polloptions_input = ($alreadyvoted || !$canvote) ? "" : "<input type='".$input_type."' name='vote[]' value='".$po_id."' />&nbsp;";
		$polloptions = sed_parse(sed_cc($row1['po_text']), 1, 1, 1);

		$poll_form->assign(array(
		"POLL_OPTIONS" => $polloptions,
		"POLL_PER" => $percent,
		"POLL_COUNT" => $po_count,
		"POLL_INPUT" => $polloptions_input,
		));

	$poll_form->parse($poll_block.".POLLTABLE");

	}

	$polltext=sed_parse(sed_cc($row['poll_text']), 1, 1, 1);
	$pollbutton=(!$alreadyvoted || $canvote) ? "<input type=\"hidden\" name=\"poll_id\" value=\"$id\" /><input type=\"hidden\" name=\"poll_skin\" value=\"$skininput\" /><input type=\"submit\" class=\"submit\" value=\"".$L['polls_Vote']."\" />" :"";
	$polldate=date($cfg['dateformat'], $row['poll_creationdate'] + $usr['timezone'] * 3600);
 if (empty($formlink)) {$formlink=sed_url('polls', "id=".$id);}


	$pollformbegin = "<div id='poll_".$id."'><form action=\"".$formlink."\" method=\"post\" id='poll_form_".$id."' onsubmit=\"return ajaxSend({method: 'POST', formId: 'poll_form_".$id."', url: '".sed_url('polls', 'mode=ajax')."', divId: 'poll_".$id."', errMsg: '".$L['ajaxSenderror']."'});\">";
	$pollformend .= "</form></div>";

	$poll_form->assign(array(
		"POLL_VOTERS" => $totalvotes,
		"POLL_SINCE" => $polldate,
		"POLL_TITLE" => $polltext,
		"POLL_FORM_BEGIN" => $pollformbegin,
		"POLL_FORM_END" => $pollformend,
		"POLL_FORM_BUTTON" => $pollbutton,
		"POLL_RESULTS" => $result,
	));

	$poll_form->parse($poll_block);
$pollform = array($polltext, $poll_form -> text($poll_block));


	return($pollform);
}

?>