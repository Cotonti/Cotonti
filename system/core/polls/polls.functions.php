<?php

/**
 * Polls functions.
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

function sed_poll_edit_form($id, $t, $block='', $type='')
{
    global $cfg, $L, $db_polls, $db_polls_options;
    global $error_string;
    $poll_options ='';

    $mask_edit_form ="<div %1\$s>
        <input type='hidden' name='poll_option_id[]' value='%2\$s' />
        <input  class='tbox' type='text' name='poll_option[]' size='40' value='%3\$s' maxlength='128' />
        <input  name='addoption' value='x' onclick='removeAns(this)' type='button' class='deloption' style='display:none;' /></div>";


    if(!empty($error_string))
    {
        global $poll_id, $poll_option_text, $poll_option_id, $poll_multiple, $poll_state, $poll_text;

        $counter=0;
        $date="";
        $id=$poll_id;
        $poll_multiple=($poll_multiple) ? "checked='checked'":"";
        $poll_state=($poll_state) ? "checked='checked'":"";
        $option_count = (count($poll_option_id) ? count($poll_option_id) : 0);
        for($i = 0; $i < $option_count; $i++)
        {
            if ($poll_option_text[$i]!="" || ($poll_option_text[$i]=="" && $poll_option_id[$i]!='new'))
            {
                $counter++;
                $poll_options_x .=sprintf($mask_edit_form, '', $poll_option_id[$i], htmlspecialchars($poll_option_text[$i]));
            }
        }

    }
    elseif ($id!='new')
    {
        if(!$type)
        {
            $sql = sed_sql_query("SELECT * FROM $db_polls WHERE poll_id='$id' ");
        }
        else
        {
            $sql = sed_sql_query("SELECT * FROM $db_polls WHERE poll_type='$type' AND poll_code='$id' LIMIT 1");
        }

        if ($row = sed_sql_fetcharray($sql))
        {
            $id=$row["poll_id"];
            $sql1 = sed_sql_query("SELECT * FROM $db_polls_options WHERE po_pollid='$id' ORDER by po_id ASC");
            $poll_text=htmlspecialchars($row["poll_text"]);
            $poll_multiple=($row["poll_multiple"]) ? "checked='checked'":"";
            $poll_state=($row["poll_state"]) ? "checked='checked'":"";
            $date=date($cfg['dateformat'], $row["poll_creationdate"])." GMT";
            $counter=0;
            while ($row1 = sed_sql_fetcharray($sql1))
            {
                $counter++;
                $poll_options_x .=sprintf($mask_edit_form, '', $row1['po_id'], htmlspecialchars($row1['po_text']));
            }
        }
        else
        {
            return false;
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

        $poll_options_x=sprintf($mask_edit_form, '', 'new', '');
        $counter++;
    }
    if ($counter==1)
    {
        $poll_options_x.=sprintf($mask_edit_form, '', 'new', '');;
        $counter++;
    }
    $poll_date=$date.'<input type="hidden" name="poll_date" value="'.$date.'" />';
    if($cfg['jquery'])
    {
        // Render rules table
        $poll_options .= <<<HTM
<script type="text/javascript">
//<![CDATA[

var ansCount = $counter;
var ansMax = {$cfg['max_options_polls']};
function removeAns(object)
{
    $(object).parent().children('[name="poll_option[]"]').attr('value', '');
    if (ansCount>2)
    { 	ansCount--;
    if ($(object).parent().children('[name="poll_option_id[]"]').value !="new")
    { $(object).parent().hide(); }
    else { $(object).parent().remove(); }
    }

    return false;
}

$(document).ready(function(){
    $("#addoption").click(function () {
        if (ansCount<ansMax)
        {
            $('#newanswer').clone().attr("id", '').insertBefore('#newanswer').show();
            ansCount++;
        }
        return false;
    });
    $('#addoption').show();
    $("#newanswer").hide();
    $('.deloption').show();
});
//]]>
</script>
HTM;
    }

    $poll_options .= "<input type='hidden' name='poll_id' value='".$id."' />";
    $poll_options .= $poll_options_x;
    if ($counter>=$cfg['max_options_polls'])
    {
        $slylehide=" style='display:none;'";
    }
    $poll_options .= sprintf($mask_edit_form, 'id="newanswer"'.$slylehide, 'new', '');
    $poll_options .= "
    <input id='addoption' name='addoption' value='".$L['Add']."' type='button' style='display:none;' />";

    $poll_multiple = "<input name='poll_multiple' type='checkbox' value='1' $poll_multiple />";

    $poll_close = "<input name='poll_state' type='checkbox' value='1' $poll_state />";
    $poll_reset = "<input name='poll_reset' type='checkbox' value='1' />";
    $pol_delete = "<input name='poll_delete' type='checkbox' value='1' />";

    if ($id!='new')
    {
        $t->assign(array(
        "EDIT_POLL_CLOSE" => $poll_close,
        "EDIT_POLL_RESET" => $poll_reset,
        "EDIT_POLL_DELETE" => $pol_delete,
            ));

        $t->parse($block.".EDIT");
    }

    $t->assign(array(
        "EDIT_POLL_TEXT" => $poll_text,
        "EDIT_POLL_DATE" => $poll_date,
        "EDIT_POLL_OPTIONS" => $poll_options,
        "EDIT_POLL_MULTIPLE" => $poll_multiple
        ));

    return true;
}

/* ------------------ */

