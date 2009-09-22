<?PHP

/**
 * PM List
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pm', 'a');
sed_block($usr['auth_read']);

$f = sed_import('f','G','ALP');
$d = sed_import('d','G','INT');

/* === Hook === */
$extp = sed_getextplugins('pm.list.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state=2");
$totalarchives = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_fromuserid='".$usr['id']."' AND (pm_state=0 OR pm_state=3)");
$totalsentbox = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state<2");
$totalinbox = sed_sql_result($sql, 0, "COUNT(*)");

if (empty($d)) { $d = '0'; }
unset($pageprev, $pagenext);



$bhome = $cfg['homebreadcrumb'] ? '<a href="'.$cfg['mainurl'].'">'.htmlspecialchars($cfg['maintitle']).'</a> '.$cfg['separator'].' ' : '';

$title = $bhome . "<a href=\"".sed_url('pm')."\">".$L['Private_Messages']."</a> ".$cfg['separator'];

if ($f=='archives')
{
    $totallines = $totalarchives;
    $sql = sed_sql_query("SELECT * FROM $db_pm
        WHERE pm_touserid='".$usr['id']."' AND pm_state=2
        ORDER BY pm_date DESC LIMIT $d,".$cfg['maxpmperpage']);
    $title .= " <a href=\"".sed_url('pm', 'f=archives')."\">".$L['pm_archives']."</a>";
    $subtitle = $L['pm_arcsubtitle'];
}
elseif ($f=='sentbox')
{
    $totallines = $totalsentbox;
    $sql = sed_sql_query("SELECT p.*, u.user_name FROM $db_pm p, $db_users u
        WHERE p.pm_fromuserid='".$usr['id']."' AND (p.pm_state=0 OR p.pm_state=3) AND u.user_id=p.pm_touserid
        ORDER BY pm_date DESC LIMIT $d,".$cfg['maxpmperpage']);
    $title .= " <a href=\"".sed_url('pm', 'f=sentbox')."\">".$L['pm_sentbox']."</a>";
    $subtitle = $L['pm_sentboxsubtitle'];
}
else
{
    $f = 'inbox';
    $totallines = $totalinbox;
    $sql = sed_sql_query("SELECT * FROM $db_pm
        WHERE pm_touserid='".$usr['id']."' AND pm_state<2
        ORDER BY pm_date DESC LIMIT  $d,".$cfg['maxpmperpage']);
    $title .= " <a href=\"".sed_url('pm')."\">".$L['pm_inbox']."</a>";
    $subtitle = $L['pm_inboxsubtitle'];
    $archive = ($totallines) ? "<input type=\"submit\" name=\"move\" value=\"".$L['pm_putinarchives']."\" />" : '';
}
$delete = ($totallines) ? "<input type=\"submit\" name=\"delete\" value=\"".$L['Delete']."\" />" : '';

$pm_totalpages = ceil($totallines / $cfg['maxpmperpage']);
$pm_currentpage = ceil ($d / $cfg['maxpmperpage'])+1;

$pm_pagination = sed_pagination(sed_url('pm', "f=$f"), $d, $totallines, $cfg['maxpmperpage'], 'd');
list($pm_pageprev, $pm_pagenext) = sed_pagination_pn(sed_url('pm', "f=$f"), $d, $totallines, $cfg['maxpmperpage'], TRUE, 'd');


$title_tags[] = array('{PM}', '{INBOX}', '{ARCHIVES}', '{SENTBOX}');
$title_tags[] = array('%1$s', '%2$s', '%3$s', '%4$s');
$title_data = array($L['Private_Messages'], $totalinbox, $totalarchives, $totalsentbox);
$out['subtitle'] = sed_title('title_pm_main', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('pm.list.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$pm_sendlink = ($usr['auth_write']) ? "<a href=\"".sed_url('pm', 'm=send')."\">".$L['pm_sendnew']."</a>" : '';

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('pm.list'));

if ($pm_totalpages=='0') {$pm_totalpages = '1'; }

$t-> assign(array(
    "PM_PAGETITLE" => $title,
    "PM_SUBTITLE" => $subtitle,
    "PM_FORM_UPDATE" => sed_url('pm', "m=edit&a=op&".sed_xg()."&f=".$f),
    "PM_SENDNEWPM" => $pm_sendlink,
    "PM_INBOX" => "<a href=\"".sed_url('pm')."\">".$L['pm_inbox']."</a>:".$totalinbox,
    "PM_ARCHIVES" => "<a href=\"".sed_url('pm', 'f=archives')."\">".$L['pm_archives']."</a>:".$totalarchives,
    "PM_SENTBOX" => "<a href=\"".sed_url('pm', 'f=sentbox')."\">".$L['pm_sentbox']."</a>:".$totalsentbox,
    "PM_DELETE" => $delete,
    "PM_ARCHIVE" => $archive,
    "PM_TOP_PAGEPREV" => $pm_pageprev,
    "PM_TOP_PAGENEXT" => $pm_pagenext,
    'PM_TOP_PAGES' => $pm_pagination,
    "PM_TOP_CURRENTPAGE" => $pm_currentpage,
    "PM_TOP_TOTALPAGES" => $pm_totalpages,
    "PM_TOP_SENTBOX" => ($f=='sentbox') ? $L['Recipient'] : $L['Sender'],
    ));

$jj=0;

/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('pm.list.loop');
/* ===== */

while ($row = sed_sql_fetcharray($sql) and ($jj<$cfg['maxpmperpage']))
{
    $jj++;
    $row['pm_icon_status'] = ($row['pm_state']=='0' && $f!='sentbox') ? sed_rc_link(sed_url('pm', 'id='.$row['pm_id']), $R['pm_icon_new']) : sed_rc_link(sed_url('pm', 'id='.$row['pm_id']), $R['pm_icon']);

    if ($f=='sentbox')
    {
        $pm_fromuserid = $usr['id'];
        $pm_fromuser = htmlspecialchars($usr['name']);
        $pm_touserid = $row['pm_touserid'];
        $pm_touser = htmlspecialchars($row['user_name']);
        $pm_fromortouser = sed_build_user($pm_touserid, $pm_touser);
        $row['pm_icon_action'] = sed_rc_link(sed_url('pm', 'm=edit&a=delete&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f), $R['pm_icon_trashcan'], array('title' => $L['Delete']));
    }
    elseif ($f=='archives')
    {
        $pm_fromuserid = $row['pm_fromuserid'];
        $pm_fromuser = htmlspecialchars($row['pm_fromuser']);
        $pm_touserid = $usr['id'];
        $pm_touser = htmlspecialchars($usr['name']);
        $pm_fromortouser = sed_build_user($pm_fromuserid, $pm_fromuser);
        $row['pm_icon_action'] = sed_rc_link(sed_url('pm', 'm=edit&a=delete&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f), $R['pm_icon_trashcan'], array('title' => $L['Delete']));
    }
    else
    {
        $pm_fromuserid = $row['pm_fromuserid'];
        $pm_fromuser = htmlspecialchars($row['pm_fromuser']);
        $pm_touserid = $usr['id'];
        $pm_touser = htmlspecialchars($usr['name']);
        $pm_fromortouser = sed_build_user($pm_fromuserid, $pm_fromuser);
        $row['pm_icon_action'] = sed_rc_link(sed_url('pm', 'm=edit&a=archive&'.sed_xg().'&id='.$row['pm_id']), $R['pm_icon_archive'], array('title' => $L['pm_putinarchives']));
        $row['pm_icon_action'] .= ($row['pm_state']>0) ? ' ' . sed_rc_link(sed_url('pm', 'm=edit&a=delete&'.sed_xg().'&id='.$row['pm_id'].'&f='.$f), $R['pm_icon_trashcan'], array('title' => $L['Delete'])) : '';
    }

    if($cfg['parser_cache'])
    {
        if(empty($row['pm_html']) && !empty($row['pm_text']))
        {
            $row['pm_html'] = sed_parse(htmlspecialchars($row['pm_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], 1);
            sed_sql_query("UPDATE $db_pm SET pm_html = '".sed_sql_prep($row['pm_html'])."' WHERE pm_id = " . $row['pm_id']);
        }
        $pm_data = sed_post_parse($row['pm_html']);
    }
    else
    {
        $pm_data = sed_parse(htmlspecialchars($row['pm_text']), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], 1);
        $pm_data = sed_post_parse($pm_data);
    }

    $t-> assign(array(
        "PM_ROW_ID" => $row['pm_id'],
        "PM_ROW_STATE" => $row['pm_state'],
        "PM_ROW_SELECT" => "<input type=\"checkbox\" class=\"checkbox\"  name=\"msg[".$row['pm_id']."]\" />",
        "PM_ROW_DATE" => @date($cfg['dateformat'], $row['pm_date'] + $usr['timezone'] * 3600),
        "PM_ROW_FROMUSERID" => $pm_fromuserid,
        "PM_ROW_FROMUSER" => sed_build_user($pm_fromuserid, $pm_fromuser),
        "PM_ROW_TOUSERID" => $pm_touserid,
        "PM_ROW_TOUSER" => sed_build_user($pm_touserid, $pm_touser),
        "PM_ROW_TITLE" => "<a href=\"".sed_url('pm', 'm=message&id='.$row['pm_id'])."\">".htmlspecialchars($row['pm_title'])."</a>",
        "PM_ROW_TEXT" => $pm_data,
        "PM_ROW_FROMORTOUSER" => $pm_fromortouser,
        "PM_ROW_ICON_STATUS" => $row['pm_icon_status'],
        "PM_ROW_ICON_ACTION" => $row['pm_icon_action'],
        "PM_ROW_DESC" => sed_cutpost($pm_data, 100, false),
        "PM_ROW_ODDEVEN" => sed_build_oddeven($jj),
        "PM_ROW_NUM" => $jj,
        ));

    /* === Hook - Part2 : Include === */
    if (is_array($extp))
    { foreach($extp as $k => $pl) { include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
    /* ===== */

    $t->parse("MAIN.PM_ROW");


}

if ($jj==0)
{ $t->parse("MAIN.PM_ROW_EMPTY"); }

$t->parse("MAIN.PM_FOOTER");


/* === Hook === */
$extp = sed_getextplugins('pm.list.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>