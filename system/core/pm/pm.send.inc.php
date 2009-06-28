<?PHP
/**
 * PM Send
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pm', 'a');
sed_block($usr['auth_write']);

$id = sed_import('id', 'G', 'INT');
$f = sed_import('f', 'G', 'ALP');
$to = sed_import('to', 'G', 'TXT');
$d = sed_import('d', 'G', 'INT');

unset($touser);
$totalrecipients = 0;
$touser_sql = array();
$touser_ids = array();
$touser_names = array();

/* === Hook === */
$extp = sed_getextplugins('pm.send.first');
if(is_array($extp))
{
    foreach($extp as $k => $pl)
    {
        include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
    }
}
/* ===== */

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state=2");
$totalarchives = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_fromuserid='".$usr['id']."' AND pm_state=0");
$totalsentbox = sed_sql_result($sql, 0, "COUNT(*)");
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state<2");
$totalinbox = sed_sql_result($sql, 0, "COUNT(*)");

if($a=='send')
{
    /* === Hook === */
    $extp = sed_getextplugins('pm.send.send.first');
    if(is_array($extp))
    {
        foreach($extp as $k => $pl)
        {
            include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
        }
    }
    /* ===== */

    sed_shield_protect();
    $newpmtitle = sed_import('newpmtitle', 'P', 'TXT');
    $newpmtext = sed_import('newpmtext', 'P', 'HTM');
    $newpmrecipient = sed_import('newpmrecipient', 'P', 'TXT');

    if(!empty($newpmrecipient))
    {
    $touser_src = explode(",", $newpmrecipient);
    $touser_req = count($touser_src);
    foreach($touser_src as $k => $i)
    {
        $touser_sql[] = "'".sed_sql_prep(trim(sed_import($i, 'D', 'TXT')))."'";
    }
    $touser_sql = implode(',', $touser_sql);
    $touser_sql = '('.$touser_sql.')';
    $sql = sed_sql_query("SELECT user_id, user_name FROM $db_users WHERE user_name IN $touser_sql");
    $totalrecipients = sed_sql_numrows($sql);
    while($row = sed_sql_fetcharray($sql))
    {
        $touser_ids[] = $row['user_id'];
        $row['user_name'] = sed_cc($row['user_name']);
        $touser_names[] = $row['user_name'];
        $touser_usrlnk[] .= ($cfg['parsebbcodecom']) ? "[user=".$row['user_id']."]".$row['user_name']."[/user]" : $row['user_name'];
    }
    $error_string .= ($totalrecipients < $touser_req ) ? $L['pm_wrongname']."<br />" : '';
    $error_string .= (!$usr['isadmin'] && $totalrecipients > 10) ? sprintf($L['pm_toomanyrecipients'], 10)."<br />" : '';
    $touser = ($totalrecipients>0) ? implode(",", $touser_names) : '';
    }
    else
    {
       $touser_ids[] = $to;
       $touser = $to;
       $totalrecipients = 1;
    }
    
    $error_string .= (mb_strlen($newpmtitle) < 2) ? $L['pm_titletooshort']."<br />" : '';
    $error_string .= (mb_strlen($newpmtext) < 2) ? $L['pm_bodytooshort']."<br />" : '';
    $error_string .= (mb_strlen($newpmtext) > $cfg['pm_maxsize']) ? $L['pm_bodytoolong']."<br />" : '';


    if(empty($error_string))
    {
        $newpmtext .= ($totalrecipients>1) ? "\n\n".sprintf($L['pm_multiplerecipients'], ($totalrecipients-1))."\n".implode(', ', $touser_usrlnk) : '';

        if($cfg['parser_cache'])
        {
            $newpmhtml = sed_sql_prep(sed_parse(sed_cc($newpmtext)));
        }
        else
        {
            $newpmhtml = '';
        }

        foreach($touser_ids as $k => $userid)
        {
            $sql = sed_sql_query("INSERT into $db_pm
            (pm_state,
            pm_date,
            pm_fromuserid,
            pm_fromuser,
            pm_touserid,
            pm_title,
            pm_text,
            pm_html)
            VALUES
            (0,
                ".(int)$sys['now_offset'].",
                ".(int)$usr['id'].",
                '".sed_sql_prep($usr['name'])."',
                ".(int)$userid.",
                '".sed_sql_prep($newpmtitle)."',
                '".sed_sql_prep($newpmtext)."',
                '$newpmhtml')");

            $sql = sed_sql_query("UPDATE $db_users SET user_newpm=1 WHERE user_id='".$userid."'");

            if($cfg['pm_allownotifications'])
            {
                $sql = sed_sql_query("SELECT user_email, user_name, user_lang
                FROM $db_users
                WHERE user_id='$userid' AND user_pmnotify=1 AND user_maingrp>3");

                if($row = sed_sql_fetcharray($sql))
                {
                    send_translated_mail($row['user_lang'], $row['user_email'], sed_cc($row['user_name']));
                    sed_stat_inc('totalmailpmnot');
                }
            }
        }

        /* === Hook === */
        $extp = sed_getextplugins('pm.send.send.done');
        if(is_array($extp))
        {
            foreach($extp as $k => $pl)
            {
                include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
            }
        }
        /* ===== */

        sed_stat_inc('totalpms');
        sed_shield_update(30, "New private message (".$totalrecipients.")");
        header("Location: " . SED_ABSOLUTE_URL . sed_url('pm', 'f=sentbox'));
        exit;
    }
}
elseif(!empty($to))
{
    if(mb_substr(mb_strtolower($to), 0, 1) == 'g' && $usr['maingrp'] == 5)
    {
        $group = sed_import(mb_substr($to, 1, 8), 'D', 'INT');
        if($group > 1)
        {
            $sql = sed_sql_query("SELECT user_id, user_name FROM $db_users WHERE user_maingrp='$group' ORDER BY user_name ASC");
            $totalrecipients = sed_sql_numrows($sql);
        }
    }
    else
    {
        $touser_src = explode('-', $to);
        $touser_req = count($touser_src);

        foreach($touser_src as $k => $i)
        {
            $userid = sed_import($i, 'D', 'INT');
            if($userid > 0)
            {
                $touser_sql[] = "'".$userid."'";
            }
        }
        if(count($touser_sql) > 0)
        {
            $touser_sql = implode(',', $touser_sql);
            $touser_sql = '('.$touser_sql.')';
            $sql = sed_sql_query("SELECT user_id, user_name FROM $db_users WHERE user_id IN $touser_sql");
            $totalrecipients = sed_sql_numrows($sql);
        }
    }

    if($totalrecipients>0)
    {
        while($row = sed_sql_fetcharray($sql))
        {
            $touser_ids[] = $row['user_id'];
            $touser_names[] = sed_cc($row['user_name']);
        }
        $touser = implode(", ", $touser_names);
        $error_string .= ($totalrecipients<$touser_req) ? $L['pm_wrongname']."<br />" : '';
        $error_string .= (!$usr['isadmin'] && $totalrecipients>10) ? sprintf($L['pm_toomanyrecipients'], 10)."<br />" : '';
    }
}

$pfs = sed_build_pfs($usr['id'], 'newlink', 'newpmtext', $L['Mypfs']);
$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, 'newlink', 'newpmtext', $L['SFS']) : '';
$pm_sendlink = ($usr['auth_write']) ? "<a href=\"".sed_url('pm', 'm=send')."\">".$L['pm_sendnew']."</a>" : '';

$title_tags[] = array('{PM}', '{SEND_NEW}');
$title_tags[] = array('%1$s', '%2$s');
$title_data = array($L['Private_Messages'], $L['pm_sendnew']);
$out['subtitle'] = sed_title('title_pm_send', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('pm.send.main');
if(is_array($extp))
{
    foreach($extp as $k => $pl)
    {
        include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
    }
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('pm.send'));

if(!empty($error_string))
{
    $t -> assign("PMSEND_ERROR_BODY",$error_string);
    $t -> parse("MAIN.PMSEND_ERROR");
}

$bhome = $cfg['homebreadcrumb'] ? '<a href="'.$cfg['mainurl'].'">'.sed_cc($cfg['maintitle']).'</a> '.$cfg['separator'].' ' : '';

$t -> assign(array(
    "PMSEND_TITLE" => $bhome . "<a href=\"".sed_url('pm')."\">".$L['Private_Messages']."</a> ".$cfg['separator']." ".$L['pmsend_title'],
    "PMSEND_SUBTITLE" => $L['pmsend_subtitle'],
    "PMSEND_SENDNEWPM" => $pm_sendlink,
    "PMSEND_INBOX" => "<a href=\"".sed_url('pm')."\">".$L['pm_inbox']."</a>:".$totalinbox,
    "PMSEND_ARCHIVES" => "<a href=\"".sed_url('pm', 'f=archives')."\">".$L['pm_archives']."</a>:".$totalarchives,
    "PMSEND_SENTBOX" => "<a href=\"".sed_url('pm', 'f=sentbox')."\">".$L['pm_sentbox']."</a>:".$totalsentbox,
    "PMSEND_FORM_SEND" => sed_url('pm', 'm=send&amp;a=send&amp;to='.$to),
    "PMSEND_FORM_TITLE" => "<input type=\"text\" class=\"text\" name=\"newpmtitle\" value=\"".sed_cc($newpmtitle)."\" size=\"56\" maxlength=\"255\" />",
    "PMSEND_FORM_TEXT" =>  "<textarea class=\"editor\" name=\"newpmtext\" rows=\"16\" cols=\"56\"></textarea><br />".$pfs,
    "PMSEND_FORM_TEXTBOXER" => "<textarea class=\"editor\" name=\"newpmtext\" rows=\"16\" cols=\"56\">".sed_cc($newpmtext)."</textarea><br />".$pfs,
    "PMSEND_FORM_MYPFS" => $pfs,
    "PMSEND_FORM_TOUSER" => "<textarea name=\"newpmrecipient\" rows=\"3\" cols=\"56\">".$touser."</textarea>"
    ));

/* === Hook === */
$extp = sed_getextplugins('pm.send.tags');
if(is_array($extp))
{
    foreach($extp as $k => $pl)
    {
        include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
    }
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

/* ======== Language PM for recipient ======== */
function send_translated_mail($transtolang, $remail, $rusername)
{
    global $cfg, $usr;

    $dlang = $cfg['system_dir'].'/lang/en/main.lang.php';
    $mlang = $cfg['system_dir'].'/lang/'.$cfg['defaultlang'].'/main.lang.php';
    $ulang = $cfg['system_dir'].'/lang/'.$transtolang.'/main.lang.php';

    if(file_exists($dlang))
    {
        require($dlang);
        $dlangne = 1;
    }
    if(file_exists($ulang) && $transtolang != 'en')
    {
        require($ulang);
    }
    elseif(file_exists($mlang) && $transtolang != $cfg['defaultlang'] && $transtolang != 'en')
    {
        require($mlang);
        $transtolang = $cfg['defaultlang'];
    }
    elseif(!$dlangne)
    {
        sed_diefatal('Main language file not found.');
    }

    $rsubject = $cfg['maintitle']." - ".$L['pm_notifytitle'];
    $rbody = sprintf($L['pm_notify'], $rusername, sed_cc($usr['name']), $cfg['mainurl'].'/'.sed_url('pm', '', '', true));

    sed_mail($remail, $rsubject, $rbody);
}

?>