<?PHP

/**
 * PM
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

$id = sed_import('id','G','INT');
$q = sed_import('q','G','TXT');
if(empty($id))
{
    sed_redirect(sed_url('pm'));
}

/* === Hook === */
$extp = sed_getextplugins('pm.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state=2");
$totalarchives = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_fromuserid='".$usr['id']."' AND (pm_state=0 OR pm_state=3)");
$totalsentbox = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state<2");
$totalinbox = sed_sql_result($sql, 0, "COUNT(*)");

$sql = sed_sql_query("SELECT *, u.user_name FROM $db_pm AS p LEFT JOIN $db_users AS u ON u.user_id=p.pm_touserid WHERE pm_id='".$id."'");
sed_die(sed_sql_numrows($sql)==0);
$row = sed_sql_fetcharray($sql);

$title = "<a href=\"".sed_url('pm')."\">".$L['Private_Messages']."</a> ".$cfg['separator'];

if ($row['pm_touserid']==$usr['id'] && $row['pm_state']==2)
{
    $f = 'archives';
    $title .= " <a href=\"".sed_url('pm', 'f=archives')."\">".$L['pm_archives']."</a>";
    $pm_fromuserid = $row['pm_fromuserid'];
    $pm_fromuser = htmlspecialchars($row['pm_fromuser']);
    $pm_touserid = $usr['id'];
    $pm_touser = htmlspecialchars($usr['name']);
    $pm_fromortouser = sed_build_user($pm_fromuserid, $pm_fromuser);
    $row['pm_icon_action'] = "<a href=\"".sed_url('pm', "m=edit&a=delete&".sed_xg()."&id=".$row['pm_id']."&f=".$f)."\" title=\"".$L['Delete']."\"><img src=\"skins/".$skin."/img/system/icon-pm-trashcan.gif\" alt=\"".$L['Delete']."\" /></a>";
    $to = $row['pm_fromuserid'];
}
elseif ($row['pm_touserid']==$usr['id'] && $row['pm_state']<2)
{
    $f = 'inbox';
    $title .= " <a href=\"".sed_url('pm', 'f=inbox')."\">".$L['pm_inbox']."</a>";

    if ($row['pm_state']==0)
    {
        $sql = sed_sql_query("UPDATE $db_pm SET pm_state=1 WHERE pm_touserid='".$usr['id']."' AND pm_id='".$id."'");
        $sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state=0");
        $notread = sed_sql_result($sql,0,'COUNT(*)');
        if ($notread==0)
        { $sql = sed_sql_query("UPDATE $db_users SET user_newpm=0 WHERE user_id='".$usr['id']."'"); }
        // Leave a copy in sentbox
        sed_sql_query("INSERT INTO $db_pm (pm_state, pm_date, pm_fromuserid, pm_fromuser, pm_touserid, pm_title, pm_text, pm_html)
        VALUES(3, {$row['pm_date']}, {$row['pm_fromuserid']}, '".sed_sql_prep($row['pm_fromuser'])."', {$row['pm_touserid']}, '".sed_sql_prep($row['pm_title'])."', '".sed_sql_prep($row['pm_text'])."', '".sed_sql_prep($row['pm_html'])."')");
    }

    $pm_fromuserid = $row['pm_fromuserid'];
    $pm_fromuser = htmlspecialchars($row['pm_fromuser']);
    $pm_touserid = $usr['id'];
    $pm_touser = htmlspecialchars($usr['name']);
    $pm_fromortouser = sed_build_user($pm_fromuserid, $pm_fromuser);
    $row['pm_icon_action'] = "<a href=\"".sed_url('pm', "m=edit&a=archive&".sed_xg()."&id=".$row['pm_id'])."\" title=\"".$L['pm_putinarchives']."\"><img src=\"skins/".$skin."/img/system/icon-pm-archive.gif\" alt=\"".$L['pm_putinarchives']."\" /></a>";
    $row['pm_icon_action'] .= ($row['pm_state']>0) ? " <a href=\"".sed_url('pm', "m=edit&a=delete&".sed_xg()."&id=".$row['pm_id']."&f=".$f)."\" title=\"".$L['Delete']."\"><img src=\"skins/".$skin."/img/system/icon-pm-trashcan.gif\" alt=\"".$L['Delete']."\" /></a>" : '';
    $to = $row['pm_fromuserid'];


}
elseif ($row['pm_fromuserid']==$usr['id'] && ($row['pm_state']==0 || $row['pm_state']==3))
{
    $f = 'sentbox';
    $title .= " <a href=\"".sed_url('pm', 'f=sentbox')."\">".$L['pm_sentbox']."</a>";
    $pm_fromuserid = $usr['id'];
    $pm_fromuser = htmlspecialchars($usr['name']);
    $pm_touserid = $row['pm_touserid'];
    $pm_touser = htmlspecialchars($row['user_name']);
    $pm_fromortouser = sed_build_user($pm_touserid, $pm_touser);
    $row['pm_icon_action'] = "<a href=\"".sed_url('pm', "m=edit&a=delete&".sed_xg()."&id=".$row['pm_id']."&f=".$f)."\" title=\"".$L['Delete']."\"><img src=\"skins/".$skin."/img/system/icon-pm-trashcan.gif\" alt=\"".$L['Delete']."\" /></a>";
    $to = $row['pm_touserid'];
}
else
{
    sed_die();
}

$title .= ' '.$cfg['separator']." <a href=\"".sed_url('pm', 'id='.$id)."\">".htmlspecialchars($row['pm_title'])."</a>";

$title_tags[] = array('{PM}', '{INBOX}', '{ARCHIVES}', '{SENTBOX}');
$title_tags[] = array('%1$s', '%2$s', '%3$s', '%4$s');
$title_data = array($L['Private_Messages'], $totalinbox, $totalarchives, $totalsentbox);
$out['subtitle'] = sed_title('title_pm_main', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('pm.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$pm_sendlink = ($usr['auth_write']) ? "<a href=\"".sed_url('pm', 'm=send')."\">".$L['pm_sendnew']."</a>" : '';

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
$mess_title = $row['pm_title'];
if (preg_match("/Re(\(\d+\))?\:(.+)/", $row['pm_title'], $matches))
{
    $matches[1] = empty($matches[1]) ? 2 : trim($matches[1], '()') + 1;
    $newpmtitle = 'Re(' . $matches[1] . '): ' . trim($matches[2]);
}
else
{
    $newpmtitle = 'Re: ' . $row['pm_title'];
}

if(!empty($q))
{
    $newpmtext= "[quote]".$row['pm_text']."[/quote]";
}

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('pm'));

$t-> assign(array(
        "PM_PAGETITLE" => $title,
        "PM_SENDNEWPM" => $pm_sendlink,
        "PM_INBOX" => "<a href=\"".sed_url('pm')."\">".$L['pm_inbox']."</a>:".$totalinbox,
        "PM_ARCHIVES" => "<a href=\"".sed_url('pm', 'f=archives')."\">".$L['pm_archives']."</a>:".$totalarchives,
        "PM_SENTBOX" => "<a href=\"".sed_url('pm', 'f=sentbox')."\">".$L['pm_sentbox']."</a>:".$totalsentbox,
        "PM_ID" => $row['pm_id'],
        "PM_STATE" => $row['pm_state'],
        "PM_SELECT" => "<input type=\"checkbox\" class=\"checkbox\"  name=\"msg[".$row['pm_id']."]\" />",
        "PM_DATE" => @date($cfg['dateformat'], $row['pm_date'] + $usr['timezone'] * 3600),
        "PM_FROMUSERID" => $pm_fromuserid,
        "PM_FROMUSER" => sed_build_user($pm_fromuserid, $pm_fromuser),
        "PM_TOUSERID" => $pm_touserid,
        "PM_TOUSER" => sed_build_user($pm_touserid, $pm_touser),
        "PM_TITLE" => htmlspecialchars($mess_title),
        "PM_TEXT" => $pm_data,
        "PM_FROMORTOUSER" => $pm_fromortouser,
        "PM_ICON_ACTION" => $row['pm_icon_action'],
    ));

if ($usr['auth_write'])
{
    $t-> assign(array(
        "PM_QUOTE" => '<a href="'.sed_url('pm', 'm=message&amp;id='.$id.'&amp;q=quote').'">'.$L[Quote].'</a>',
        "PM_FORM_SEND" => sed_url('pm', 'm=send&amp;a=send&amp;to='.$to),
        "PM_FORM_TITLE" => "<input type=\"text\" class=\"text\" name=\"newpmtitle\" value=\"".htmlspecialchars($newpmtitle)."\" size=\"56\" maxlength=\"255\" />",
        "PM_FORM_TEXTBOXER" => "<textarea class=\"editor\" name=\"newpmtext\" rows=\"8\" cols=\"56\">".htmlspecialchars($newpmtext)."</textarea>".$pfs,
        "PM_FORM_MYPFS" => $pfs,
        ));
    $t->parse("MAIN.REPLY");
}



/* === Hook === */
$extp = sed_getextplugins('pm.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>