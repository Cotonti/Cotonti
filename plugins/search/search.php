<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=plugins/search/search.php
Version=125
Date=2008-jun-04
Type=Plugin
Author=Olivier C. & Spartan
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=search
Part=main
File=search
Hooks=standalone
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

if (!defined('SED_CODE') || !defined('SED_PLUG')) { die('Wrong URL.'); }

/* === EDIT THE FOLLOWING === */

$cfg_maxwords = 5;
$cfg_maxwords_frm = 5;
$cfg_maxwords_pag = 5;
$cfg_maxitems = 50;

/* === DO NOT EDIT THE FOLLOWING === */

$sq = sed_import('sq','P','TXT');
$pre = sed_import('pre','G','TXT');
$a = sed_import('a','G','TXT');
$tab = sed_import('tab','G','TXT');
$frm = sed_import('frm','G','BOL');

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

$sq = (!empty($pre)) ? $pre : $sq;

$plugin_title = $L['plu_title_frmtab'];

$plugin_subtitle .= "<a href=\"plug.php?e=search\">".$L['plu_tabs_all']."</a> &nbsp; ".$L['plu_tabs_frm']." &nbsp; <a href=\"plug.php?e=search&amp;tab=pag\">".$L['plu_tabs_pag']."</a>";
$plugin_subtitle .= "<br>".$L['plu_title_frmtab_s'];

$plugin_body .= "<form id=\"search\" action=\"plug.php?e=search&amp;tab=frm&amp;a=search\" method=\"post\">";
$plugin_body .= "<table class=\"cells\">";
$plugin_body .= "<tr><td width=\"20%\">".$L['plu_searchin1']."</td>";
$plugin_body .= "<td width=\"80%\"><input type=\"text\" class=\"text\" name=\"sq\" value=\"".sed_cc($sq)."\" size=\"16\" maxlength=\"32\" />".$L['plu_searchin2']."</td></tr>";

if (!$cfg['disable_forums'])
	{

	$sql1 = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s
		LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category
    	ORDER by fn_path ASC, fs_order ASC");

	$plugin_body .= "<tr><td>";
	$plugin_body .= $L['Forums']."<br />".$L['plu_frm_hint']."</td><td><select multiple name=\"frm_sub[]\" size=\"5\">";
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
	$plugin_body .= "<option value=\"new\" select=\"selected\">".$L['And newer']."</option>";
	$plugin_body .= "<option value=\"old\">".$L['And older']."</option></select></td></tr>";
	$plugin_body .= "<tr><td>".$L['Sort results by']."</td><td><select name=\"sea_frmsort\"><option value=\"1\" selected=\"selected\">".$L['Last updated']."</option>";
	$plugin_body .= "<option value=\"2\">".$L['Creation date']."</option>";
	$plugin_body .= "<option value=\"3\">".$L['Title']."</option>";
	$plugin_body .= "<option value=\"4\">".$L['Number of replies']."</option>";
	$plugin_body .= "<option value=\"5\">".$L['Number of views']."</option>";
	$plugin_body .= "</select><select name=\"sea_frmsort2\">";
	$plugin_body .= "<option value=\"DESC\" select=\"selected\">".$L['Descending']."</option>";
	$plugin_body .= "<option value=\"ASC\">".$L['Ascending']."</option></select></td></tr>";
	$plugin_body .= "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" class=\"submit\" value=\"".$L['Search']."\" /></td></tr>";
	}

$plugin_body .= "</table></form>";