function sed_poll_check()
{
    global $cfg, $L;
    global $error_string;
    global $poll_id, $poll_text, $poll_option_id, $poll_multiple, $poll_state, $poll_option_text;
    $poll_id = sed_import('poll_id','P','TXT');
    $poll_delete = sed_import('poll_delete', 'P', 'BOL');

    if ($poll_delete && !empty($poll_id))
    {
        sed_poll_delete($poll_id);
        $poll_id = "";
    }
    if (!empty($poll_id))
    {
        $poll_reset = sed_import('poll_reset', 'P', 'BOL');
        if ($poll_reset)
        {sed_poll_reset($id);}
        $poll_text = trim(sed_import('poll_text','P','HTM'));
        $poll_option_id = sed_import('poll_option_id', 'P', 'ARR');
        $poll_multiple = sed_import('poll_multiple', 'P', 'BOL');
        $poll_state = sed_import('poll_state', 'P', 'BOL');
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
                if (MQGPC) $poll_option_text[$i] = stripcslashes($poll_option_text[$i]);
            }
        }
        $error_string .= ($counter<2) ? $L['polls_error_count']."<br/>" : '';
    }
}


/* ------------------ */

function sed_poll_save($type='index', $code='')
{

    global $cfg, $L, $sys, $db_polls, $db_polls_options;
    global $error_string;
    global $poll_id, $poll_text, $poll_option_id, $poll_multiple, $poll_state, $poll_option_text;

    if (!empty($poll_id) && empty($error_string))
    {
        $option_count = (count($poll_option_id) ? count($poll_option_id) : 0);
        if ($poll_id=='new') {
            $sql = sed_sql_query("INSERT INTO $db_polls (poll_type, poll_state, poll_creationdate, poll_text, poll_multiple, poll_code) VALUES ('".sed_sql_prep($type)."', ".(int)$poll_state.", ".(int)$sys['now_offset'].", '".sed_sql_prep($poll_text)."', '".(int)$poll_multiple."', '".(int)$code."')");
            $newpoll_id = sed_sql_insertid(); }
        else {
            // TODO: CHECK if changed
            $sql = sed_sql_query("UPDATE $db_polls SET  poll_state='".(int)$poll_state."', poll_text='".sed_sql_prep($poll_text)."', poll_multiple='".(int)$poll_multiple."' WHERE poll_id='$poll_id'");
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
    return(false);
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
    }
}

/*---------------*/

function sed_poll_form($id, $formlink='', $skin='', $type='')
{
    global $cfg, $L, $db_polls, $db_polls_options, $db_polls_voters, $usr;
    global $error_string;
    $canvote = true;

    if(!is_array($id))
    {
        $where=(!$type) ? "poll_id='$id'" : "poll_type='$type' AND poll_code='$id'" ;
        $sql = sed_sql_query("SELECT * FROM $db_polls WHERE $where LIMIT 1");
        if (!$row = sed_sql_fetcharray($sql))
        {
            return false;
        }
    }
    else
    {
        $row=$id;
    }
    $id=$row['poll_id'];
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
    if (sed_sql_numrows($sql1)<1)
    {
        return false;
    }

    $skininput=$skin;

    if (empty($skin)) {$skin=sed_skinfile('polls');}
    else			  {$skin=sed_skinfile($skin, true);}

    $poll_form = new XTemplate($skin);

    if  ($alreadyvoted) $poll_block = "POLL_VIEW_VOTED";
    elseif (!$canvote) $poll_block = "POLL_VIEW_DISABLED";
    elseif ($row['poll_state']) $poll_block = "POLL_VIEW_LOCKED";
    else $poll_block = "POLL_VIEW";

    while ($row1 = sed_sql_fetcharray($sql1))
    {
        $po_id = $row1['po_id'];
        $po_count = $row1['po_count'];
        $percent = @round(100 * ($po_count / $totalvotes),1);

        $input_type=$row['poll_multiple'] ? "checkbox" : "radio";
        $polloptions_input = ($alreadyvoted || !$canvote) ? "" : "<input type='".$input_type."' name='vote[]' value='".$po_id."' />&nbsp;";
        $polloptions = sed_parse(htmlspecialchars($row1['po_text']), 1, 1, 1);

        $poll_form->assign(array(
        "POLL_OPTIONS" => $polloptions,
        "POLL_PER" => $percent,
        "POLL_COUNT" => $po_count,
        "POLL_INPUT" => $polloptions_input,
            ));

        $poll_form->parse($poll_block.".POLLTABLE");

    }

    $polltext=sed_parse(htmlspecialchars($row['poll_text']), 1, 1, 1);
    $pollbutton=(!$alreadyvoted || $canvote) ? "<input type=\"hidden\" name=\"poll_id\" value=\"$id\" /><input type=\"hidden\" name=\"poll_skin\" value=\"$skininput\" /><input type=\"submit\" class=\"submit\" value=\"".$L['polls_Vote']."\" />" :"";
    $polldate=date($cfg['dateformat'], $row['poll_creationdate'] + $usr['timezone'] * 3600);
    $polldate_short=date($cfg['formatmonthday'], $row['poll_creationdate'] + $usr['timezone'] * 3600);
    if (empty($formlink)) {$formlink=sed_url('polls', "id=".$id);}

	if($cfg['jquery'] AND $cfg['turnajax'])
	{
		$onsubmit="onsubmit=\"return ajaxSend({method: 'POST', formId: 'poll_form_".$id."', url: '".sed_url('polls', 'mode=ajax')."', divId: 'poll_".$id."', errMsg: '".$L['ajaxSenderror']."'});\"";
	}

    $pollformbegin = "<div id='poll_".$id."'><form action=\"".$formlink."\" method=\"post\" id='poll_form_".$id."' ".$onsubmit.">";
    $pollformend .= "</form></div>";

    $poll_form->assign(array(
        "POLL_VOTERS" => $totalvotes,
        "POLL_SINCE" => $polldate,
        "POLL_SINCE_SHORT" => $polldate_short,
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

function sed_poll_delete($id, $type='')
{
    global $db_polls, $db_polls_options, $db_polls_voters, $db_com;
    if($type)
    {
        $sql = sed_sql_query("SELECT poll_id FROM $db_polls WHERE poll_type='$type' AND poll_code='$id' LIMIT 1");
        if ($row = sed_sql_fetcharray($sql))
        {
            $id=$row['poll_id'];
        }
        else $id=0;
    }
    if($id!=0)
    {
        $sql = sed_sql_query("DELETE FROM $db_polls WHERE poll_id=".$id);
        $sql = sed_sql_query("DELETE FROM $db_polls_options WHERE po_pollid=".$id);
        $sql = sed_sql_query("DELETE FROM $db_polls_voters WHERE pv_pollid=".$id);
        $id2 = "v".$poll_id;
        $sql = sed_sql_query("DELETE FROM $db_com WHERE com_code='$id2'");
    }
}

function sed_poll_lock($id, $state, $type='')
{
    global $db_polls;
    if($type)
    {
        $sql = sed_sql_query("SELECT poll_id, poll_state FROM $db_polls WHERE poll_type='$type' AND poll_code='$id' LIMIT 1");
        if ($row = sed_sql_fetcharray($sql))
        {
            $id=$row['poll_id'];
            $rstate = $row['poll_state'];
        }
        else $id=0;
    }
    if ($state=3)
    {
        if (!$type)
        {
            $sql = sed_sql_query("SELECT poll_state FROM $db_polls WHERE  poll_id='$id' LIMIT 1");
            if ($row = sed_sql_fetcharray($sql))
            {
                $rstate = $row['poll_state'];
            }
            else $id=0;
        }
        if ($rstate) $state = 0;
        else		 $state = 1;
    }
    if($id!=0)
    {
        $sql = sed_sql_query("UPDATE $db_polls SET poll_state='".(int)$state."' WHERE poll_id='$id'");
    }
}

function sed_poll_reset($id, $type='')
{
    global $db_polls, $db_polls_options, $db_polls_voters;
    if($type)
    {
        $sql = sed_sql_query("SELECT poll_id FROM $db_polls WHERE poll_type='$type' AND poll_code='$id' LIMIT 1");
        if ($row = sed_sql_fetcharray($sql))
        {
            $id=$row['poll_id'];
        }
        else $id=0;
    }
    if($id!=0)
    {
        $sql = sed_sql_query("DELETE FROM $db_polls_voters WHERE pv_pollid='$id'");
        $sql = sed_sql_query("UPDATE $db_polls_options SET po_count=0 WHERE po_pollid='$id'");
    }
}

function sed_poll_exists($id, $type='')
{
    global $db_polls;
    if(!$type)
    {
        $sql = sed_sql_query("SELECT COUNT(*)  FROM $db_polls WHERE poll_id='$id' ");
    }
    else
    {
        $sql = sed_sql_query("SELECT COUNT(*)  FROM $db_polls WHERE poll_type='$type' AND poll_code='$id' LIMIT 1");
    }
    return (sed_sql_result($sql, 0, "COUNT(*)"));
}

?>