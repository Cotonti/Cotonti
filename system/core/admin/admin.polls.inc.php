<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.polls.inc.php
Version=101
Updated=2006-mar-15
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('polls', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=polls'), $L['Polls']);
$adminhelp = $L['adm_help_polls'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$error_string='';

$adminmain .= '<script type="text/javascript">
function duplicateHTML(copy,paste,delend,countopt){
		if(document.getElementById(copy)){
          if(!countopt[1] || countopt[1]>countopt[0])
          {
			countopt[0]++;
			var type = document.getElementById(copy).nodeName; // get the tag name of the source copy.

			var but = document.createElement("input");
			var br = document.createElement("br");

			but.type = "button";
			but.value = "x";
			but.className = "delbutton";
			but.onclick = function(){ this.parentNode.parentNode.removeChild(this.parentNode); countopt[0]--;};

			var destination = document.getElementById(paste);
			var source      = document.getElementById(copy).cloneNode(true);

			var newentry = document.createElement(type);
			if(!delend)
			{ newentry.appendChild(but); }
			newentry.appendChild(source);
			newentry.value="";
			if(delend)
			{ newentry.appendChild(but); }
			//newentry.appendChild(but);
			newentry.appendChild(br);
			destination.appendChild(newentry);
		  }
		}
}
</script>';

$adminmain .= "<ul><li><a href=\"".sed_url('admin', "m=config&n=edit&o=core&p=polls")."\">".$L['Configuration']." : <img src=\"images/admin/config.gif\" alt=\"\" /></a></li></ul>";

if ($a=='delete')
{
	sed_check_xg();
	$id2 = "v".$id;

	$sql = sed_sql_query("DELETE FROM $db_polls WHERE poll_id='$id'");
	$num = sed_sql_affectedrows();
	$sql = sed_sql_query("DELETE FROM $db_polls_options WHERE po_pollid='$id'");
	$num = $num + sed_sql_affectedrows();
	$sql = sed_sql_query("DELETE FROM $db_polls_voters WHERE pv_pollid='$id'");
	$num = $num + sed_sql_affectedrows();
	$sql = sed_sql_query("DELETE FROM $db_com WHERE com_code='$id2'");
	$num = $num + sed_sql_affectedrows();
	header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=916&rc=102&num=".$num, '', true));
	exit;
}

elseif ($a=='reset')
{
	sed_check_xg();
	$sql = sed_sql_query("DELETE FROM $db_polls_voters WHERE pv_pollid='$id'");
	$num = sed_sql_affectedrows();
	$sql = sed_sql_query("UPDATE $db_polls_options SET po_count=0 WHERE po_pollid='$id'");
	$num = $num + sed_sql_affectedrows();
	header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=916&rc=102&num=".$num, '', true));
	exit;
}

if ($a=='bump')
{
	sed_check_xg();
	$sql = sed_sql_query("UPDATE $db_polls SET poll_creationdate='".$sys['now_offset']."' WHERE poll_id='$id'");
	header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=916&rc=102&num=1", '', true));
	exit;
}
$poll_id = sed_import('poll_id','P','TXT');
if (!empty($poll_id))
{
	$ntext = sed_import('ntext','P','HTM');
	$noption_id = sed_import('poll_option_id', 'P', 'ARR');
	$option_count = (count($noption_id) ? count($noption_id) : 0);
	$noption_text = sed_import('poll_option', 'P', 'ARR');
	$counter=0;
	$error_string .= (mb_strlen($ntext)<4) ? $L['adm_polls_error_title']."<br/>" : '';

	for($i = 0; $i<$option_count; $i++)
	{
		$noption_text[$i]=trim($noption_text[$i]);
		if ($noption_text[$i] != "")
		{
			for ($j = $i+1; $j<$option_count; $j++)
			{
				$noption_text[$j]=trim($noption_text[$j]);
				if($noption_text[$i] == $noption_text[$j]){
					if ($noption_id[$j]=='new')		{ $noption_text[$j]=""; }
					if ($cfg['del_dup_options'])   	{ $noption_text[$j]=""; }
				}
			}
			$counter++;
		}
	}
	$error_string .= ($counter<2) ? $L['adm_polls_error_count'] : '';
	if (empty($error_string)){
		if ($poll_id=='new')
		{
			$sql = sed_sql_query("INSERT INTO $db_polls (poll_state, poll_creationdate, poll_text) VALUES (0, ".(int)$sys['now_offset'].", '".sed_sql_prep($ntext)."')");
			$newpoll_id = sed_sql_insertid();
		}
		else
		{
			$sql = sed_sql_query("UPDATE $db_polls SET poll_text='".sed_sql_prep($ntext)."' WHERE poll_id='$poll_id'");
			$newpoll_id = $poll_id;
		}
		// Dinamic adding polloptions
		for($count = 0; $count < $option_count; $count++) {
			//poll_option_id[] poll_option[] fac_extra1 fac_extra2
			if ($noption_id[($count)]!="new")
			{
				if ($noption_text[($count)]=="")// drop
				{
					$sql2 = sed_sql_query("DELETE FROM $db_polls_options WHERE po_id='".$noption_id[($count)]."'");
				}
				else // edit
				{
					$sql2 = sed_sql_query("UPDATE $db_polls_options SET po_text='".$noption_text[($count)]."' WHERE po_id='".$noption_id[($count)]."'");
				}
			}
			else //insert
			{
				if ($noption_text[($count)]!="")// insert not empty
				{
					$sql2 = sed_sql_query("INSERT into $db_polls_options ( po_pollid, po_text, po_count) VALUES ('$newpoll_id', '".$noption_text[($count)]."', '0')");
				}
			}
		}

		if($poll_id=='new'){$adminmain .= "<h4>".$L['adm_polls_created']."</h4>";}
		else {$adminmain .= "<h4>".$L['adm_polls_updated']."</h4>";}
	}
	else
	{$adminmain .="<div class=\"error\">".$error_string."</div>";}
}

$totalitems = sed_sql_rowcount($db_polls);
$pagnav = sed_pagination(sed_url('admin','m=polls'), $d, $totalitems, $cfg['maxrowsperpage']);
list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=polls'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);

$sql = sed_sql_query("SELECT p.*, t.ft_id FROM $db_polls AS p
LEFT JOIN $db_forum_topics AS t ON t.ft_poll = p.poll_id
WHERE 1 ORDER BY p.poll_type ASC, p.poll_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);

$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
$adminmain .= "<div class=\"pagnav\">".$pagination_prev." ".$pagnav." ".$pagination_next."</div>";
$adminmain .= "<table class=\"cells\">";

$ii = 0;
$prev = -1;

while ($row = sed_sql_fetcharray($sql))
{
	$id = $row['poll_id'];
	$type = $row['poll_type'];

	if ($type==0 && $prev==-1)
	{
		$prev = 0;
		$adminmain .= "<tr><td colspan=\"8\">".$L['adm_polls_indexpolls']."</td></tr>";
		$adminmain .= "<tr><td class=\"coltop\" style=\"width:128px;\">".$L['Date']."</td>";
		$adminmain .= "<td class=\"coltop\">".$L['Poll']." ".$L['adm_clicktoedit']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Votes']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Delete']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Reset']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Bump']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Open']."</td></tr>";
	}

	if ($type==1 && $prev==0)
	{
		$prev = 1;
		$adminmain .= "<tr><td colspan=\"8\">".$L['adm_polls_forumpolls']."</td></tr>";
		$adminmain .= "<tr><td class=\"coltop\" style=\"width:128px;\">".$L['Date']."</td>";
		$adminmain .= "<td class=\"coltop\">".$L['Topic']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Votes']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Delete']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Reset']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:40px;\">".$L['Bump']."</td>";
		$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Open']."</td></tr>";
	}

	$sql2 = sed_sql_query("SELECT SUM(po_count) FROM $db_polls_options WHERE po_pollid='$id'");
	$totalvotes = sed_sql_result($sql2,0,"SUM(po_count)");
	$adminmain .= "<tr><td style=\"text-align:center;\">".date($cfg['formatyearmonthday'], $row['poll_creationdate'])."</td>";


	$adminmain .= "<td><a href=\"".sed_url('admin', "m=polls&n=options&id=".$row['poll_id'])."\">".sed_cc($row['poll_text'])."</a></td>";
	$adminmain .= "<td style=\"text-align:center;\">".$totalvotes."</td>";

	$adminmain .= "<td style=\"text-align:center;\">[<a href=\"".sed_url('admin', "m=polls&a=delete&id=".$id."&".sed_xg())."\">x</a>]</td>";
	$adminmain .= "<td style=\"text-align:center;\">[<a href=\"".sed_url('admin', "m=polls&a=reset&id=".$id."&".sed_xg())."\">R</a>]</td>";
	$adminmain .= "<td style=\"text-align:center;\">[<a href=\"".sed_url('admin', "m=polls&a=bump&id=".$id."&".sed_xg())."\">B</a>]</td>";
	$adminmain .= "<td style=\"text-align:center;\">";

	if ($type==0)
	{ $adminmain .= "<a href=\"".sed_url('polls', "id=".$row['poll_id'])."\"><img src=\"images/admin/jumpto.gif\" alt=\"\" /></a>"; }
	else
	{ $adminmain .= "<a href=\"".sed_url('forums', "m=posts&q=".$row['ft_id'])."\"><img src=\"images/admin/jumpto.gif\" alt=\"\" /></a>"; }

	$adminmain .= "</td></tr>";
	$ii++;
}
$adminmain .= "<tr><td colspan=\"8\">".$L['Total']." : ".$totalitems.", ".$L['adm_polls_on_page'].": ".$ii."</td></tr></table>";

