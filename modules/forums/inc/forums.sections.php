<?php

/* ====================
 Seditio - Website engine
 Copyright Neocrome
 http://www.neocrome.net
 ==================== */

/**
 * @package forums
 * @version 0.7.0
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('forums', 'any');
/* === Hook === */
foreach (cot_getextplugins('forums.sections.rights') as $pl)
{
	include $pl;
}
/* ===== */
cot_block($usr['auth_read']);

$s = cot_import('s','G','ALP');
$c = cot_import('c','G','ALP');
$ce = explode('_', $s);
$sys['sublocation'] = $L['Home'];

/* === Hook === */
foreach (cot_getextplugins('forums.sections.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($n=='markall' && $usr['id']>0)
{
    $sql = $db->query("UPDATE $db_users set user_lastvisit='".$sys['now_offset']."' WHERE user_id='".$usr['id']."'");
    $usr['lastvisit'] = $sys['now_offset'];
}

$sql = $db->query("SELECT s.*, n.* FROM $db_forum_sections AS s LEFT JOIN
    $db_forum_structure AS n ON n.fn_code=s.fs_category WHERE fs_masterid='0'
ORDER by fs_masterid DESC, fn_path ASC, fs_order ASC");

if (!$cot_sections_act)
{
    $timeback = $sys['now'] - 604800;
    $sqlact = $db->query("SELECT fs_id FROM $db_forum_sections");

    while ($tmprow = $sqlact->fetch())
    {
        $section = $tmprow['fs_id'];
        $sqltmp = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_creation>'$timeback' AND fp_sectionid='$section'");
        $cot_sections_act[$section] = $sqltmp->fetchColumn();
    }
    $cache && $cache->db->store('cot_sections_act', $cot_sections_act, 'system', 600);
}

$cache && $cache->mem && $cot_sections_vw = $cache->mem->get('sections_wv', 'forums');
if (!$cot_sections_vw)
{
    $sqltmp = $db->query("SELECT online_subloc, COUNT(*) FROM $db_online WHERE online_location='Forums' GROUP BY online_subloc");

    while ($tmprow = $sqltmp->fetch())
    {
        $cot_sections_vw[$tmprow['online_subloc']] = $tmprow['COUNT(*)'];
    }
    $cache && $cache->mem && $cache->mem->store('sections_vw', $cot_sections_vw, 'forums', 120);
}

unset($pcat);
$secact_max = max($cot_sections_act);
$out['markall'] = ($usr['id']>0) ? cot_rc_link(cot_url('forums', "n=markall"), $L['for_markallasread']) : '';

$title_params = array(
	'FORUM' => $L['Forums']
);
$out['subtitle'] = cot_title('title_forum_main', $title_params);

/* === Hook === */
foreach (cot_getextplugins('forums.sections.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate(cot_skinfile('forums.sections'));

if($cfg['homebreadcrumb'])
{
    $bhome = cot_rc_link($cfg['mainurl'], htmlspecialchars($cfg['maintitle'])).$cfg['separator'].' ';
}
else
{
    $bhome = '';
}

$t->assign(array(
    "FORUMS_RSS" => cot_url('rss', 'c=forums'),
    "FORUMS_SECTIONS_PAGETITLE" => $bhome.cot_rc_link(cot_url('forums'), $L['Forums']),
    "FORUMS_SECTIONS_MARKALL" =>  $out['markall'],
    "FORUMS_SECTIONS_WHOSONLINE" => $out['whosonline']." : ".$out['whosonline_reg_list']
    ));

/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('forums.sections.loop');
/* ===== */


while ($fsn = $sql->fetch())
{

    $latestp = $fsn['fs_lt_date'];
    if ($pcat!=$fsn['fs_category'])
    {
        $pcat = $fsn['fs_category'];
        $sql2 = $db->query("SELECT COUNT(*) FROM $db_forum_sections WHERE fs_category='$pcat' AND fs_masterid=0");
        $catnum = $sql2->fetchColumn();

        $cattitle = cot_rc_link(cot_url('forums'), htmlspecialchars($cot_forums_str[$fsn['fs_category']]['tpath']),  "onclick=\"return toggleblock('blk_".$fsn['fs_category']."')'\"");

        if ($c=='fold')
        { $fold = TRUE; }
        elseif ($c=='unfold')
        { $fold = FALSE; }
        elseif (!empty($c))
        {
            $fold = ($c==$fsn['fs_category']) ? FALSE : TRUE;
        }
        else
        { $fold = (!$cot_forums_str[$fsn['fs_category']]['defstate']) ? TRUE : FALSE; }

        $t-> assign(array(
            "FORUMS_SECTIONS_ROW_CAT_TITLE" => $cattitle,
            "FORUMS_SECTIONS_ROW_CAT_ICON" => $fsn['fn_icon'],
            "FORUMS_SECTIONS_ROW_CAT_SHORTTITLE" => htmlspecialchars($fsn['fn_title']),
            "FORUMS_SECTIONS_ROW_CAT_DESC" => cot_parse_autourls($fsn['fn_desc']),
            "FORUMS_SECTIONS_ROW_CAT_DEFSTATE" => htmlspecialchars($fsn['fn_defstate']),
            "FORUMS_SECTIONS_ROW_CAT_TBODY" => cot_rc('frm_code_tbody_begin', array('cat' => $fsn['fs_category'], 'style' => ($fold ? 'style="display:none"' : ''))),
            "FORUMS_SECTIONS_ROW_CAT_TBODY_END" => $R['frm_code_tbody_end'],
            "FORUMS_SECTIONS_ROW_CAT_CODE" => $fsn['fs_category'],
            ));
        $t->parse("MAIN.FORUMS_SECTIONS_ROW.FORUMS_SECTIONS_ROW_CAT");
    }

    if (cot_auth('forums', $fsn['fs_id'], 'R'))
    {
        $fsn['fs_topiccount_all'] = $fsn['fs_topiccount'] + $fsn['fs_topiccount_pruned'];
        $fsn['fs_postcount_all'] = $fsn['fs_postcount'] + $fsn['fs_postcount_pruned'];
        $fsn['fs_newposts'] = '0';
        $fsn['fs_desc'] = cot_parse_autourls($fsn['fs_desc']);
        $fsn['fs_desc'] .= ($fsn['fs_state']) ? " ".$L['Locked'] : '';
        $cot_sections_vw_cur = (!$cot_sections_vw[$fsn['fs_title']]) ? "0" : $cot_sections_vw[$fsn['fs_title']];

        if (!$fsn['fs_lt_id'])
        { cot_forum_sectionsetlast($fsn['fs_id']); }

        $fsn['fs_timago'] = cot_build_timegap($fsn['fs_lt_date'], $sys['now_offset']);

        if ($usr['id']>0 && $fsn['fs_lt_date']>$usr['lastvisit'] && $fsn['fs_lt_posterid']!=$usr['id'])
        {
            $fsn['fs_title'] = "+ ".$fsn['fs_title'];
            $fsn['fs_newposts'] = '1';
        }

        if ($fsn['fs_lt_id']>0)
        {
            $fsn['lastpost'] = cot_rc_link(($usr['id']>0 && $fsn['fs_lt_date']>$usr['lastvisit'] && $fsn['fs_lt_posterid']!=$usr['id']) ? cot_url('forums', "m=posts&q=".$fsn['fs_lt_id']."&n=unread", "#unread") : cot_url('forums', "m=posts&q=".$fsn['fs_lt_id']."&n=last", "#bottom"), cot_cutstring($fsn['fs_lt_title'], 32));
        }
        else
        {
            $fsn['lastpost'] = $R['frm_code_post_empty'];
            $fsn['fs_lt_date'] = $R['frm_code_post_empty'];
            $fsn['fs_lt_postername'] = '';
            $fsn['fs_lt_posterid'] = 0;
        }

        $fsn['fs_lt_date'] = ($fsn['fs_lt_date']>0) ? @date($cfg['formatmonthdayhourmin'], $fsn['fs_lt_date'] + $usr['timezone'] * 3600) : '';
        $fsn['fs_viewcount_short'] = ($fsn['fs_viewcount']>9999) ? floor($fsn['fs_viewcount']/1000)."k" : $fsn['fs_viewcount'];
        $fsn['fs_lt_postername'] = cot_build_user($fsn['fs_lt_posterid'], htmlspecialchars($fsn['fs_lt_postername']));

        if (!$secact_max)
        {
            $section_activity = '';
            $section_activity_img = '';
            $secact_num = 0;
        }
        else
        {
            $secact_num = round(6.25 * $cot_sections_act[$fsn['fs_id']] / $secact_max);
            if ($secact_num>5) { $secact_num = 5; }
            if (!$secact_num && $cot_sections_act[$fsn['fs_id']]>1) { $secact_num = 1; }
            $section_activity_img = cot_rc('frm_icon_section_activity');
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
        "FORUMS_SECTIONS_ROW_VIEWERS" => $cot_sections_vw_cur,
        "FORUMS_SECTIONS_ROW_URL" => cot_url('forums', "m=topics&s=".$fsn['fs_id']),
        "FORUMS_SECTIONS_ROW_LASTPOSTDATE" => $fsn['fs_lt_date'],
        "FORUMS_SECTIONS_ROW_LASTPOSTER" => $fsn['fs_lt_postername'],
        "FORUMS_SECTIONS_ROW_LASTPOST" => $fsn['lastpost'],
        "FORUMS_SECTIONS_ROW_TIMEAGO" => $fsn['fs_timago'],
        "FORUMS_SECTIONS_ROW_ACTIVITY" => $section_activity_img,
        "FORUMS_SECTIONS_ROW_ACTIVITYVALUE" => $secact_num,
        "FORUMS_SECTIONS_ROW_NEWPOSTS" => $fsn['fs_newposts'],
        "FORUMS_SECTIONS_ROW_ODDEVEN" => cot_build_oddeven($fs_num),
        "FORUMS_SECTIONS_ROW_NUM" => $fs_num,
        "FORUMS_SECTIONS_ROW" => $fsn
            ));

        $ii = 0;
        $sql1 = $db->query("SELECT fs_id, fs_title, fs_lt_date FROM $db_forum_sections WHERE fs_masterid='".$fsn['fs_id']."' ");
        while ($row = $sql1->fetch())
        {

            if ($row['fs_lt_date']>$latestp)
            {
                $sql0 = $db->query("SELECT fs_lt_id, fs_lt_title, fs_lt_posterid, fs_lt_postername FROM $db_forum_sections WHERE fs_id='".$row['fs_id']."' ");
                $fsnn = $sql0->fetch();

                $fsnn['fs_lt_date'] = @date($cfg['formatmonthdayhourmin'], $row['fs_lt_date'] + $usr['timezone'] * 3600);

                $fsnn['lastpost'] = cot_rc_link(cot_url('forums', "m=posts&q=".$fsnn['fs_lt_id']."&n=last", "#bottom"), cot_cutstring($fsnn['fs_lt_title'], 32), 'rel="nofollow"');

                $fsnn['fs_timago'] = cot_build_timegap($row['fs_lt_date'], $sys['now_offset']);

                $t-> assign(array(
                    "FORUMS_SECTIONS_ROW_LASTPOSTDATE" => $fsnn['fs_lt_date'],
                    "FORUMS_SECTIONS_ROW_LASTPOSTER" => cot_build_user($fsnn['fs_lt_posterid'], htmlspecialchars($fsnn['fs_lt_postername'])),
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
                "FORUMS_SECTIONS_ROW_SLAVE_URL" => cot_url('forums', "m=topics&s=".$row['fs_id']),
                "FORUMS_SECTIONS_ROW_SLAVE_ODDEVEN" => cot_build_oddeven($ii),
                "FORUMS_SECTIONS_ROW_SLAVE_NUM" => $ii,
                "FORUMS_SECTIONS_ROW_SLAVE" => $row,
                "FORUMS_SECTIONS_ROW_SLAVEI" => cot_rc_link(cot_url('forums', "m=topics&s=".$row['fs_id']), $j.htmlspecialchars($row['fs_title'])),
                ));

            $t->parse("MAIN.FORUMS_SECTIONS_ROW.FORUMS_SECTIONS_ROW_SECTION.FORUMS_SECTIONS_ROW_SECTION_SLAVES");
        }

        /* === Hook - Part2 : Include === */
        foreach ($extp as $pl)
        {
        	include $pl;
        }
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
foreach (cot_getextplugins('forums.sections.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

if ($cache && $usr['id'] === 0 && $cfg['cache_forums'])
{
	$cache->page->write();
}

?>