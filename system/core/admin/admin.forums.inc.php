<?PHP
/**
 * Administration panel - Forums & categories
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$id = sed_import('id','G','INT');

$adminpath[] = array (sed_url('admin', 'm=forums'), $L['Forums']);

$adminmain .= "<ul><li><a href=\"".sed_url('admin', 'm=config&n=edit&o=core&p=forums')."\">".$L['Configuration']." : <img src=\"images/admin/config.gif\" alt=\"\" /></a></li><li>";
$adminmain .= sed_linkif(sed_url('admin', 'm=forums&s=structure'), $L['adm_forum_structure'], sed_auth('admin', 'a', 'A'));
$adminmain .= "</li></ul>";

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

if ($n=='edit')
{
	if ($a=='update')
	{
		$rstate = sed_import('rstate', 'P', 'BOL');
		$rtitle = sed_import('rtitle', 'P', 'TXT');
		$rdesc = sed_import('rdesc', 'P', 'TXT');
		$ricon = sed_import('ricon', 'P', 'TXT');
		$rautoprune = sed_import('rautoprune', 'P', 'INT');
		$rcat = sed_import('rcat', 'P', 'TXT');
		$rallowusertext = sed_import('rallowusertext', 'P', 'BOL');
		$rallowbbcodes = sed_import('rallowbbcodes', 'P', 'BOL');
		$rallowsmilies = sed_import('rallowsmilies', 'P', 'BOL');
		$rallowprvtopics = sed_import('rallowprvtopics', 'P', 'BOL');
		$rallowviewers = sed_import('rallowviewers', 'P', 'BOL');
		$rallowpolls = sed_import('rallowpolls', 'P', 'BOL');
		$rcountposts = sed_import('rcountposts', 'P', 'BOL');
		$rtitle = sed_sql_prep($rtitle);
		$rdesc = sed_sql_prep($rdesc);
		$rcat = sed_sql_prep($rcat);
		$rmaster = sed_import('rmaster', 'P', 'INT');
		$mastername = $rtitle;

		$sql = sed_sql_query("SELECT fs_id, fs_masterid, fs_order, fs_category FROM $db_forum_sections WHERE fs_id=$id ");
		sed_die(sed_sql_numrows($sql)==0);
		$row_cur = sed_sql_fetcharray($sql);

		if ($rmaster!='' && $row_cur['fs_masterid']!=$rmaster || empty($row_cur['fs_mastername']))
		{
			$sql1 = sed_sql_query("SELECT fs_title FROM $db_forum_sections WHERE fs_id='$rmaster' ");
			$row1 = sed_sql_fetcharray($sql1);

			$master = sed_sql_prep($row1['fs_title']);

			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_masterid='".$rmaster."', fs_mastername='".$master."' WHERE fs_id='".$id."' ");
		}

		if ($row_cur['fs_category']!=$rcat)
		{
			$sql = sed_sql_query("SELECT fs_order FROM $db_forum_sections WHERE fs_category='".$rcat."' ORDER BY fs_order DESC LIMIT 1");

			if (sed_sql_numrows($sql)>0)
			{
				$row_oth = sed_sql_fetcharray($sql);
				$rorder = $row_oth['fs_order']+1;
			}
			else
			{ $rorder = 100; }

			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_order=fs_order-1 WHERE fs_category='".$row_cur['fs_category']."' AND fs_order>".$row_cur['fs_order']);
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_order='$rorder' WHERE fs_id='$id'");
		}

		if (!empty($rtitle))
		{

			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_state='$rstate', fs_title='$rtitle', fs_desc='$rdesc', fs_category='$rcat' , fs_icon='$ricon', fs_autoprune='$rautoprune', fs_allowusertext='$rallowusertext', fs_allowbbcodes='$rallowbbcodes', fs_allowsmilies='$rallowsmilies', fs_allowprvtopics='$rallowprvtopics', fs_allowviewers='$rallowviewers', fs_allowpolls='$rallowpolls', fs_countposts='$rcountposts' WHERE fs_id='$id'");
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_mastername='".$mastername."' WHERE fs_masterid='$id' ");

		}

		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=forums&d='.$d, '', true));
		exit;
	}
	elseif ($a=='delete')
	{
		sed_check_xg();
		sed_auth_clear('all');
		$num = sed_forum_deletesection($id);
		$sql1 = sed_sql_query("UPDATE $db_forum_sections SET fs_masterid='0', fs_mastername='' WHERE fs_masterid='".$id."' ");
		$num = sed_sql_numrows($sql1);
		header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=916&rc=103&num=".$num, '', true));
		exit;
	}
	elseif ($a=='resync')
	{
		sed_check_xg();
		sed_forum_resync($id);
	}

	$sql = sed_sql_query("SELECT * FROM $db_forum_sections WHERE fs_id='$id'");
	sed_die(sed_sql_numrows($sql)==0);
	$row = sed_sql_fetcharray($sql);

	$fs_id = $row['fs_id'];
	$fs_masterid = $row['fs_masterid'];
	$fs_state = $row['fs_state'];
	$fs_order = $row['fs_order'];
	$fs_title = $row['fs_title'];
	$fs_desc = $row['fs_desc'];
	$fs_category = $row['fs_category'];
	$fs_icon = $row['fs_icon'];
	$fs_autoprune = $row['fs_autoprune'];
	$fs_allowusertext = $row['fs_allowusertext'];
	$fs_allowbbcodes = $row['fs_allowbbcodes'];
	$fs_allowsmilies = $row['fs_allowsmilies'];
	$fs_allowprvtopics = $row['fs_allowprvtopics'];
	$fs_allowviewers = $row['fs_allowviewers'];
	$fs_allowpolls = $row['fs_allowpolls'];
	$fs_countposts = $row['fs_countposts'];

	$form_state = ($fs_state) ? "<input type=\"radio\" class=\"radio\" name=\"rstate\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rstate\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rstate\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rstate\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_allowusertext = ($fs_allowusertext) ? "<input type=\"radio\" class=\"radio\" name=\"rallowusertext\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowusertext\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rallowusertext\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowusertext\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_allowbbcodes = ($fs_allowbbcodes) ? "<input type=\"radio\" class=\"radio\" name=\"rallowbbcodes\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowbbcodes\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rallowbbcodes\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowbbcodes\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_allowsmilies = ($fs_allowsmilies) ? "<input type=\"radio\" class=\"radio\" name=\"rallowsmilies\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowsmilies\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rallowsmilies\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowsmilies\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_allowprvtopics = ($fs_allowprvtopics) ? "<input type=\"radio\" class=\"radio\" name=\"rallowprvtopics\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowprvtopics\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rallowprvtopics\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowprvtopics\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_allowviewers = ($fs_allowviewers) ? "<input type=\"radio\" class=\"radio\" name=\"rallowviewers\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowviewers\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rallowprvtopics\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowprvtopics\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_allowpolls = ($fs_allowpolls) ? "<input type=\"radio\" class=\"radio\" name=\"rallowpolls\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowpolls\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rallowpolls\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rallowpolls\" value=\"0\" checked=\"checked\" />".$L['No'];

	$form_countposts = ($fs_countposts) ? "<input type=\"radio\" class=\"radio\" name=\"rcountposts\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rcountposts\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"rcountposts\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"rcountposts\" value=\"0\" checked=\"checked\" />".$L['No'];

	$adminpath[] = array (sed_url('admin', 'm=forums&n=edit&id='.$id), sed_cc($fs_title));

	$adminmain .= "<form id=\"updatesection\" action=\"".sed_url('admin', "m=forums&n=edit&a=update&id=".$fs_id)."\" method=\"post\">";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr><td>".$L['Section']." :</td><td>".$fs_id."</td></tr>";
	$sqlc = sed_sql_query("SELECT fs_id FROM $db_forum_sections WHERE fs_masterid='".$id."' ");
	if (!mysql_num_rows($sqlc))
	{
		$adminmain .= "<tr><td>".$L['adm_forums_master']." :</td><td><select name=\"rmaster\"><option value=\"0\">--</option>";
		$sqla = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category WHERE fs_id<>$id AND fs_masterid<1 AND fs_category='".$fs_category."' ORDER by fn_path ASC, fs_order ASC");
		while ($rowa = sed_sql_fetchassoc($sqla))
		{
			$cfs = sed_build_forums($rowa['fs_id'], $rowa['fs_title'], $rowa['fs_category'], FALSE);
			if ($fs_masterid==$rowa['fs_id'])
			{
				$adminmain .= "<option value=\"".$rowa['fs_id']."\" selected=\"selected\">".$cfs."</option>";
			}
			else
			{
				$adminmain .= "<option value=\"".$rowa['fs_id']."\">".$cfs."</option>";
			}
		}
		$adminmain .= "</select></td></tr>";
	}
	$adminmain .= "<tr><td>".$L['Category']." :</td><td>".sed_selectbox_forumcat($fs_category, 'rcat')."</td></tr>";
	$adminmain .= "<tr><td>".$L['Title']." :</td><td><input type=\"text\" class=\"text\" name=\"rtitle\" value=\"".sed_cc($fs_title)."\" size=\"56\" maxlength=\"128\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Description']." :</td><td><input type=\"text\" class=\"text\" name=\"rdesc\" value=\"".sed_cc($fs_desc)."\" size=\"56\" maxlength=\"255\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['Icon']." :</td><td> <input type=\"text\" class=\"text\" name=\"ricon\" value=\"".sed_cc($fs_icon)."\" size=\"40\" maxlength=\"255\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['adm_diplaysignatures']." :</td><td>".$form_allowusertext."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_enablebbcodes']." :</td><td>".$form_allowbbcodes."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_enablesmilies']." :</td><td>".$form_allowsmilies."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_enableprvtopics']." :</td><td>".$form_allowprvtopics."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_enableviewers']." :</td><td>".$form_allowviewers."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_enablepolls']." :</td><td>".$form_allowpolls."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_countposts']." :</td><td>".$form_countposts."</td></tr>";
	$adminmain .= "<tr><td>".$L['Locked']." :</td><td>".$form_state."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_autoprune']." :</td><td><input type=\"text\" class=\"text\" name=\"rautoprune\" value=\"".$fs_autoprune."\" size=\"3\" maxlength=\"7\" /></td></tr>";
	$adminmain .= "<tr><td>".$L['adm_postcounters']." :</td><td><a href=\"".sed_url('admin', "m=forums&n=edit&a=resync&id=".$fs_id."&".sed_xg())."\">".$L['Resync']."</a></td></tr>";
	$adminmain .= ($usr['isadmin']) ? "<tr><td>".$L['Delete']." :</td><td>[<a href=\"".sed_url('admin', "m=forums&n=edit&a=delete&id=".$fs_id."&".sed_xg())."\">x</a>]</td></tr>" : '';
	$adminmain .= "<tr><td colspan=\"2\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr>";
	$adminmain .= "</table></form>";
}

else
{
	if ($a=='order')
	{
		$w = sed_import('w', 'G', 'ALP', 4);

		$sql = sed_sql_query("SELECT fs_order, fs_category FROM $db_forum_sections WHERE fs_id='".$id."'");
		sed_die(sed_sql_numrows($sql)==0);
		$row_cur = sed_sql_fetcharray($sql);

		if ($w=='up')
		{
			$sql = sed_sql_query("SELECT fs_id, fs_order FROM $db_forum_sections WHERE fs_category='".$row_cur['fs_category']."' AND fs_order<'".$row_cur['fs_order']."' ORDER BY fs_order DESC LIMIT 1");
		}
		else
		{
			$sql = sed_sql_query("SELECT fs_id, fs_order FROM $db_forum_sections WHERE fs_category='".$row_cur['fs_category']."' AND fs_order>'".$row_cur['fs_order']."' ORDER BY fs_order ASC LIMIT 1");
		}

		if (sed_sql_numrows($sql)>0)
		{
			$row_oth = sed_sql_fetcharray($sql);
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_order='".$row_oth['fs_order']."' WHERE fs_id='".$id."'");
			$sql = sed_sql_query("UPDATE $db_forum_sections SET fs_order='".$row_cur['fs_order']."' WHERE fs_id='".$row_oth['fs_id']."'");
		}

		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=forums&d='.$d, '', true));
		exit;
	}
	elseif ($a=='add')
	{
		$nmaster = sed_import('nmaster', 'P', 'INT');
		$ntitle = sed_import('ntitle', 'P', 'TXT');
		$ndesc = sed_import('ndesc', 'P', 'TXT');
		$ncat = sed_import('ncat', 'P', 'TXT');

		if (!empty($ntitle))
		{
			$sql1 = sed_sql_query("SELECT fs_order FROM $db_forum_sections WHERE fs_category='".sed_sql_prep($ncat)."' ORDER BY fs_order DESC LIMIT 1");
			if ($row1 = sed_sql_fetcharray($sql1))
			{ $nextorder = $row1['fs_order']+1; }
			else
			{ $nextorder = 100; }

			if (!empty($nmaster))
			{
				$sql2 = sed_sql_query("SELECT fs_title FROM $db_forum_sections WHERE fs_id='".$nmaster."' ");
				$row2 = sed_sql_fetcharray($sql2);

				$mastername = sed_sql_prep($row2['fs_title']);

			}

			$sql = sed_sql_query("INSERT INTO $db_forum_sections (fs_masterid, fs_mastername, fs_order, fs_title, fs_desc, fs_category, fs_icon, fs_autoprune, fs_allowusertext, fs_allowbbcodes, fs_allowsmilies, fs_allowprvtopics, fs_countposts) VALUES ('".(int)$nmaster."', '".$mastername."', '".(int)$nextorder."', '".sed_sql_prep($ntitle)."', '".sed_sql_prep($ndesc)."', '".sed_sql_prep($ncat)."', 'images/admin/forums.gif', 0, 1, 1, 1, 0, 1)");

			$forumid = sed_sql_insertid();

			foreach($sed_groups as $k => $v)
			{
				if ($k==1 || $k==2)
				{
					$ins_auth = 1;
					$ins_lock = 254;
				}
				elseif ($k==3)
				{
					$ins_auth = 0;
					$ins_lock = 255;
				}
				elseif ($k==5)
				{
					$ins_auth = 255;
					$ins_lock = 255;
				}
				else
				{
					$ins_auth = 3;
					$ins_lock = ($k==4) ? 128 : 0;
				}

				$sql = sed_sql_query("INSERT into $db_auth (auth_groupid, auth_code, auth_option, auth_rights, auth_rights_lock, auth_setbyuserid) VALUES (".(int)$v['id'].", 'forums', ".(int)$forumid.", ".(int)$ins_auth.", ".(int)$ins_lock.", ".(int)$usr['id'].")");
			}
			sed_auth_reorder();
			sed_auth_clear('all');
			header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', 'm=forums', '', true));
		}
	}
	/*
	 $totalitems = sed_sql_rowcount($db_forum_sections)+sed_sql_rowcount($db_forum_structure);
	 $pagnav = sed_pagination(sed_url('admin','m=forums'), $d, $totalitems, $cfg['maxrowsperpage']);
	 list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=forums'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
	 */
	$sql = sed_sql_query("SELECT s.*, n.* FROM $db_forum_sections AS s LEFT JOIN
	$db_forum_structure AS n ON n.fn_code=s.fs_category
	ORDER by fs_masterid DESC, fn_path ASC, fs_order ASC, fs_title ASC");

	$adminmain .= "<h4>".$L['editdeleteentries']." :</h4>";
	//$adminmain .= "<div class=\"pagnav\">".$pagination_prev." ".$pagnav." ".$pagination_next."</div>";
	$adminmain .= "<form id=\"updateorder\" action=\"".sed_url('admin', 'm=forums&a=update&d='.$d)."\" method=\"post\">";
	$adminmain .= "<table class=\"cells\"><tr>";
	$adminmain .= "<td class=\"coltop\">".$L['Section']." ".$L['adm_clicktoedit']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['Order']."</td>";
	$adminmain .= "<td class=\"coltop\">".$L['adm_enableprvtopics']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Topics']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Posts']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:48px;\">".$L['Views']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:80px;\">".$L['Rights']."</td>";
	$adminmain .= "<td class=\"coltop\" style=\"width:64px;\">".$L['Open']."</td>";
	$adminmain .= "</tr>";

	$prev_cat = '';
	$line = 1;
	$fcache = array();

	$ii = 0;

	while ($row = sed_sql_fetcharray($sql))
	{
		if ($row['fs_masterid']>0)
		{
			$fcache[$row['fs_masterid']][$row['fs_id']] = array($row['fs_title'], $row['fs_topiccount'], $row['fs_postcount'], $row['fs_viewcount'], $row['fs_allowprvtopics']);
		}

		else
		{
			$fs_id = $row['fs_id'];
			$fs_state = $row['fs_state'];
			$fs_order = $row['fs_order'];
			$fs_title = sed_cc($row['fs_title']);
			$fs_desc = sed_cc($row['fs_desc']);
			$fs_category = $row['fs_category'];

			if ($fs_category!=$prev_cat)
			{
				$adminmain .= "<tr><td colspan=\"8\"><strong><a href=\"".sed_url('admin', "m=forums&s=structure&n=options&id=".$row['fn_id'])."\">".sed_cc($row['fn_title']);
				$adminmain .= " (".$row['fn_path'].")</a></strong></td></tr>";
				$prev_cat = $fs_category;
				$line = 1;

				$ii++;
			}

			$adminmain .= "<tr>";
			$adminmain .= "<td><a href=\"".sed_url('admin', "m=forums&n=edit&id=".$fs_id)."\">".sed_cc($fs_title)."</a></td>";
			$adminmain .= "<td style=\"text-align:center;\">";
			$adminmain .= "<a href=\"".sed_url('admin', "m=forums&id=".$fs_id."&a=order&w=up&d=".$d)."\">".$sed_img_up."</a> ";
			$adminmain .= "<a href=\"".sed_url('admin', "m=forums&id=".$fs_id."&a=order&w=down&d=".$d)."\">".$sed_img_down."</a></td>";

			$adminmain .= "<td style=\"text-align:center;\">".$sed_yesno[$row['fs_allowprvtopics']]."</td>";
			$adminmain .= "<td style=\"text-align:right;\">".$row['fs_topiccount']."</td>";
			$adminmain .= "<td style=\"text-align:right;\">".$row['fs_postcount']."</td>";
			$adminmain .= "<td style=\"text-align:right;\">".$row['fs_viewcount']."</td>";
			$adminmain .= "<td style=\"text-align:center;\"><a href=\"".sed_url('admin', "m=rightsbyitem&ic=forums&io=".$row['fs_id'])."\"><img src=\"images/admin/rights2.gif\" alt=\"\" /></a></td>";
			$adminmain .= "<td style=\"text-align:center;\"><a href=\"".sed_url('forums', "m=topics&s=".$fs_id)."\"><img src=\"images/admin/jumpto.gif\" alt=\"\" /></a></td>";
			$adminmain .= "</tr>";
			$line++;

			$ii++;

			if ($fcache[$fs_id])
			{

				foreach ($fcache[$fs_id] as $key => $value)
				{
					$adminmain .= "<tr>";
					$adminmain .= "<td><a href=\"".sed_url('admin', "m=forums&n=edit&id=".$key)."\"> - ".sed_cc($value[0])."</a></td>";
					$adminmain .= "<td style=\"text-align:center;\">";
					$adminmain .= "<a href=\"".sed_url('admin', "m=forums&id=".$key."&a=order&w=up")."\">".$sed_img_up."</a> ";
					$adminmain .= "<a href=\"".sed_url('admin', "m=forums&id=".$key."&a=order&w=down")."\">".$sed_img_down."</a></td>";
					$adminmain .= "<td style=\"text-align:center;\">".$sed_yesno[$value[4]]."</td>";
					$adminmain .= "<td style=\"text-align:right;\">".$value[1]."</td>";
					$adminmain .= "<td style=\"text-align:right;\">".$value[2]."</td>";
					$adminmain .= "<td style=\"text-align:right;\">".$value[3]."</td>";
					$adminmain .= "<td style=\"text-align:center;\"><a href=\"".sed_url('admin', "m=rightsbyitem&ic=forums&io=".$key)."\"><img src=\"images/admin/rights2.gif\" alt=\"\" /></a></td>";
					$adminmain .= "<td style=\"text-align:center;\"><a href=\"".sed_url('forums', "m=topics&s=".$key)."\"><img src=\"images/admin/jumpto.gif\" alt=\"\" /></a></td>";
					$adminmain .= "</tr>";$ii++;
				}

			}

		}
	}

	$adminmain .= "<tr><td colspan=\"9\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\" /></td></tr>";
	$adminmain .= "<tr><td colspan=\"9\">&nbsp;</td></tr>";
	$adminmain .= "<tr><td colspan=\"9\">".$L['Total']." : ".$totalitems.", ".$L['adm_polls_on_page'].": ".$ii."</td></tr></table>";
	$adminmain .= "</form>";

	$adminmain .= "<h4>".$L['addnewentry']." :</h4>";
	$adminmain .= "<form id=\"addsection\" action=\"".sed_url('admin', "m=forums&a=add")."\" method=\"post\">";
	$adminmain .= "<table class=\"cells\">";
	$adminmain .= "<tr><td>".$L['Category']." :</td><td>".sed_selectbox_forumcat('', 'ncat')."</td></tr>";
	$adminmain .= "<tr><td>".$L['adm_forums_master']." :</td><td><select name=\"nmaster\"><option value=\"0\">--</option>";
	$sqla = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category WHERE fs_masterid<1 ORDER by fn_path ASC, fs_order ASC");
	while ($rowa = sed_sql_fetchassoc($sqla))
	{
		$cfs = sed_build_forums($rowa['fs_id'], $rowa['fs_title'], $rowa['fs_category'], FALSE);
		$adminmain .= "<option value=\"".$rowa['fs_id']."\">".$cfs."</option>";
	}
	$adminmain .= "</select></td></tr>";
	$adminmain .= "<tr><td>".$L['Title']." :</td><td><input type=\"text\" class=\"text\" name=\"ntitle\" value=\"\" size=\"64\" maxlength=\"128\" /> ".$L['adm_required']."</td></tr>";
	$adminmain .= "<tr><td>".$L['Description']." :</td><td><input type=\"text\" class=\"text\" name=\"ndesc\" value=\"\" size=\"64\" maxlength=\"255\" /></td></tr>";
	$adminmain .= "<tr><td colspan=\"2\"><input type=\"submit\" class=\"submit\" value=\"".$L['Add']."\" /></td></tr>";
	$adminmain .= "</table></form>";
}

?>