if ($n=='options')
{
	$poll_id = sed_import('id','G','TXT');
	$sql = sed_sql_query("SELECT * FROM $db_polls WHERE poll_id='$poll_id' ");
	$sql1 = sed_sql_query("SELECT * FROM $db_polls_options WHERE po_pollid='$poll_id' ORDER by po_id ASC");
	$row = sed_sql_fetcharray($sql);
	$adminpath[] = array (sed_url('admin', 'm=polls&n=options&id=$poll_id'), $L['Options']." (#$id)");
	$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
	$ntext=sed_cc($row["poll_text"]);
	$send_button=$L['Update'];
	$date=date($cfg['dateformat'], $row["poll_creationdate"])." GMT";
	$counter=1;
	while ($row1 = sed_sql_fetcharray($sql1))
	{
		$adminmain2 .="<span class='hidebox'><input type=\"hidden\" name=\"poll_option_id[]\" value=\"".$row1['po_id']."\" />
				<input  class='tbox' type='text' name='poll_option[]' size='40' value=\"".sed_cc($row1['po_text'])."\" maxlength='128' />
				</span><br />";
		$counter++;
	}
}
elseif(!empty($error_string))
{
	if ($poll_id!='new')
	{$adminpath[] = array (sed_url('admin', 'm=polls&n=options&id=$poll_id'), $L['Options']." (#$id)");
	$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
	$send_button=$L['Update'];}
	else
	{$adminmain .= "<h4>".$L['addnewentry']." :</h4>";
	$send_button=$L['Create'];}
	$counter=1;
	$date="";
	for($count = 0; $count < $option_count; $count++)
	{
		if ($noption_text[($count)]!="" || ($noption_text[($count)]=="" && $noption_id[($count)]!='new'))
		{
			$adminmain2 .="<span class='hidebox'><input type=\"hidden\" name=\"poll_option_id[]\" value=\"".$noption_id[($count)]."\" />
				<input  class='tbox' type='text' name='poll_option[]' size='40' value=\"".sed_cc($noption_text[($count)])."\" maxlength='128' />
				</span><br />";
			$counter++;
		}
	}

}
else
{
	$poll_id='new';
	$ntext='';
	$adminmain .= "<h4>".$L['addnewentry']." :</h4>";
	$date='';
	$adminmain2='';
	$send_button=$L['Create'];
	$counter=1;
}