if ($a=='search')
	{
	if (strlen($sq)<3)
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

	$sqlsearch = implode("%", $words);
	$sqlsearch = "%".$sqlsearch."%";

	if (!$cfg['disable_forums'])
		{
		$frm_sub = sed_import('frm_sub','P','ARR');

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
		$sql = sed_sql_query("SELECT p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
			WHERE 1 AND (t.ft_title LIKE '".sed_sql_prep($sqlsearch)."')
			AND p.fp_topicid=t.ft_id $frm_reply
			AND p.fp_sectionid=s.fs_id $sqlsections $sqlsections2
			GROUP BY t.ft_id ORDER BY $orderby
			LIMIT $cfg_maxitems");
		$items = mysql_num_rows($sql);
		}
		elseif ($sea_frmtext=='1' && $sea_frmtitle!='1') {
		$sql = sed_sql_query("SELECT p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
			WHERE 1 AND (p.fp_text LIKE '".sed_sql_prep($sqlsearch)."')
			AND p.fp_topicid=t.ft_id $frm_reply
			AND p.fp_sectionid=s.fs_id $sqlsections $sqlsections2
			GROUP BY t.ft_id ORDER BY $orderby
			LIMIT $cfg_maxitems");
		$items = mysql_num_rows($sql);
		}
		elseif ($sea_frmtext=='1' && $sea_frmtitle=='1') {
		$sql = sed_sql_query("SELECT p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
			WHERE 1 AND (p.fp_text LIKE '".sed_sql_prep($sqlsearch)."' OR t.ft_title LIKE '".sed_sql_prep($sqlsearch)."')
			AND p.fp_topicid=t.ft_id $frm_reply
			AND p.fp_sectionid=s.fs_id $sqlsections $sqlsections2
			GROUP BY t.ft_id ORDER BY $orderby
			LIMIT $cfg_maxitems");
		$items = mysql_num_rows($sql);
		}
		else {
		$plugin_body .= "<p>blahbla</p>";
		$a = '';
		$items = "0";
		}

		if ($items!='0') {
		$plugin_body .= "<h4>".$L['Forums']." : ".$L['plu_found']." ".$items." ".$L['plu_match']."</h4>";

		$plugin_body .= "<table class=\"cells\" width=\"100%\"><tr><td width=\"30%\">".$L['plu_fs']."</td><td width=\"60%\">".$L['plu_ft']."</td><td width=\"10%\">".$L['plu_fo']."</td></tr>";
		while ($row = mysql_fetch_array($sql))
			{
			if (sed_auth('forums', $row['fs_id'], 'R'))
				{
				$plugin_body .= "<tr><td>".sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category'], TRUE)."</td><td><a href=\"forums.php?m=posts&amp;p=".$row['fp_id']."#".$row['fp_id']."\">".sed_cc($row['ft_title'])."</a></td><td>".sed_build_user($row['ft_firstposterid'],$row['ft_firstpostername'])."</td></tr>";
				}
			}
			$plugin_body .= "</table>";
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

$sq = (!empty($pre)) ? $pre : $sq;

$plugin_title = $L['plu_title_pagtab'];

$plugin_subtitle .= "<a href=\"plug.php?e=search\">".$L['plu_tabs_all']."</a> &nbsp; <a href=\"plug.php?e=search&amp;tab=frm\">".$L['plu_tabs_frm']."</a> &nbsp; ".$L['plu_tabs_pag'];
$plugin_subtitle .= "<br>".$L['plu_title_pagtab_s'];

$plugin_body .= "<form id=\"search\" action=\"plug.php?e=search&amp;tab=pag&amp;a=search\" method=\"post\">";
$plugin_body .= "<table class=\"cells\">";
$plugin_body .= "<tr><td width=\"20%\">".$L['plu_searchin1']."</td>";
$plugin_body .= "<td width=\"80%\"><input type=\"text\" class=\"text\" name=\"sq\" value=\"".sed_cc($sq)."\" size=\"16\" maxlength=\"32\" />".$L['plu_searchin2']."</td></tr>";

if (!$cfg['disable_page'])
	{
	$plugin_body .= "<tr><td>";
	$plugin_body .= $L['Pages']."<br />".$L['plu_pag_hint']."</td><td><select multiple name=\"pag_sub[]\" size=\"5\">";
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
	$plugin_body .= "<option value=\"new\" select=\"selected\">".$L['And newer']."</option>";
	$plugin_body .= "<option value=\"old\">".$L['And older']."</option></select></td></tr>";
	$plugin_body .= "<tr><td>".$L['Sort results by']."</td><td><select name=\"sea_pagsort\"><option value=\"1\" selected=\"selected\">".$L['Creation date']."</option>";
	$plugin_body .= "<option value=\"2\">".$L['Title']."</option>";
	$plugin_body .= "<option value=\"3\">".$L['Number of views']."</option>";
	$plugin_body .= "</select><select name=\"sea_pagsort2\">";
	$plugin_body .= "<option value=\"DESC\" select=\"selected\">".$L['Descending']."</option>";
	$plugin_body .= "<option value=\"ASC\">".$L['Ascending']."</option></select></td></tr>";
	$plugin_body .= "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" class=\"submit\" value=\"".$L['Search']."\" /></td></tr>";
	}

$plugin_body .= "</table></form>";

if ($a=='search')
	{
	if (strlen($sq)<3)
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

	$sqlsearch = implode("%", $words);
	$sqlsearch = "%".$sqlsearch."%";

	if (!$cfg['disable_page'])
		{
		$pag_sub = sed_import('pag_sub','P','ARR');

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
		$pagsql = "(p.page_title LIKE '".$sqlsearch."') AND ";
		}
		elseif ($sea_pagtitle=='1' && $sea_pagdesc=='1' && $sea_pagtext!='1') {
		$pagsql = "(p.page_title LIKE '".$sqlsearch."' OR p.page_desc LIKE '".$sqlsearch."') AND ";
		}
		elseif ($sea_pagtitle=='1' && $sea_pagdesc!='1' && $sea_pagtext=='1') {
		$pagsql = "(p.page_title LIKE '".$sqlsearch."' OR p.page_text LIKE '".sed_sql_prep($sqlsearch)."') AND ";
		}
		elseif ($sea_pagtitle!='1' && $sea_pagdesc=='1' && $sea_pagtext!='1') {
		$pagsql = "(p.page_desc LIKE '".$sqlsearch."') AND ";
		}
		elseif ($sea_pagtitle!='1' && $sea_pagdesc=='1' && $sea_pagtext=='1') {
		$pagsql = "(p.page_desc LIKE '".$sqlsearch."' OR p.page_text LIKE '".sed_sql_prep($sqlsearch)."') AND ";
		}
		elseif ($sea_pagtitle!='1' && $sea_pagdesc!='1' && $sea_pagtext=='1') {
		$pagsql = "(p.page_text LIKE '".sed_sql_prep($sqlsearch)."') AND ";
		}
		elseif ($sea_pagtitle=='1' && $sea_pagdesc=='1' && $sea_pagtext=='1') {
		$pagsql = "(p.page_text LIKE '".$sqlsearch."' OR p.page_title LIKE '".$sqlsearch."' OR p.page_desc LIKE '".sed_sql_prep($sqlsearch)."') AND ";
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
       	 $sql  = sed_sql_query("SELECT page_id, page_ownerid, page_title, page_cat from $db_pages p, $db_structure s
   	 		WHERE $pagsql
			p.page_file='1'
       	 	AND p.page_state='0'
       	 	AND p.page_cat=s.structure_code
       	 	AND p.page_cat NOT LIKE 'system' $sqlsections2
       	 	$sqlsections ORDER by $orderby
       	 	LIMIT $cfg_maxitems");
		$items = mysql_num_rows($sql);
		} else {
       	 $sql  = sed_sql_query("SELECT page_id, page_ownerid, page_title, page_cat from $db_pages p, $db_structure s
   	 		WHERE $pagsql
       	 	p.page_state='0'
       	 	AND p.page_cat=s.structure_code
       	 	AND p.page_cat NOT LIKE 'system' $sqlsections2
       	 	$sqlsections ORDER by $orderby
       	 	LIMIT $cfg_maxitems");
		$items = mysql_num_rows($sql);
		}

		$plugin_body .= "<h4>".$L['Pages']." : ".$L['plu_found']." ".$items." ".$L['plu_match']."</h4>";

		$plugin_body .= "<table class=\"cells\" width=\"100%\"><tr><td width=\"30%\">".$L['plu_ps']."</td><td width=\"60%\">".$L['plu_pt']."</td><td width=\"10%\">".$L['plu_po']."</td></tr>";
		while ($row = mysql_fetch_array($sql))
			{
			if (sed_auth('page', $row['page_cat'], 'R'))
				{
				$ownername = sed_sql_fetcharray(sed_sql_query("SELECT user_name FROM $db_users WHERE user_id='".$row['page_ownerid']."'"));
				$plugin_body .= "<tr><td><a href=\"list.php?c=".$row['page_cat']."\">".$sed_cat[$row['page_cat']]['tpath']."</a></td>";
				$plugin_body .= "<td><a href=\"page.php?id=".$row['page_id']."\">";
				$plugin_body .= sed_cc($row['page_title'])."</a></td><td>".sed_build_user($row['page_ownerid'],$ownername['user_name'])."</td></tr>";
				}
			}
			$plugin_body .= "</table>";
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

$sq = (!empty($pre)) ? $pre : $sq;

$plugin_title = $L['plu_title_alltab'];

$plugin_subtitle .= $L['plu_tabs_all']."</b> &nbsp; <a href=\"plug.php?e=search&amp;tab=frm\">".$L['plu_tabs_frm']."</a> &nbsp; <a href=\"plug.php?e=search&amp;tab=pag\">".$L['plu_tabs_pag']."</a>";
$plugin_subtitle .= "<br>".$L['plu_title_alltab_s'];

$plugin_body .= "<form id=\"search\" action=\"plug.php?e=search&amp;a=search\" method=\"post\">";
$plugin_body .= "<table class=\"cells\">";
$plugin_body .= "<tr><td width=\"20%\">".$L['plu_searchin1']."</td>";
$plugin_body .= "<td width=\"80%\"><input type=\"text\" class=\"text\" name=\"sq\" value=\"".sed_cc($sq)."\" size=\"16\" maxlength=\"32\" />".$L['plu_searchin2']."</td></tr>";

if (!$cfg['disable_forums'])
	{

	$sql1 = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s
		LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category
    	ORDER by fn_path ASC, fs_order ASC");

	$plugin_body .= "<tr><td>";
	$plugin_body .= $L['Forums']."<br />".$L['plu_frm_hint']."</td><td><select multiple name=\"frm_sub[]\" size=\"5\">";
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
	$plugin_body .= $L['Pages']."<br />".$L['plu_pag_hint']."</td><td><select multiple name=\"pag_sub[]\" size=\"5\">";
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
	if (strlen($sq)<3)
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

	$sqlsearch = implode("%", $words);
	$sqlsearch = "%".$sqlsearch."%";

	if (!$cfg['disable_page'])
		{
		$pag_sub = sed_import('pag_sub','P','ARR');

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
		$pagsql = "(p.page_title LIKE '".$sqlsearch."') AND ";
		}
		elseif ($sea_pagtitle=='1' && $sea_pagdesc=='1' && $sea_pagtext!='1') {
		$pagsql = "(p.page_title LIKE '".$sqlsearch."' OR p.page_desc LIKE '".$sqlsearch."') AND ";
		}
		elseif ($sea_pagtitle=='1' && $sea_pagdesc!='1' && $sea_pagtext=='1') {
		$pagsql = "(p.page_title LIKE '".$sqlsearch."' OR p.page_text LIKE '".sed_sql_prep($sqlsearch)."') AND ";
		}
		elseif ($sea_pagtitle!='1' && $sea_pagdesc=='1' && $sea_pagtext!='1') {
		$pagsql = "(p.page_desc LIKE '".$sqlsearch."') AND ";
		}
		elseif ($sea_pagtitle!='1' && $sea_pagdesc=='1' && $sea_pagtext=='1') {
		$pagsql = "(p.page_desc LIKE '".$sqlsearch."' OR p.page_text LIKE '".sed_sql_prep($sqlsearch)."') AND ";
		}
		elseif ($sea_pagtitle!='1' && $sea_pagdesc!='1' && $sea_pagtext=='1') {
		$pagsql = "(p.page_text LIKE '".sed_sql_prep($sqlsearch)."') AND ";
		}
		elseif ($sea_pagtitle=='1' && $sea_pagdesc=='1' && $sea_pagtext=='1') {
		$pagsql = "(p.page_text LIKE '".$sqlsearch."' OR p.page_title LIKE '".$sqlsearch."' OR p.page_desc LIKE '".sed_sql_prep($sqlsearch)."') AND ";
		}
		else {
		$pagsql = "";
		$plugin_body .= "<p>blahbla</p>";
		$cancel = "1";
		}

		if ($cancel!='1') {

       	 $sql  = sed_sql_query("SELECT page_id, page_ownerid, page_title, page_cat from $db_pages p, $db_structure s
   	 		WHERE $pagsql
       	 	p.page_state='0'
       	 	AND p.page_cat=s.structure_code
       	 	AND p.page_cat NOT LIKE 'system'
       	 	$sqlsections ORDER by page_cat ASC, page_title ASC
       	 	LIMIT $cfg_maxitems");
		$items = mysql_num_rows($sql);

		$plugin_body .= "<h4>".$L['Pages']." : ".$L['plu_found']." ".$items." ".$L['plu_match']."</h4>";

		$plugin_body .= "<table class=\"cells\" width=\"100%\"><tr><td width=\"30%\">".$L['plu_ps']."</td><td width=\"60%\">".$L['plu_pt']."</td><td width=\"10%\">".$L['plu_po']."</td></tr>";
		while ($row = mysql_fetch_array($sql))
			{
			if (sed_auth('page', $row['page_cat'], 'R'))
				{
				$ownername = sed_sql_fetcharray(sed_sql_query("SELECT user_name FROM $db_users WHERE user_id='".$row['page_ownerid']."'"));
				$plugin_body .= "<tr><td><a href=\"list.php?c=".$row['page_cat']."\">".$sed_cat[$row['page_cat']]['tpath']."</a></td>";
				$plugin_body .= "<td><a href=\"page.php?id=".$row['page_id']."\">";
				$plugin_body .= sed_cc($row['page_title'])."</a></td><td>".sed_build_user($row['page_ownerid'],$ownername['user_name'])."</td></tr>";
				}
			}
			$plugin_body .= "</table>";
		$sections++;
		}
		}

	if (!$cfg['disable_forums'])
		{
		$frm_sub = sed_import('frm_sub','P','ARR');

		if ($frm_sub[0]==9999)
			{ $sqlsections = ''; }
	       else
	       	{
			foreach($frm_sub as $i => $k)
   				{ $sections1[] = "s.fs_id='".sed_sql_prep($k)."'"; }
			$sqlsections = "AND (".implode(' OR ', $sections1).")";
			}

		if ($sea_frmtitle=='1' && $sea_frmtext!='1') {
		$sql = sed_sql_query("SELECT p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
			WHERE 1 AND (t.ft_title LIKE '".sed_sql_prep($sqlsearch)."')
			AND p.fp_topicid=t.ft_id
			AND p.fp_sectionid=s.fs_id $sqlsections
			GROUP BY t.ft_id ORDER BY fp_id DESC
			LIMIT $cfg_maxitems");
		$items = mysql_num_rows($sql);
		}
		elseif ($sea_frmtext=='1' && $sea_frmtitle!='1') {
		$sql = sed_sql_query("SELECT p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
			WHERE 1 AND (p.fp_text LIKE '".sed_sql_prep($sqlsearch)."')
			AND p.fp_topicid=t.ft_id
			AND p.fp_sectionid=s.fs_id $sqlsections
			GROUP BY t.ft_id ORDER BY fp_id DESC
			LIMIT $cfg_maxitems");
		$items = mysql_num_rows($sql);
		}
		elseif ($sea_frmtext=='1' && $sea_frmtitle=='1') {
		$sql = sed_sql_query("SELECT p.fp_id, t.ft_firstposterid, t.ft_firstpostername, t.ft_title, t.ft_id, s.fs_id, s.fs_title, s.fs_category
		 	FROM $db_forum_posts p, $db_forum_topics t, $db_forum_sections s
			WHERE 1 AND (p.fp_text LIKE '".sed_sql_prep($sqlsearch)."' OR t.ft_title LIKE '".sed_sql_prep($sqlsearch)."')
			AND p.fp_topicid=t.ft_id
			AND p.fp_sectionid=s.fs_id $sqlsections
			GROUP BY t.ft_id ORDER BY fp_id DESC
			LIMIT $cfg_maxitems");
		$items = mysql_num_rows($sql);
		}
		else {
		$plugin_body .= "<p>blahbla</p>";
		$a = '';
		$items = "0";
		}

		if ($items!='0') {
		$plugin_body .= "<h4>".$L['Forums']." : ".$L['plu_found']." ".$items." ".$L['plu_match']."</h4>";

		$plugin_body .= "<table class=\"cells\" width=\"100%\"><tr><td width=\"30%\">".$L['plu_fs']."</td><td width=\"60%\">".$L['plu_ft']."</td><td width=\"10%\">".$L['plu_fo']."</td></tr>";
		while ($row = mysql_fetch_array($sql))
			{
			if (sed_auth('forums', $row['fs_id'], 'R'))
				{
				$plugin_body .= "<tr><td>".sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category'], TRUE)."</td><td><a href=\"forums.php?m=posts&amp;p=".$row['fp_id']."#".$row['fp_id']."\">".sed_cc($row['ft_title'])."</a></td><td>".sed_build_user($row['ft_firstposterid'],$row['ft_firstpostername'])."</td></tr>";
				}
			}
			$plugin_body .= "</table>";
		$sections++;
		}
		}

	}
}
?>


