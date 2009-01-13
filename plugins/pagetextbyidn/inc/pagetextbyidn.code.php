<?php
if (!defined('SED_CODE')){die('Wrong URL.'); }

$currenttpl = file_get_contents($mskin);
if (strpos($currenttpl, "{PAGE_TEXT_ID_")==true)// if tag exist on page
{
	unset($p_match);
	preg_match_all("#\{(PAGE_TEXT_ID_(\d*?))\}#", $currenttpl, $p_match);
	$numtags = count($p_match[0]);
	for($i = 0; $i<$numtags; $i++){
		$p_tag = $p_match[1][$i];
		$p_id = $p_match[2][$i];
		
		// Get page content by id in tag
		$psql = "SELECT * FROM $db_pages WHERE page_id='$p_id' LIMIT 1";
		$pres = sed_sql_query($psql);
		$prow = sed_sql_fetchassoc($pres);
		
		// Parse bbcode etc
		$page_html = "";
		$page_html = "";
		switch($prow['page_type']){
			case '1':
				$page_html = $prow['page_text'];
				break;
			
			case '2':
				
				if ($cfg['allowphp_pages']&&$cfg['allowphp_override']){
					ob_start();
					eval($prow['page_text']);
					$page_html = ob_get_clean();
				}else{
					$page_html = "The PHP mode is disabled for pages.<br />Please see the administration panel, then \"Configuration\", then \"Parsers\".";
				}
				break;
			
			default:
				if ($cfg['parser_cache']){
					if (empty($prow['page_html'])&&!empty($prow['page_text'])){
						$prow['page_html'] = sed_parse(sed_cc($prow['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
						sed_sql_query("UPDATE $db_pages SET page_html = '".sed_sql_prep($prow['page_html'])."' WHERE page_id = ".$prow['page_id']);
					}
					$html = $cfg['parsebbcodepages'] ? sed_post_parse($prow['page_html']) : sed_cc($prow['page_text']);
					$page_html = $html;
				}else{
					$text = sed_parse(sed_cc($prow['page_text']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], 1);
					$text = sed_post_parse($text, 'pages');
					$page_html = $text;
				}
				break;
		}
		
		// Tag [more] (<!--more--> in html)
		$pos_more = strpos($page_html, "<!--more-->");
		if ($pos_more!==false) $page_html = substr($page_html, 0, $pos_more)." <a href=\"page.php?id=$p_id\">".$L['ReadMore']."</a>";
		
		// Adding link to edit for admins
		if ($usr['isadmin']){
			$edit_link = $cfg['plugin']['pagetextbyidn']['link_to_edit'];
			$edit_link = str_replace("{HREF_EDIT}", "href='page.php?m=edit&id=$p_id'", $edit_link);
			if ($cfg['plugin']['pagetextbyidn']['where']=="before")
				$page_html = $edit_link." ".$page_html;
			if ($cfg['plugin']['pagetextbyidn']['where']=="after")
				$page_html = $page_html." ".$edit_link;
		}
		
		// Assign tag
		$t->assign($p_tag, $page_html);
	}
}