$adminmain .= "<form id=\"addpoll\" action=\"".sed_url('admin', "m=polls")."\" method=\"post\">";
$adminmain .= "<table class=\"cells\">";
$adminmain .= "<tr><td>".$L['adm_polls_polltopic']."</td><td><input type=\"text\" class=\"text\" name=\"ntext\" value=\"".$ntext."\" size=\"64\" maxlength=\"255\" /><input type=\"hidden\" name=\"poll_id\" value=\"".$poll_id."\" /></td></tr>";
$adminmain .= "<tr><td>".$L['Date']." : </td><td>".$date."</td></tr>";
$adminmain .= "<tr><td>".$L['Options']."</td><td>";
$adminmain .= $adminmain2;

if($counter<$cfg['max_options_polls'])
{
	$adminmain .="<div id='poptions'>";
	$adminmain .="<span id='newpolloption' class='hidebox'><input type=\"hidden\" name=\"poll_option_id[]\" value=\"new\" /><input  class='tbox' type='text' name='poll_option[]' size='40' value=\"\" maxlength='128' /> </span><br />";
	$adminmain .="<script>dupCounter = 6;
	cpollopt = new Array(".$counter.",".$cfg['max_options_polls'].");</script>";
	$adminmain .="</div><input class='delbutton' type='button' name='addoption' value='".$L['Add']."' onclick=\"duplicateHTML('newpolloption','poptions', true,  cpollopt)\" /><br />";
}
$adminmain .= "</td></tr>";
$adminmain .= "<tr><td colspan=\"2\"><input type=\"submit\" class=\"submit\" value=\"".$send_button."\" /></td></tr></table></form>";

?>