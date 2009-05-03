<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=search
Part=main
File=search
Hooks=standalone
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Search standalone.
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Olivier C. & Spartan, oc
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') && defined('SED_PLUG') or die('Wrong URL');

/* === EDIT THE FOLLOWING === */

$cfg_maxwords = 5;
$cfg_maxwords_frm = 5;
$cfg_maxwords_pag = 5;
$cfg_maxitems = 50;

/* === DO NOT EDIT THE FOLLOWING === */

$sq = sed_import('sq','P','TXT');
$searchall = sed_import('searchall','P','INT');
$pre = sed_import('pre','G','TXT');
$a = sed_import('a','G','TXT');
$d = sed_import('d','G','INT');
$tab = sed_import('tab','G','TXT');
$frm = sed_import('frm','G','BOL');
$pag = sed_import('pag','G','INT');

if (empty($d))
{ $d = '0'; }

if (empty($pag))
{ $pag = '0'; }

$cfg['plugin']['search']['results'] = 2;

if ($frm) { $tab='frm'; }

/* === FORUM TAB === */

if ($tab=='frm') {

	$sea_frmtitle = sed_import('sea_frmtitle','P','INT');
	$sea_frmtext = sed_import('sea_frmtext','P','INT');
	$sea_frmreply = sed_import('sea_frmreply','P','INT');
	$sea_frmtime = sed_import('sea_frmtime','P','INT');
	$sea_frmtime2 = sed_import('sea_frmtime2','P','TXT');
	$sea_frmsort = sed_import('sea_frmsort','P','INT');
	$sea_frmsort2 = sed_sql_prep(sed_import('sea_frmsort2','P','TXT'));
	
	/*== Keep data ==*/
	
	if ($d==0 && empty($pre))
		{
		$_SESSION['s_sea_frmtitle'] = $sea_frmtitle;
		$_SESSION['s_sea_frmtext'] = $sea_frmtext;
		$_SESSION['s_sea_frmreply'] = $sea_frmreply;
		$_SESSION['s_sea_frmtime'] = $sea_frmtime;
		$_SESSION['s_sea_pagtime'] = $sea_pagtime;
		$_SESSION['s_sea_frmtime2'] = $sea_frmtime2;
		$_SESSION['s_sea_frmsort'] = $sea_frmsort;
		$_SESSION['s_sea_frmsort2'] = $sea_frmsort2;
		}
	else
		{
		$sea_frmtitle = $_SESSION['s_sea_frmtitle'];
		$sea_frmtext = $_SESSION['s_sea_frmtext'];
		$sea_frmreply = $_SESSION['s_sea_frmreply'];
		$sea_frmtime = $_SESSION['s_sea_frmtime'];
		$sea_frmtime2 = $_SESSION['s_sea_frmtime2'];
		$sea_frmsort = $_SESSION['s_sea_frmsort'];
		$sea_frmsort2 = $_SESSION['s_sea_frmsort2'];		
		}
		
	/*== Keep data ==*/

	$sq = (!empty($pre)) ? $pre : $sq;
	$sachecked = ($searchall) ? ' checked="checked" ' : '';

	$plugin_title = $L['plu_title_frmtab'];

	$plugin_subtitle .= "<a href=\"".sed_url('plug', 'e=search')."\">".$L['All']."</a> &nbsp; ".$L['Forums']." &nbsp; <a href=\"".sed_url('plug', 'e=search&tab=pag')."\">".$L['Pages']."</a>";
	$plugin_subtitle .= "<br />".$L['plu_title_frmtab_s'];

	$plugin_body .= "<form id=\"search\" action=\"".sed_url('plug', 'e=search&tab=frm&a=search')."\" method=\"post\">";
	$plugin_body .= "<table class=\"cells\">";
	$plugin_body .= "<tr><td width=\"20%\">".$L['plu_searchin1']."</td>";
	$plugin_body .= "<td width=\"80%\"><input type=\"text\" class=\"text\" name=\"sq\" value=\"".sed_cc($sq)."\" size=\"16\" maxlength=\"32\" />".$L['plu_searchin2']."
	<p><input type=\"checkbox\" name=\"searchall\" value=\"1\" $sachecked />".$L['plu_searchall']."<br />
	".$L['plu_searchall2']."</p></td></tr>";

	if (!$cfg['disable_forums'])
	{

		$sql1 = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s
		LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category
		ORDER by fn_path ASC, fs_order ASC");

		$plugin_body .= "<tr><td>";
		$plugin_body .= $L['Forums']."<br />".$L['plu_frm_hint']."</td><td><select multiple=\"multiple\" name=\"frm_sub[]\" size=\"5\">";
		$plugin_body .= "<option value=\"9999\" selected=\"selected\">".$L['plu_allsections']."</option>";

		while ($row1 = mysql_fetch_array($sql1))
		{
			if (sed_auth('forums', $row1['fs_id'], 'R'))
			{
				$plugin_body .= "<option value=\"".$row1['fs_id']."\">".sed_build_forums($row1['fs_id'], $row1['fs_title'], $row1['fs_category'], FALSE)."</option>";
			}
		}

		$plugin_body .= "</select></td></tr>";
		$plugin_body .= "<tr><td>".$L['plu_searchin']."</td><td><input type=\"checkbox\" class=\"checkbox\" name=\"sea_frmtitle\" checked=\"checked\" value=\"1\" />".$L['Title']."<input type=\"checkbox\" class=\"checkbox\" name=\"sea_frmtext\" checked=\"checked\" value=\"1\" />".$L['Post']."</td></tr>";
		$plugin_body .= "<tr><td>".$L['plu_frm_rep']."</td><td><input type=\"checkbox\" class=\"checkbox\" name=\"sea_frmreply\" value=\"1\" /></td></tr>";
		$plugin_body .= "<tr><td>".$L['Find results from']."</td><td><select name=\"sea_frmtime\"><option value=\"86400\">".$L['A day ago']."</option>";
		$plugin_body .= "<option value=\"604800\">".$L['A week ago']."</option>";
		$plugin_body .= "<option value=\"1209600\">".$L['2 weeks ago']."</option>";
		$plugin_body .= "<option value=\"2419200\">".$L['A month ago']."</option>";
		$plugin_body .= "<option value=\"7257600\">".$L['3 months ago']."</option>";
		$plugin_body .= "<option value=\"14515200\">".$L['6 months ago']."</option>";
		$plugin_body .= "<option value=\"29030400\">".$L['A year ago']."</option>";
		$plugin_body .= "<option value=\"999999999\" selected=\"selected\">".$L['Any date']."</option>";
		$plugin_body .= "</select><select name=\"sea_frmtime2\">";
		$plugin_body .= "<option value=\"new\" selected=\"selected\">".$L['And newer']."</option>";
		$plugin_body .= "<option value=\"old\">".$L['And older']."</option></select></td></tr>";
		$plugin_body .= "<tr><td>".$L['Sort results by']."</td><td><select name=\"sea_frmsort\"><option value=\"1\" selected=\"selected\">".$L['Last updated']."</option>";
		$plugin_body .= "<option value=\"2\">".$L['Creation date']."</option>";
		$plugin_body .= "<option value=\"3\">".$L['Title']."</option>";
		$plugin_body .= "<option value=\"4\">".$L['Number of replies']."</option>";
		$plugin_body .= "<option value=\"5\">".$L['Number of views']."</option>";
		$plugin_body .= "</select><select name=\"sea_frmsort2\">";
		$plugin_body .= "<option value=\"DESC\" selected=\"selected\">".$L['Descending']."</option>";
		$plugin_body .= "<option value=\"ASC\">".$L['Ascending']."</option></select></td></tr>";
		$plugin_body .= "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" class=\"submit\" value=\"".$L['Search']."\" /></td></tr>";
	}

	$plugin_body .= "</table></form>";

	if ($a=='search')
	{	
		if (mb_strlen($sq)<3)
		{
			$plugin_body .= "<p>".$L['plu_querytooshort']."</p>";
			$a = '';
		}

		$sq = sed_sql_prep($sq);
		
		$words = explode(" ", $sq);
		$words_count = count($words);

		if ($words_count > $cfg_maxwords_frm)
		{
			$plugin_body .= "<p>".$L['plu_toomanywords']." ".$cfg_maxwords_frm."</p>";
			$a = '';
		}

		$ii = 0;
		foreach ($words as $key=>$value)
		{
			$ii++;
			if (!$searchall)
			{
			if ($sea_frmtext=='1')
				{
			$like = 'p.fp_text LIKE ';
			$sqlsearch1 .= ($words_count>$ii) ? $like."'%$value%' OR " : $like."'%$value%' ";
				}
			if ($sea_frmtitle=='1')
				{
			$like = 't.ft_title LIKE ';
			$sqlsearch2 .= ($words_count>$ii) ? $like."'%$value%' OR " : $like."'%$value%' ";
				}
			}
			else
			{
			if ($sea_frmtext=='1')
				{
			$like = 'p.fp_text LIKE ';
			$sqlsearch1 .= ($words_count>$ii) ? $like."'%$value%' AND " : $like."'%$value%' ";
				}
			if ($sea_frmtitle=='1')
				{
			$like = 't.ft_title LIKE ';
			$sqlsearch2 .= ($words_count>$ii) ? $like."'%$value%' AND " : $like."'%$value%' ";
				}
			}
		}


		if (!$cfg['disable_forums'] && !empty($a))
		{			
			$frm_sub = sed_import('frm_sub','P','ARR');
			
	/*== Keep data ==*/
			
		if ($d==0 && empty($pre))
			{ $_SESSION['s_frm_sub'] = $frm_sub; }
		else
			{ $frm_sub = $_SESSION['s_frm_sub']; }
			
	/*== Keep data ==*/

	
			if ($frm_sub[0]==9999)
			{ $sqlsections = ''; }
			else
			{
				foreach($frm_sub as $i => $k)
				{ $sections1[] = "s.fs_id='".sed_sql_prep($k)."'"; }
				$sqlsections = "AND (".implode(' OR ', $sections1).")";
			}

			if ($sea_frmreply=='1') { $frm_reply = "AND t.ft_postcount>1"; }

			if ($sea_frmtime2=='new') { $sqlsections2 = "AND t.ft_updated>(".$sys['now_offset']."-".$sea_frmtime.")"; } elseif ($sea_frmtime2=='old') { $sqlsections2 = "AND t.ft_updated<(".$sys['now_offset']."-".$sea_frmtime.")"; }

			if ($sea_frmsort=='1') { $orderby = "ft_updated ".$sea_frmsort2; }
			elseif ($sea_frmsort=='2') { $orderby = "ft_creationdate ".$sea_frmsort2; }
			elseif ($sea_frmsort=='3') { $orderby = "ft_title ".$sea_frmsort2; }
			elseif ($sea_frmsort=='4') { $orderby = "ft_postcount ".$sea_frmsort2; }
			elseif ($sea_frmsort=='5') { $orderby = "ft_viewcount ".$sea_frmsort2; }

			if ($sea_frmtitle=='1' && $sea_frmtext!='1') {
				$sql = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
		 	WHERE 1 AND ($sqlsearch2)
		 	AND p.fp_topicid=t.ft_id $frm_reply
		 	AND p.fp_sectionid=s.fs_id $sqlsections $sqlsections2
		 	GROUP BY t.ft_id ORDER BY $orderby
		 	LIMIT $d, ".$cfg['plugin']['search']['results']);
			$totalitems = mysql_fetch_row(sed_sql_query("SELECT FOUND_ROWS()"));
		 	$items = mysql_num_rows($sql);
			}
			elseif ($sea_frmtext=='1' && $sea_frmtitle!='1') {
				$sql = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
		 	WHERE 1 AND ($sqlsearch1)
		 	AND p.fp_topicid=t.ft_id $frm_reply
		 	AND p.fp_sectionid=s.fs_id $sqlsections $sqlsections2
		 	GROUP BY t.ft_id ORDER BY $orderby
		 	LIMIT $d, ".$cfg['plugin']['search']['results']);
			$totalitems = mysql_fetch_row(sed_sql_query("SELECT FOUND_ROWS()"));
		 	$items = mysql_num_rows($sql);
			}
			elseif ($sea_frmtext=='1' && $sea_frmtitle=='1') {
				$sql = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
		 	WHERE 1 AND ( ($sqlsearch1) OR ($sqlsearch2) )
		 	AND p.fp_topicid=t.ft_id $frm_reply
		 	AND p.fp_sectionid=s.fs_id $sqlsections $sqlsections2
		 	GROUP BY t.ft_id ORDER BY $orderby
		 	LIMIT $d, ".$cfg['plugin']['search']['results']);
			$totalitems = mysql_fetch_row(sed_sql_query("SELECT FOUND_ROWS()"));
		 	$items = mysql_num_rows($sql);
			}
			else {
				$plugin_body .= "<p>blahbla</p>";
				$a = '';
				$items = "0";
			}

			if ($items!='0') {
				$plugin_body .= "<h4>".$L['Forums']." : ".$L['Poster']." ".sed_declension($items,$L['plu_match'])."</h4>";
				$hl = strtoupper($sq);
				
				$pagnav = sed_pagination(sed_url('plug','e=search&tab=frm&a=search&pre='.$sq), $d, $totalitems[0], $cfg['plugin']['search']['results']);
				list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('plug', 'e=search&tab=frm&a=search&pre='.$sq), $d, $totalitems[0], $cfg['plugin']['search']['results'], TRUE);

				$plugin_body .= "<table class=\"cells\" width=\"100%\"><tr><td width=\"30%\">".$L['Section']."</td><td width=\"60%\">".$L['Topic']."</td><td width=\"10%\">".$L['Poster']."</td></tr>";
				while ($row = mysql_fetch_array($sql))
				{
					if (sed_auth('forums', $row['fs_id'], 'R'))
					{
						$post_url = ($cfg['plugin']['search']['searchurls'] == 'Single') ? sed_url('forums', 'm=posts&id='.$row['fp_id'].'&highlight='.$hl) : sed_url('forums', 'm=posts&p='.$row['fp_id'].'&highlight='.$hl, '#'.$row['fp_id']);
						$plugin_body .= "<tr><td>".sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category'], TRUE)."</td><td><a href=\"".$post_url."\">".sed_cc($row['ft_title'])."</a></td><td>".sed_build_user($row['ft_firstposterid'],$row['ft_firstpostername'])."</td></tr>";
					}
				}
				$plugin_body .= "</table>";
				$plugin_body .= $pagination_prev.$pagnav.$pagination_next;
				
				$sections++;
			}
		}

	}

	/* === PAGE TAB === */

} elseif ($tab=='pag') {

	$sea_pagtitle = sed_import('sea_pagtitle','P','INT');
	$sea_pagdesc = sed_import('sea_pagdesc','P','INT');
	$sea_pagtext = sed_import('sea_pagtext','P','INT');
	$sea_pagfile = sed_import('sea_pagfile','P','INT');
	$sea_pagtime = sed_import('sea_pagtime','P','INT');
	$sea_pagtime2 = sed_import('sea_pagtime2','P','TXT');
	$sea_pagsort = sed_import('sea_pagsort','P','INT');
	$sea_pagsort2 = sed_sql_prep(sed_import('sea_pagsort2','P','TXT'));
	
	/*== Keep data ==*/
	
	if ($d==0 && empty($pre))
		{
		$_SESSION['s_sea_pagtitle'] = $sea_pagtitle;
		$_SESSION['s_sea_pagdesc'] = $sea_pagdesc;
		$_SESSION['s_sea_pagtext'] = $sea_pagtext;
		$_SESSION['s_sea_pagfile'] = $sea_pagfile;
		$_SESSION['s_sea_pagtime'] = $sea_pagtime;
		$_SESSION['s_sea_pagtime2'] = $sea_pagtime2;
		$_SESSION['s_sea_pagsort'] = $sea_pagsort;
		$_SESSION['s_sea_pagsort2'] = $sea_pagsort2;
		}
	else
		{
		$sea_pagtitle = $_SESSION['s_sea_pagtitle'];
		$sea_pagdesc = $_SESSION['s_sea_pagdesc'];
		$sea_pagtext = $_SESSION['s_sea_pagtext'];
		$sea_pagfile = $_SESSION['s_sea_pagfile'];
		$sea_pagtime = $_SESSION['s_sea_pagtime'];
		$sea_pagtime2 = $_SESSION['s_sea_pagtime2'];
		$sea_pagsort = $_SESSION['s_sea_pagsort'];
		$sea_pagsort2 = $_SESSION['s_sea_pagsort2'];
		}
		
	/*== Keep data ==*/

	$sq = (!empty($pre)) ? $pre : $sq;
	$sachecked = ($searchall) ? ' checked="checked" ' : '';

	$plugin_title = $L['plu_title_pagtab'];

	$plugin_subtitle .= "<a href=\"".sed_url('plug', 'e=search')."\">".$L['All']."</a> &nbsp; <a href=\"".sed_url('plug', 'e=search&tab=frm')."\">".$L['Forums']."</a> &nbsp; ".$L['Pages'];
	$plugin_subtitle .= "<br />".$L['plu_title_pagtab_s'];

	$plugin_body .= "<form id=\"search\" action=\"".sed_url('plug', 'e=search&tab=pag&a=search')."\" method=\"post\">";
	$plugin_body .= "<table class=\"cells\">";
	$plugin_body .= "<tr><td width=\"20%\">".$L['plu_searchin1']."</td>";
	$plugin_body .= "<td width=\"80%\"><input type=\"text\" class=\"text\" name=\"sq\" value=\"".sed_cc($sq)."\" size=\"16\" maxlength=\"32\" />".$L['plu_searchin2']."
	<p><input type=\"checkbox\" name=\"searchall\" value=\"1\" $sachecked />".$L['plu_searchall']."<br />
	".$L['plu_searchall2']."</p></td></tr>";

	if (!$cfg['disable_page'])
	{
		$plugin_body .= "<tr><td>";
		$plugin_body .= $L['Pages']."<br />".$L['plu_pag_hint']."</td><td><select multiple=\"multiple\" name=\"pag_sub[]\" size=\"5\">";
		$plugin_body .= "<option value=\"all\" selected=\"selected\">".$L['plu_allcategories']."</option>";

		foreach ($sed_cat as $i =>$x)
		{
			if ($i!='all' && $i!='system' && sed_auth('page', $i, 'R'))
			{
				$selected = ($i == $check) ? "selected=\"selected\"" : '';
				$plugin_body .= "<option value=\"".$i."\" $selected> ".$x['tpath']."</option>";
			}
		}

		$plugin_body .= "</select></td></tr>";
		$plugin_body .= "<tr><td>".$L['plu_searchin']."</td><td><input type=\"checkbox\" class=\"checkbox\" name=\"sea_pagtitle\" checked=\"checked\" value=\"1\" />".$L['Title']."<input type=\"checkbox\" class=\"checkbox\" name=\"sea_pagdesc\" checked=\"checked\" value=\"1\" />".$L['Description']."<input type=\"checkbox\" class=\"checkbox\" name=\"sea_pagtext\" checked=\"checked\" value=\"1\" />".$L['Body']."</td></tr>";
		$plugin_body .= "<tr><td>".$L['plu_pag_fil']."</td><td><input type=\"checkbox\" class=\"checkbox\" name=\"sea_pagfile\" value=\"1\" /></td></tr>";
		$plugin_body .= "<tr><td>".$L['Find results from']."</td><td><select name=\"sea_pagtime\"><option value=\"86400\">".$L['A day ago']."</option>";
		$plugin_body .= "<option value=\"604800\">".$L['A week ago']."</option>";
		$plugin_body .= "<option value=\"1209600\">".$L['2 weeks ago']."</option>";
		$plugin_body .= "<option value=\"2419200\">".$L['A month ago']."</option>";
		$plugin_body .= "<option value=\"7257600\">".$L['3 months ago']."</option>";
		$plugin_body .= "<option value=\"14515200\">".$L['6 months ago']."</option>";
		$plugin_body .= "<option value=\"29030400\">".$L['A year ago']."</option>";
		$plugin_body .= "<option value=\"999999999\" selected=\"selected\">".$L['Any date']."</option>";
		$plugin_body .= "</select><select name=\"sea_pagtime2\">";
		$plugin_body .= "<option value=\"new\" selected=\"selected\">".$L['And newer']."</option>";
		$plugin_body .= "<option value=\"old\">".$L['And older']."</option></select></td></tr>";
		$plugin_body .= "<tr><td>".$L['Sort results by']."</td><td><select name=\"sea_pagsort\"><option value=\"1\" selected=\"selected\">".$L['Creation date']."</option>";
		$plugin_body .= "<option value=\"2\">".$L['Title']."</option>";
		$plugin_body .= "<option value=\"3\">".$L['Number of views']."</option>";
		$plugin_body .= "</select><select name=\"sea_pagsort2\">";
		$plugin_body .= "<option value=\"DESC\" selected=\"selected\">".$L['Descending']."</option>";
		$plugin_body .= "<option value=\"ASC\">".$L['Ascending']."</option></select></td></tr>";
		$plugin_body .= "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" class=\"submit\" value=\"".$L['Search']."\" /></td></tr>";
	}

	$plugin_body .= "</table></form>";

	if ($a=='search')
	{
		if (mb_strlen($sq)<3)
		{
			$plugin_body .= "<p>".$L['plu_querytooshort']."</p>";
			$a = '';
		}

		$sq = sed_sql_prep($sq);

		$words = explode(" ", $sq);
		$words_count = count($words);

		if ($words_count > $cfg_maxwords_pag)
		{
			$plugin_body .= "<p>".$L['plu_toomanywords']." ".$cfg_maxwords_pag."</p>";
			$a = '';
		}

		$ii = 0;
		foreach ($words as $key=>$value)
		{
			$ii++;
			if (!$searchall)
			{
			if ($sea_pagtitle=='1')
				{
			$like = 'p.page_title LIKE ';
			$sqlsearch2 .= ($words_count>$ii) ? $like."'%$value%' OR " : $like."'%$value%' ";
				}
			if ($sea_pagdesc=='1')
				{
			$like = 'p.page_desc LIKE ';
			$sqlsearch3 .= ($words_count>$ii) ? $like."'%$value%' OR " : $like."'%$value%' ";
				}
			if ($sea_pagtext=='1')
				{
			$like = 'p.page_text LIKE ';
			$sqlsearch1 .= ($words_count>$ii) ? $like."'%$value%' OR " : $like."'%$value%' ";
				}
			}
			else
			{
			if ($sea_pagtitle=='1')
				{
			$like = 'p.page_title LIKE ';
			$sqlsearch2 .= ($words_count>$ii) ? $like."'%$value%' AND " : $like."'%$value%' ";
				}
			if ($sea_pagdesc=='1')
				{
			$like = 'p.page_desc LIKE ';
			$sqlsearch3 .= ($words_count>$ii) ? $like."'%$value%' AND " : $like."'%$value%' ";
				}
			if ($sea_pagtext=='1')
				{
			$like = 'p.page_text LIKE ';
			$sqlsearch1 .= ($words_count>$ii) ? $like."'%$value%' AND " : $like."'%$value%' ";
				}
			}
		}

		if (!$cfg['disable_page'] && !empty($a))
		{
			$pag_sub = sed_import('pag_sub','P','ARR');
			
	/*== Keep data ==*/
			
		if ($d==0 && empty($pre))
			{ $_SESSION['s_pag_sub'] = $pag_sub; }
		else
			{ $pag_sub = $_SESSION['s_pag_sub']; }
			
	/*== Keep data ==*/
	
			if ($pag_sub[0]=='all')
			{ $sqlsections = ''; }
			else
			{
				$sub = array();
				foreach($pag_sub as $i => $k)
				{ $sub[] = "page_cat='".sed_sql_prep($k)."'"; }
				$sqlsections = "AND (".implode(' OR ', $sub).")";
			}

			if ($sea_pagtitle=='1' && $sea_pagdesc!='1' && $sea_pagtext!='1') {
				$pagsql = "($sqlsearch2) AND ";
			}
			elseif ($sea_pagtitle=='1' && $sea_pagdesc=='1' && $sea_pagtext!='1') {
				$pagsql = "(($sqlsearch2) OR ($sqlsearch3)) AND ";
			}
			elseif ($sea_pagtitle=='1' && $sea_pagdesc!='1' && $sea_pagtext=='1') {
				$pagsql = "(($sqlsearch2) OR ($sqlsearch1)) AND ";
			}
			elseif ($sea_pagtitle!='1' && $sea_pagdesc=='1' && $sea_pagtext!='1') {
				$pagsql = "($sqlsearch3) AND ";
			}
			elseif ($sea_pagtitle!='1' && $sea_pagdesc=='1' && $sea_pagtext=='1') {
				$pagsql = "(($sqlsearch3) OR ($sqlsearch1)) AND ";
			}
			elseif ($sea_pagtitle!='1' && $sea_pagdesc!='1' && $sea_pagtext=='1') {
				$pagsql = "($sqlsearch1) AND ";
			}
			elseif ($sea_pagtitle=='1' && $sea_pagdesc=='1' && $sea_pagtext=='1') {
				$pagsql = "(($sqlsearch1) OR ($sqlsearch2) OR ($sqlsearch3)) AND ";
			}
			else {
				$pagsql = "";
				$plugin_body .= "<p>blahbla</p>";
				$cancel = "1";
			}

			if ($sea_pagtime2=='new') { $sqlsections2 = "AND page_date>(".$sys['now_offset']."-".$sea_pagtime.")"; } elseif ($sea_pagtime2=='old') { $sqlsections2 = "AND page_date<(".$sys['now_offset']."-".$sea_pagtime.")"; }

			if ($sea_pagsort=='1') { $orderby = "page_date ".$sea_pagsort2; }
			elseif ($sea_pagsort=='2') { $orderby = "page_title ".$sea_pagsort2; }
			elseif ($sea_pagsort=='3') { $orderby = "page_count ".$sea_pagsort2; }

			if ($cancel!='1') {
				if ($sea_pagfile=='1') {
					$sql  = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS page_id, page_ownerid, page_title, page_cat from $db_pages p, $db_structure s
					WHERE $pagsql
					p.page_file='1'
					AND p.page_state='0'
					AND p.page_cat=s.structure_code
					AND p.page_cat NOT LIKE 'system' $sqlsections2
					$sqlsections ORDER by $orderby
					LIMIT $d, ".$cfg['plugin']['search']['results']);
					$totalitems = mysql_fetch_row(sed_sql_query("SELECT FOUND_ROWS()"));
					$items = mysql_num_rows($sql);
				} else {
					$sql  = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS page_id, page_ownerid, page_title, page_cat from $db_pages p, $db_structure s
					WHERE $pagsql
					p.page_state='0'
					AND p.page_cat=s.structure_code
					AND p.page_cat NOT LIKE 'system' $sqlsections2
					$sqlsections ORDER by $orderby
					LIMIT $d, ".$cfg['plugin']['search']['results']);
					$totalitems = mysql_fetch_row(sed_sql_query("SELECT FOUND_ROWS()"));
					$items = mysql_num_rows($sql);
				}

				$plugin_body .= "<h4>".$L['Pages']." : ".$L['Poster']." ".sed_declension($items,$L['plu_match'])."</h4>";
				$hl = strtoupper($sq);
				
				$pagnav = sed_pagination(sed_url('plug','e=search&tab=pag&a=search&pre='.$sq), $d, $totalitems[0], $cfg['plugin']['search']['results']);
				list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('plug', 'e=search&tab=pag&a=search&pre='.$sq), $d, $totalitems[0], $cfg['plugin']['search']['results'], TRUE);

				$plugin_body .= "<table class=\"cells\" width=\"100%\"><tr><td width=\"30%\">".$L['Category']."</td><td width=\"60%\">".$L['Title']."</td><td width=\"10%\">".$L['Author']."</td></tr>";
				while ($row = mysql_fetch_array($sql))
				{
					if (sed_auth('page', $row['page_cat'], 'R'))
					{
						$ownername = sed_sql_fetcharray(sed_sql_query("SELECT user_name FROM $db_users WHERE user_id='".$row['page_ownerid']."'"));
						$plugin_body .= "<tr><td>".sed_build_catpath($row['page_cat'], '<a href="%1$s">%2$s</a>')."</td>";
						$plugin_body .= "<td><a href=\"".sed_url('page', 'id='.$row['page_id'].'&highlight='.$hl)."\">";
						$plugin_body .= sed_cc($row['page_title'])."</a></td><td>".sed_build_user($row['page_ownerid'],$ownername['user_name'])."</td></tr>";
					}
				}
				$plugin_body .= "</table>";
				$plugin_body .= $pagination_prev.$pagnav.$pagination_next;

				$sections++;
			}
		}
	}

	/* === DEFAULT TAB === */

} else {

	$sea_frmtitle = sed_import('sea_frmtitle','P','INT');
	$sea_frmtext = sed_import('sea_frmtext','P','INT');
	$sea_pagtitle = sed_import('sea_pagtitle','P','INT');
	$sea_pagdesc = sed_import('sea_pagdesc','P','INT');
	$sea_pagtext = sed_import('sea_pagtext','P','INT');
	
	/*== Keep data ==*/
	
	if ($d==0 && empty($pre))
		{
		$_SESSION['s_sea_frmtitle'] = $sea_frmtitle;
		$_SESSION['s_sea_frmtext'] = $sea_frmtext;
		$_SESSION['s_sea_pagtitle'] = $sea_pagtitle;
		$_SESSION['s_sea_pagdesc'] = $sea_pagdesc;
		$_SESSION['s_sea_pagtext'] = $sea_pagtext;
		}
	else
		{
		$sea_frmtitle = $_SESSION['s_sea_frmtitle'];
		$sea_frmtext = $_SESSION['s_sea_frmtext'];
		$sea_pagtitle = $_SESSION['s_sea_pagtitle'];
		$sea_pagdesc = $_SESSION['s_sea_pagdesc'];
		$sea_pagtext = $_SESSION['s_sea_pagtext'];
		}
		
	/*== Keep data ==*/
		
	$sq = (!empty($pre)) ? $pre : $sq;
	$sachecked = ($searchall) ? ' checked="checked" ' : '';

	$plugin_title = $L['Search'];

	$plugin_subtitle .= $L['All']." &nbsp; <a href=\"".sed_url('plug', 'e=search&tab=frm')."\">".$L['Forums']."</a> &nbsp; <a href=\"".sed_url('plug', 'e=search&tab=pag')."\">".$L['Pages']."</a>";
	$plugin_subtitle .= "<br />".$L['plu_title_alltab_s'];

	$plugin_body .= "<form id=\"search\" action=\"".sed_url('plug', 'e=search&a=search')."\" method=\"post\">";
	$plugin_body .= "<table class=\"cells\">";
	$plugin_body .= "<tr><td width=\"20%\">".$L['plu_searchin1']."</td>";
	$plugin_body .= "<td width=\"80%\"><input type=\"text\" class=\"text\" name=\"sq\" value=\"".sed_cc($sq)."\" size=\"16\" maxlength=\"32\" />".$L['plu_searchin2']."
	<p><input type=\"checkbox\" name=\"searchall\" value=\"1\" $sachecked />".$L['plu_searchall']."<br />
	".$L['plu_searchall2']."</p></td></tr>";

	if (!$cfg['disable_forums'])
	{

		$sql1 = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s
		LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category
		ORDER by fn_path ASC, fs_order ASC");

		$plugin_body .= "<tr><td>";
		$plugin_body .= $L['Forums']."<br />".$L['plu_frm_hint']."</td><td><select multiple=\"multiple\" name=\"frm_sub[]\" size=\"5\">";
		$plugin_body .= "<option value=\"9999\" selected=\"selected\">".$L['plu_allsections']."</option>";

		while ($row1 = mysql_fetch_array($sql1))
		{
			if (sed_auth('forums', $row1['fs_id'], 'R'))
			{
				$plugin_body .= "<option value=\"".$row1['fs_id']."\">".sed_build_forums($row1['fs_id'], $row1['fs_title'], $row1['fs_category'], FALSE)."</option>";
			}
		}

		$plugin_body .= "</select></td></tr>";
		$plugin_body .= "<tr><td>".$L['plu_searchin']."</td><td><input type=\"checkbox\" class=\"checkbox\" name=\"sea_frmtitle\" checked=\"checked\" value=\"1\" />".$L['Title']."<input type=\"checkbox\" class=\"checkbox\" name=\"sea_frmtext\" checked=\"checked\" value=\"1\" />".$L['Post']."</td></tr>";
	}

	if (!$cfg['disable_page'])
	{
		$plugin_body .= "<tr><td>";
		$plugin_body .= $L['Pages']."<br />".$L['plu_pag_hint']."</td><td><select multiple=\"multiple\" name=\"pag_sub[]\" size=\"5\">";
		$plugin_body .= "<option value=\"all\" selected=\"selected\">".$L['plu_allcategories']."</option>";

		foreach ($sed_cat as $i =>$x)
		{
			if ($i!='all' && $i!='system' && sed_auth('page', $i, 'R'))
			{
				$selected = ($i == $check) ? "selected=\"selected\"" : '';
				$plugin_body .= "<option value=\"".$i."\" $selected> ".$x['tpath']."</option>";
			}
		}

		$plugin_body .= "</select></td></tr>";
		$plugin_body .= "<tr><td>".$L['plu_searchin']."</td><td><input type=\"checkbox\" class=\"checkbox\" name=\"sea_pagtitle\" checked=\"checked\" value=\"1\" />".$L['Title']."<input type=\"checkbox\" class=\"checkbox\" name=\"sea_pagdesc\" checked=\"checked\" value=\"1\" />".$L['Description']."<input type=\"checkbox\" class=\"checkbox\" name=\"sea_pagtext\" checked=\"checked\" value=\"1\" />".$L['Body']."</td></tr>";
	}

	$plugin_body .= "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" class=\"submit\" value=\"".$L['Search']."\" /></td></tr>";
	$plugin_body .= "</table></form>";

	if ($a=='search')
	{
		if (mb_strlen($sq)<3)
		{
			$plugin_body .= "<p>".$L['plu_querytooshort']."</p>";
			$a = '';
		}
		
		$sq = sed_sql_prep($sq);

		$words = explode(" ", $sq);
		$words_count = count($words);

		if ($words_count > $cfg_maxwords)
		{
			$plugin_body .= "<p>".$L['plu_toomanywords']." ".$cfg_maxwords."</p>";
			$a = '';
		}

		$ii = 0;
		foreach ($words as $key=>$value)
		{
			$ii++;
			if (!$searchall)
			{
			if ($sea_pagtitle=='1')
				{
			$like = 'p.page_title LIKE ';
			$sqlsearch2 .= ($words_count>$ii) ? $like."'%$value%' OR " : $like."'%$value%' ";
				}
			if ($sea_pagdesc=='1')
				{
			$like = 'p.page_desc LIKE ';
			$sqlsearch3 .= ($words_count>$ii) ? $like."'%$value%' OR " : $like."'%$value%' ";
				}
			if ($sea_pagtext=='1')
				{
			$like = 'p.page_text LIKE ';
			$sqlsearch1 .= ($words_count>$ii) ? $like."'%$value%' OR " : $like."'%$value%' ";
				}
			if ($sea_frmtext=='1')
				{
			$like = 'p.fp_text LIKE ';
			$sqlsearchx1 .= ($words_count>$ii) ? $like."'%$value%' OR " : $like."'%$value%' ";
				}
			if ($sea_frmtitle=='1')
				{
			$like = 't.ft_title LIKE ';
			$sqlsearchx2 .= ($words_count>$ii) ? $like."'%$value%' OR " : $like."'%$value%' ";
				}
			}
			else
			{
			if ($sea_pagtitle=='1')
				{
			$like = 'p.page_title LIKE ';
			$sqlsearch2 .= ($words_count>$ii) ? $like."'%$value%' AND " : $like."'%$value%' ";
				}
			if ($sea_pagdesc=='1')
				{
			$like = 'p.page_desc LIKE ';
			$sqlsearch3 .= ($words_count>$ii) ? $like."'%$value%' AND " : $like."'%$value%' ";
				}
			if ($sea_pagtext=='1')
				{
			$like = 'p.page_text LIKE ';
			$sqlsearch1 .= ($words_count>$ii) ? $like."'%$value%' AND " : $like."'%$value%' ";
				}
			if ($sea_frmtext=='1')
				{
			$like = 'p.fp_text LIKE ';
			$sqlsearchx1 .= ($words_count>$ii) ? $like."'%$value%' AND " : $like."'%$value%' ";
				}
			if ($sea_frmtitle=='1')
				{
			$like = 't.ft_title LIKE ';
			$sqlsearchx2 .= ($words_count>$ii) ? $like."'%$value%' AND " : $like."'%$value%' ";
				}
			}
		}

		if (!$cfg['disable_page'] && !empty($a))
		{
			$pag_sub = sed_import('pag_sub','P','ARR');
			
	/*== Keep data ==*/
			
		if ($d==0 && empty($pre))
			{ $_SESSION['s_pag_sub'] = $pag_sub; }
		else
			{ $pag_sub = $_SESSION['s_pag_sub']; }
			
	/*== Keep data ==*/

			if ($pag_sub[0]=='all')
			{ $sqlsections = ''; }
			else
			{
				$sub = array();
				foreach($pag_sub as $i => $k)
				{ $sub[] = "page_cat='".sed_sql_prep($k)."'"; }
				$sqlsections = "AND (".implode(' OR ', $sub).")";
			}

			if ($sea_pagtitle=='1' && $sea_pagdesc!='1' && $sea_pagtext!='1') {
				$pagsql = "($sqlsearch2) AND ";
			}
			elseif ($sea_pagtitle=='1' && $sea_pagdesc=='1' && $sea_pagtext!='1') {
				$pagsql = "(($sqlsearch2) OR ($sqlsearch3)) AND ";
			}
			elseif ($sea_pagtitle=='1' && $sea_pagdesc!='1' && $sea_pagtext=='1') {
				$pagsql = "(($sqlsearch2) OR ($sqlsearch1)) AND ";
			}
			elseif ($sea_pagtitle!='1' && $sea_pagdesc=='1' && $sea_pagtext!='1') {
				$pagsql = "($sqlsearch3) AND ";
			}
			elseif ($sea_pagtitle!='1' && $sea_pagdesc=='1' && $sea_pagtext=='1') {
				$pagsql = "(($sqlsearch3) OR ($sqlsearch1)) AND ";
			}
			elseif ($sea_pagtitle!='1' && $sea_pagdesc!='1' && $sea_pagtext=='1') {
				$pagsql = "($sqlsearch1) AND ";
			}
			elseif ($sea_pagtitle=='1' && $sea_pagdesc=='1' && $sea_pagtext=='1') {
				$pagsql = "(($sqlsearch1) OR ($sqlsearch2) OR ($sqlsearch3)) AND ";
			}
			else {
				$pagsql = "";
				$plugin_body .= "<p>blahbla</p>";
				$cancel = "1";
			}

			if ($cancel!='1') {

				$sql  = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS page_id, page_ownerid, page_title, page_cat from $db_pages p, $db_structure s
				WHERE $pagsql
				p.page_state='0'
				AND p.page_cat=s.structure_code
				AND p.page_cat NOT LIKE 'system'
				$sqlsections ORDER by page_cat ASC, page_title ASC
				LIMIT $pag, ".$cfg['plugin']['search']['results']);
				$totalitems = mysql_fetch_row(sed_sql_query("SELECT FOUND_ROWS()"));
				$items = mysql_num_rows($sql);

				$plugin_body .= "<h4>".$L['Pages']." : ".$L['Poster']." ".sed_declension($items,$L['plu_match'])."</h4>";
				$hl = strtoupper($sq);
				
				$pagnav = sed_pagination(sed_url('plug','e=search&a=search&pre='.$sq.'&d='.$d), $pag, $totalitems[0], $cfg['plugin']['search']['results'], 'pag');
				list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('plug', 'e=search&a=search&pre='.$sq.'&d='.$d), $pag, $totalitems[0], $cfg['plugin']['search']['results'], TRUE, 'pag');

				$plugin_body .= "<table class=\"cells\" width=\"100%\"><tr><td width=\"30%\">".$L['Category']."</td><td width=\"60%\">".$L['Title']."</td><td width=\"10%\">".$L['Author']."</td></tr>";
				while ($row = mysql_fetch_array($sql))
				{
					if (sed_auth('page', $row['page_cat'], 'R'))
					{
						$ownername = sed_sql_fetcharray(sed_sql_query("SELECT user_name FROM $db_users WHERE user_id='".$row['page_ownerid']."'"));
						$plugin_body .= "<tr><td>".sed_build_catpath($row['page_cat'], '<a href="%1$s">%2$s</a>')."</td>";
						$plugin_body .= "<td><a href=\"".sed_url('page', 'id='.$row['page_id'].'&highlight='.$hl)."\">";
						$plugin_body .= sed_cc($row['page_title'])."</a></td><td>".sed_build_user($row['page_ownerid'],$ownername['user_name'])."</td></tr>";
					}
				}
				$plugin_body .= "</table>";
				$plugin_body .= $pagination_prev.$pagnav.$pagination_next;

				$sections++;
			}
		}

		if (!$cfg['disable_forums'] && !empty($a))
		{
			$frm_sub = sed_import('frm_sub','P','ARR');
			
	/*== Keep data ==*/
			
		if ($d==0 && empty($pre))
			{ $_SESSION['s_frm_sub'] = $frm_sub; }
		else
			{ $frm_sub = $_SESSION['s_frm_sub'];}
			
	/*== Keep data ==*/
			
			if ($frm_sub[0]==9999)
			{ $sqlsections = ''; }
			else
			{
				foreach($frm_sub as $i => $k)
				{ $sections1[] = "s.fs_id='".sed_sql_prep($k)."'"; }
				$sqlsections = "AND (".implode(' OR ', $sections1).")";
			}

			if ($sea_frmtitle=='1' && $sea_frmtext!='1') {
				$sql = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
		 	WHERE 1 AND ($sqlsearchx2)
		 	AND p.fp_topicid=t.ft_id
		 	AND p.fp_sectionid=s.fs_id $sqlsections $sqlsections2
		 	GROUP BY t.ft_id ORDER BY fp_id DESC
		 	LIMIT $d, ".$cfg['plugin']['search']['results']);
			$totalitems = mysql_fetch_row(sed_sql_query("SELECT FOUND_ROWS()"));
		 	$items = mysql_num_rows($sql);
			}
			elseif ($sea_frmtext=='1' && $sea_frmtitle!='1') {
				$sql = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
		 	WHERE 1 AND ($sqlsearchx1)
		 	AND p.fp_topicid=t.ft_id
		 	AND p.fp_sectionid=s.fs_id $sqlsections $sqlsections2
		 	GROUP BY t.ft_id ORDER BY fp_id DESC
		 	LIMIT $d, ".$cfg['plugin']['search']['results']);
			$totalitems = mysql_fetch_row(sed_sql_query("SELECT FOUND_ROWS()"));
		 	$items = mysql_num_rows($sql);
			}
			elseif ($sea_frmtext=='1' && $sea_frmtitle=='1') {
		$sql = sed_sql_query("SELECT SQL_CALC_FOUND_ROWS p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
			WHERE 1 AND ( ($sqlsearchx1) OR ($sqlsearchx2) )
			AND p.fp_topicid=t.ft_id
			AND p.fp_sectionid=s.fs_id $sqlsections $sqlsections2
			GROUP BY t.ft_id ORDER BY fp_id DESC
			LIMIT $d, ".$cfg['plugin']['search']['results']);
			$totalitems = mysql_fetch_row(sed_sql_query("SELECT FOUND_ROWS()"));
		 	$items = mysql_num_rows($sql);
			}
			else {
				$plugin_body .= "<p>blahbla</p>";
				$a = '';
				$items = "0";
			}

			if ($items!='0') {
				$plugin_body .= "<h4>".$L['Forums']." : ".$L['Poster']." ".sed_declension($items,$L['plu_match'])."</h4>";
				$hl = strtoupper($sq);
				
				$pagnav = sed_pagination(sed_url('plug','e=search&a=search&pre='.$sq.'&pag='.$pag), $d, $totalitems[0], $cfg['plugin']['search']['results']);
				list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('plug', 'e=search&a=search&pre='.$sq.'&pag='.$pag), $d, $totalitems[0], $cfg['plugin']['search']['results'], TRUE);

				$plugin_body .= "<table class=\"cells\" width=\"100%\"><tr><td width=\"30%\">".$L['Section']."</td><td width=\"60%\">".$L['Topic']."</td><td width=\"10%\">".$L['Poster']."</td></tr>";
				while ($row = mysql_fetch_array($sql))
				{
					if (sed_auth('forums', $row['fs_id'], 'R'))
					{
						$post_url = ($cfg['plugin']['search']['searchurls'] == 'Single') ? sed_url('forums', 'm=posts&id='.$row['fp_id'].'&highlight='.$hl) : sed_url('forums', 'm=posts&p='.$row['fp_id'].'&highlight='.$hl, '#'.$row['fp_id']);
						$plugin_body .= "<tr><td>".sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category'], TRUE)."</td><td><a href=\"".$post_url."\">".sed_cc($row['ft_title'])."</a></td><td>".sed_build_user($row['ft_firstposterid'],$row['ft_firstpostername'])."</td></tr>";
					}
				}
				$plugin_body .= "</table>";
				$plugin_body .= $pagination_prev.$pagnav.$pagination_next;
				
				$sections++;
			}
		}

	}
}

?>