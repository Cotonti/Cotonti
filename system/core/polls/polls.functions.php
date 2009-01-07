<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=system/core/polls/polls.functions.php
Version=r247
Updated=2007-mar-03
Type=Core
Author=esclkm
Description=Functions
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

function sed_create_poll($id, $multiple=1)
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
				<input  class='tbox' type='text' name='poll_option[]' size='40' value=\"".sed_cc($poll_option_text[$i])."\" maxlength='128' /> <input class=\"delbutton\" name=\"addoption\" value=\"x\" onclick=\"return removeAns(".$counter.")\" type=\"button\" /></div>"; 
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
				<input  class='tbox' type='text' name='poll_option[]' size='40' value=\"".sed_cc($row1['po_text'])."\" maxlength='128' /> <input class=\"delbutton\" name=\"addoption\" value=\"x\" onclick=\"return removeAns(".$counter.")\" type=\"button\" />
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
				<input class='tbox' type='text' name='poll_option[]' size='40' value=\"\" maxlength='128' /> <input class=\"delbutton\" name=\"addoption\" value=\"x\" onclick=\"return removeAns(1)\" type=\"button\" />
				</div>";
	$counter++;
}
if ($counter==1)
{
	$poll_options_x.="<div id ='ans_2'><input type=\"hidden\" name=\"poll_option_id[]\" value=\"new\" />
				<input class='tbox' type='text' name='poll_option[]' size='40' value=\"\" maxlength='128' /> <input class=\"delbutton\" name=\"addoption\" value=\"x\" onclick=\"return removeAns(1)\" type=\"button\" />
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
	<input class=\"delbutton\" name=\"addoption\" value=\"".$L['Add']."\" onclick=\"return addAns()\" type=\"button\" />
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

function sed_save_poll_check_errors()
{
global $cfg, $L;
global $error_string;
global $poll_id, $poll_text, $poll_option_id, $poll_multiple, $poll_option_text;

if (!empty($poll_id))
{
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
		}
	}
	$error_string .= ($counter<2) ? $L['polls_error_count']."<br/>" : '';
}
}


/* ------------------ */

function sed_save_poll()
{

global $cfg, $L, $sys, $db_polls, $db_polls_options;
global $error_string;
global $poll_id, $poll_text, $poll_option_id, $poll_multiple, $poll_option_text;

if (!empty($poll_id) && empty($error_string))
{
	$option_count = (count($poll_option_id) ? count($poll_option_id) : 0);
		if ($poll_id=='new') {
			$sql = sed_sql_query("INSERT INTO $db_polls (poll_state, poll_creationdate, poll_text, poll_multiple) VALUES (0, ".(int)$sys['now_offset'].", '".sed_sql_prep($poll_text)."', '".$poll_multiple."')");
			$newpoll_id = sed_sql_insertid(); }
		else {
			$sql = sed_sql_query("UPDATE $db_polls SET poll_text='".sed_sql_prep($poll_text)."', poll_multiple='".$poll_multiple."' WHERE poll_id='$poll_id'");
			$newpoll_id = $poll_id; }
		// Dinamic adding polloptions
		for($count = 0; $count < $option_count; $count++) {
			if ($poll_option_id[($count)]!="new")
			{
				if ($poll_option_text[($count)]=="")// drop
				{ $sql2 = sed_sql_query("DELETE FROM $db_polls_options WHERE po_id='".$poll_option_id[($count)]."'"); }
				else // edit
				{ $sql2 = sed_sql_query("UPDATE $db_polls_options SET po_text='".$poll_option_text[($count)]."' WHERE po_id='".$poll_option_id[($count)]."'"); }
			}
			else //insert
			{ 
				if ($poll_option_text[($count)]!="")// insert not empty
				{ $sql2 = sed_sql_query("INSERT into $db_polls_options ( po_pollid, po_text, po_count) VALUES ('$newpoll_id', '".$poll_option_text[($count)]."', '0')"); }
			}
		}
		return($newpoll_id);
}
	return('');
}

/* ------------------ */



?>
