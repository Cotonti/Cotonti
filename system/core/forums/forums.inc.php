<?PHP

/* ====================
 Seditio - Website engine
 Copyright Neocrome
 http://www.neocrome.net
 ==================== */

/**
 * @package Cotonti
 * @version 0.0.3
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('forums', 'any');
sed_block($usr['auth_read']);

$id = sed_import('id','G','INT');
$s = sed_import('s','G','ALP');
$q = sed_import('q','G','INT');
$p = sed_import('p','G','INT');
$d = sed_import('d','G','INT');
$o = sed_import('o','G','ALP');
$w = sed_import('w','G','ALP',4);
$c = sed_import('c','G','ALP');
$quote = sed_import('quote','G','INT');
$unread_done = FALSE;
$filter_cats = FALSE;
$ce = explode('_', $s);
$sys['sublocation'] = $L['Home'];

/* === Hook === */
$extp = sed_getextplugins('forums.sections.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if ($n=='markall' && $usr['id']>0)
{
    $sql = sed_sql_query("UPDATE $db_users set user_lastvisit='".$sys['now_offset']."' WHERE user_id='".$usr['id']."'");
    $usr['lastvisit'] = $sys['now_offset'];
}

$sql = sed_sql_query("SELECT s.*, n.* FROM $db_forum_sections AS s LEFT JOIN
    $db_forum_structure AS n ON n.fn_code=s.fs_category WHERE fs_masterid='0'
ORDER by fs_masterid DESC, fn_path ASC, fs_order ASC");

if (!$sed_sections_act)
{
    $timeback = $sys['now'] - 604800;
    $sqlact = sed_sql_query("SELECT fs_id FROM $db_forum_sections");

    while ($tmprow = sed_sql_fetcharray($sqlact))
    {
        $section = $tmprow['fs_id'];
        $sqltmp = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_creation>'$timeback' AND fp_sectionid='$section'");
        $sed_sections_act[$section] = sed_sql_result($sqltmp, 0, "COUNT(*)");
    }
    sed_cache_store('sed_sections_act', $sed_sections_act, 600);
}

if (!$sed_sections_vw)
{
    $sqltmp = sed_sql_query("SELECT online_subloc, COUNT(*) FROM $db_online WHERE online_location='Forums' GROUP BY online_subloc");

    while ($tmprow = sed_sql_fetcharray($sqltmp))
    {
        $sed_sections_vw[$tmprow['online_subloc']] = $tmprow['COUNT(*)'];
    }
    sed_cache_store('sed_sections_vw', $sed_sections_vw, 120);
}

unset($pcat);
$secact_max = max($sed_sections_act);
$out['markall'] = ($usr['id']>0) ? "<a href=\"".sed_url('forums', "n=markall")."\">".$L['for_markallasread']."</a>" : '';

$title_tags[] = array('{FORUM}');
$title_tags[] = array('%1$s');
$title_data = array($L['Forums']);
$out['subtitle'] = sed_title('title_forum_main', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('forums.sections.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate(sed_skinfile('forums.sections'));

if($cfg['homebreadcrumb'])
{
    $bhome = '<a href="'.$cfg['mainurl'].'">'.htmlspecialchars($cfg['maintitle']).'</a> '.$cfg['separator'].' ';
}
else
{
    $bhome = '';
}

$t->assign(array(
    "FORUMS_RSS" => sed_url('rss', 'c=forums'),
    "FORUMS_SECTIONS_PAGETITLE" => $bhome."<a href=\"".sed_url('forums')."\">".$L['Forums']."</a>",
    "FORUMS_SECTIONS_MARKALL" =>  $out['markall'],
    "FORUMS_SECTIONS_WHOSONLINE" => $out['whosonline']." : ".$out['whosonline_reg_list']
    ));

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('forums.sections.loop');
/* ===== */


while ($fsn = sed_sql_fetcharray($sql))
{

    $latestp = $fsn['fs_lt_date'];
    if ($pcat!=$fsn['fs_category'])
    {
        $pcat = $fsn['fs_category'];
        $sql2 = sed_sql_query("SELECT COUNT(*) FROM $db_forum_sections WHERE fs_category='$pcat'");
        $catnum = sed_sql_result($sql2, 0, "COUNT(*)");

        $cattitle = "<a href=\"".sed_url('forums')."#\" onclick=\"return toggleblock('blk_".$fsn['fs_category']."')\">";
        $cattitle .= htmlspecialchars($sed_forums_str[$fsn['fs_category']]['tpath']);
        $cattitle .= "</a>";

        if ($c=='fold')
        { $fold = TRUE; }
        elseif ($c=='unfold')
        { $fold = FALSE; }
        elseif (!empty($c))
        {
            $fold = ($c==$fsn['fs_category']) ? FALSE : TRUE;
        }
        else
        { $fold = (!$sed_forums_str[$fsn['fs_category']]['defstate']) ? TRUE : FALSE; }

        $fsn['toggle_state'] .= ($fold) ? " style=\"display:none;\"" : '';
        $fsn['toggle_body'] = "<tbody id=\"blk_".$fsn['fs_category']."\" ".$fsn['toggle_state'].">";

        $t-> assign(array(
            "FORUMS_SECTIONS_ROW_CAT_TITLE" => $cattitle,
            "FORUMS_SECTIONS_ROW_CAT_ICON" => $fsn['fn_icon'],
            "FORUMS_SECTIONS_ROW_CAT_SHORTTITLE" => htmlspecialchars($fsn['fn_title']),
            "FORUMS_SECTIONS_ROW_CAT_DESC" => sed_parse_autourls($fsn['fn_desc']),
            "FORUMS_SECTIONS_ROW_CAT_DEFSTATE" => htmlspecialchars($fsn['fn_defstate']),
            "FORUMS_SECTIONS_ROW_CAT_TBODY" => $fsn['toggle_body'],
            "FORUMS_SECTIONS_ROW_CAT_TBODY_END" => "</tbody>",
            "FORUMS_SECTIONS_ROW_CAT_CODE" => $fsn['fs_category'],
            ));
        $t->parse("MAIN.FORUMS_SECTIONS_ROW.FORUMS_SECTIONS_ROW_CAT");
    }

    if (sed_auth('forums', $fsn['fs_id'], 'R'))
    {
        $fsn['fs_topiccount_all'] = $fsn['fs_topiccount'] + $fsn['fs_topiccount_pruned'];
        $fsn['fs_postcount_all'] = $fsn['fs_postcount'] + $fsn['fs_postcount_pruned'];
        $fsn['fs_newposts'] = '0';
        $fsn['fs_desc'] = sed_parse_autourls($fsn['fs_desc']);
        $fsn['fs_desc'] .= ($fsn['fs_state']) ? " ".$L['Locked'] : '';
        $sed_sections_vw_cur = (!$sed_sections_vw[$fsn['fs_title']]) ? "0" : $sed_sections_vw[$fsn['fs_title']];

        if (!$fsn['fs_lt_id'])
        { sed_forum_sectionsetlast($fsn['fs_id']); }

        $fsn['fs_timago'] = sed_build_timegap($fsn['fs_lt_date'], $sys['now_offset']);

        if ($usr['id']>0 && $fsn['fs_lt_date']>$usr['lastvisit'] && $fsn['fs_lt_posterid']!=$usr['id'])
        {
            $fsn['fs_title'] = "+ ".$fsn['fs_title'];
            $fsn['fs_newposts'] = '1';
        }

        if ($fsn['fs_lt_id']>0)
        {
            $fsn['lastpost'] = ($usr['id']>0 && $fsn['fs_lt_date']>$usr['lastvisit'] && $fsn['fs_lt_posterid']!=$usr['id']) ? "<a href=\"".sed_url('forums', "m=posts&q=".$fsn['fs_lt_id']."&n=unread", "#unread")."\">" : "<a href=\"".sed_url('forums', "m=posts&q=".$fsn['fs_lt_id']."&n=last", "#bottom")."\">";
            $fsn['lastpost'] .= sed_cutstring($fsn['fs_lt_title'], 32)."</a>";
        }
        else
        {
            $fsn['lastpost'] = '&nbsp;';
            $fsn['fs_lt_date'] = '&nbsp;';
            $fsn['fs_lt_postername'] = '';
            $fsn['fs_lt_posterid'] = 0;
        }

        $fsn['fs_lt_date'] = ($fsn['fs_lt_date']>0) ? @date($cfg['formatmonthdayhourmin'], $fsn['fs_lt_date'] + $usr['timezone'] * 3600) : '';
        $fsn['fs_viewcount_short'] = ($fsn['fs_viewcount']>9999) ? floor($fsn['fs_viewcount']/1000)."k" : $fsn['fs_viewcount'];
        $fsn['fs_lt_postername'] = sed_build_user($fsn['fs_lt_posterid'], htmlspecialchars($fsn['fs_lt_postername']));

        if (!$secact_max)
        {
            $section_activity = '';
            $section_activity_img = '';
            $secact_num = 0;
        }
        else
        {
            $secact_num = round(6.25 * $sed_sections_act[$fsn['fs_id']] / $secact_max);
            if ($secact_num>5) { $secact_num = 5; }
            if (!$secact_num && $sed_sections_act[$fsn['fs_id']]>1) { $secact_num = 1; }
            $section_activity_img = "<img src=\"skins/".$skin."/img/system/activity".$secact_num.".gif\" alt=\"\" />";
        }

        $fs_num++;

        $t-> assign(array(
        "FORUMS_SECTIONS_ROW_ID" => $fsn['fs_id'],
        "FORUMS_SECTIONS_ROW_CAT" => $fsn['fs_category'],
        "FORUMS_SECTIONS_ROW_STATE" => $fsn['fs_state'],
        "FORUMS_SECTIONS_ROW_ORDER" => $fsn['fs_order'],
        "FORUMS_SECTIONS_ROW_TITLE" => $fsn['fs_title'],
        "FORUMS_SECTIONS_ROW_DESC" => $fsn['fs_desc'],
        "FORUMS_SECTIONS_ROW_ICON" => $fsn['fs_icon'],
        "FORUMS_SECTIONS_ROW_TOPICCOUNT" => $fsn['fs_topiccount'],
        "FORUMS_SECTIONS_ROW_POSTCOUNT" => $fsn['fs_postcount'],
        "FORUMS_SECTIONS_ROW_TOPICCOUNT_ALL" => $fsn['fs_topiccount_all'],
        "FORUMS_SECTIONS_ROW_POSTCOUNT_ALL" => $fsn['fs_postcount_all'],
        "FORUMS_SECTIONS_ROW_VIEWCOUNT" => $fsn['fs_viewcount'],
        "FORUMS_SECTIONS_ROW_VIEWCOUNT_SHORT" => $fsn['fs_viewcount_short'],
        "FORUMS_SECTIONS_ROW_VIEWERS" => $sed_sections_vw_cur,
        "FORUMS_SECTIONS_ROW_URL" => sed_url('forums', "m=topics&s=".$fsn['fs_id']),
        "FORUMS_SECTIONS_ROW_LASTPOSTDATE" => $fsn['fs_lt_date'],
        "FORUMS_SECTIONS_ROW_LASTPOSTER" => $fsn['fs_lt_postername'],
        "FORUMS_SECTIONS_ROW_LASTPOST" => $fsn['lastpost'],
        "FORUMS_SECTIONS_ROW_TIMEAGO" => $fsn['fs_timago'],
        "FORUMS_SECTIONS_ROW_ACTIVITY" => $section_activity_img,
        "FORUMS_SECTIONS_ROW_ACTIVITYVALUE" => $secact_num,
        "FORUMS_SECTIONS_ROW_NEWPOSTS" => $fsn['fs_newposts'],
        "FORUMS_SECTIONS_ROW_ODDEVEN" => sed_build_oddeven($fs_num),
        "FORUMS_SECTIONS_ROW_NUM" => $fs_num,
        "FORUMS_SECTIONS_ROW" => $fsn
            ));

        $ii = 0;
        $sql1 = sed_sql_query("SELECT fs_id, fs_title, fs_lt_date FROM $db_forum_sections WHERE fs_masterid='".$fsn['fs_id']."' ");
        while ($row = sed_sql_fetcharray($sql1))
        {

            if ($row['fs_lt_date']>$latestp)
            {
                $sql0 = sed_sql_query("SELECT fs_lt_id, fs_lt_title, fs_lt_posterid, fs_lt_postername FROM $db_forum_sections WHERE fs_id='".$row['fs_id']."' ");
                $fsnn = sed_sql_fetcharray($sql0);

                $fsnn['fs_lt_date'] = @date($cfg['formatmonthdayhourmin'], $row['fs_lt_date'] + $usr['timezone'] * 3600);

                $fsnn['lastpost'] = "<a href=\"".sed_url('forums', "m=posts&q=".$fsnn['fs_lt_id']."&n=last", "#bottom")."\">";
                $fsnn['lastpost'] .= sed_cutstring($fsnn['fs_lt_title'], 32)."</a>";

                $fsnn['fs_timago'] = sed_build_timegap($row['fs_lt_date'], $sys['now_offset']);

                $t-> assign(array(
                    "FORUMS_SECTIONS_ROW_LASTPOSTDATE" => $fsnn['fs_lt_date'],
                    "FORUMS_SECTIONS_ROW_LASTPOSTER" => sed_build_user($fsnn['fs_lt_posterid'], htmlspecialchars($fsnn['fs_lt_postername'])),
                    "FORUMS_SECTIONS_ROW_LASTPOST" => $fsnn['lastpost'],
                    "FORUMS_SECTIONS_ROW_TIMEAGO" => $fsnn['fs_timago']
                    ));

                $latestp = $row['fs_lt_date'];

            }

            $j = ($row['fs_lt_date']>$usr['lastvisit']) ? '+ ' : '';
            $ii++;
            $t->assign(array(
                "FORUMS_SECTIONS_ROW_SLAVE_ID" => $row['fs_id'],
                "FORUMS_SECTIONS_ROW_SLAVE_TITLE" => $row['fs_title'],
                "FORUMS_SECTIONS_ROW_SLAVE_DESC" => $row['fs_desc'],
                "FORUMS_SECTIONS_ROW_SLAVE_ICON" => $row['fs_icon'],
                "FORUMS_SECTIONS_ROW_SLAVE_NEW" => $j,
                "FORUMS_SECTIONS_ROW_SLAVE_URL" => sed_url('forums', "m=topics&s=".$row['fs_id']),
                "FORUMS_SECTIONS_ROW_SLAVE_ODDEVEN" => sed_build_oddeven($ii),
                "FORUMS_SECTIONS_ROW_SLAVE_NUM" => $ii,
                "FORUMS_SECTIONS_ROW_SLAVE" => $row,
                "FORUMS_SECTIONS_ROW_SLAVEI" => "<a href=\"".sed_url('forums', "m=topics&s=".$row['fs_id'])."\">".$j.htmlspecialchars($row['fs_title'])."</a>",
                ));

            $t->parse("MAIN.FORUMS_SECTIONS_ROW.FORUMS_SECTIONS_ROW_SECTION.FORUMS_SECTIONS_ROW_SECTION_SLAVES");
        }

        /* === Hook - Part2 : Include === */
        if (is_array($extp))
        { foreach($extp as $k => $pl) { include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
        /* ===== */

        $t->parse("MAIN.FORUMS_SECTIONS_ROW.FORUMS_SECTIONS_ROW_SECTION");
    }

    // Required to have all divs closed
    $catnum = $catnum-1;
    if (!$catnum)
    {
        $t->parse("MAIN.FORUMS_SECTIONS_ROW.FORUMS_SECTIONS_ROW_CAT_FOOTER");
    }

    $t->parse("MAIN.FORUMS_SECTIONS_ROW");

}

/* === Hook === */
$extp = sed_getextplugins('forums.sections.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>