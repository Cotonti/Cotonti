<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=system/footer.php
Version=122
Updated=2008-feb-18
Type=Core
Author=Neocrome
Description=Global footer
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

/* === Hook === */
$extp = sed_getextplugins('footer.first');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$i = explode(' ', microtime());
$sys['endtime'] = $i[1] + $i[0];
$sys['creationtime'] = round(($sys['endtime'] - $sys['starttime']), 3);

/* ========================================================
#
#  If you're not owner of a commercial licence or a copyright removal,
#  please do not delete the copyright line in the footer, thanks.
#  With doing this you'd break the licence agrement for personal users,
#  you probably won't go in jail for this but that's bad.
#  You can order a copyright line free version here :
#  http://www.neocrome.net/
#
========================================================== */

$out['creationtime'] = (!$cfg['disablesysinfos']) ? $L['foo_created'].' '.$sys['creationtime'].' '.$L['foo_seconds'] : '';
$out['sqlstatistics'] = ($cfg['showsqlstats']) ? $L['foo_sqltotal'].': '.round($sys['tcount'], 3).' '.$L['foo_seconds'].' - '.$L['foo_sqlqueries'].': '.$sys['qcount']. ' - '.$L['foo_sqlaverage'].': '.round(($sys['tcount']/$sys['qcount']), 5).' '.$L['foo_seconds'] : '';
$out['bottomline'] = $cfg['bottomline'];
$out['bottomline'] .= ($cfg['keepcrbottom']) ? '<br />'.$out['copyright'] : '';

/* ======== Who's online (part 2) ======== */

if (!$cfg['disablewhosonline'] || $cfg['shieldenabled'])
	{
	if ($usr['id']>0)
		{
		$sql = sed_sql_query("SELECT online_id FROM $db_online WHERE online_userid='".$usr['id']."'");

		if ($row = sed_sql_fetcharray($sql))
			{
			$online_count = 1;
			if ($cfg['shieldenabled'])
				{
				$sql2 = sed_sql_query("SELECT online_shield, online_action, online_hammer, online_lastseen FROM $db_online WHERE online_userid='".$usr['id']."'");
				if ($row = sed_sql_fetcharray($sql2))
					{
					$shield_limit = $row['online_shield'];
					$shield_action = $row['online_action'];
					$shield_hammer = sed_shield_hammer($row['online_hammer'],$shield_action,$row['online_lastseen']);
					}
				}
			$sql2 = sed_sql_query("UPDATE $db_online SET online_lastseen='".$sys['now']."', online_location='".sed_sql_prep($location)."', online_subloc='".sed_sql_prep($sys['sublocation'])."', online_hammer=".(int)$shield_hammer." WHERE online_userid='".$usr['id']."'");
			}
		else
         {
         $sql2 = sed_sql_query("INSERT INTO $db_online (online_ip, online_name, online_lastseen, online_location, online_subloc, online_userid, online_shield, online_hammer) VALUES ('".$usr['ip']."', '".sed_sql_prep($usr['name'])."', ".(int)$sys['now'].", '".sed_sql_prep($location)."',  '".sed_sql_prep($sys['sublocation'])."', ".(int)$usr['id'].", 0, 0)");
         }
      }
   else
      {
      $sql = sed_sql_query("SELECT COUNT(*) FROM $db_online WHERE online_ip='".$usr['ip']."'");
      $online_count = sed_sql_result($sql,0,'COUNT(*)');

      if ($online_count>0)
         {
         if ($cfg['shieldenabled'])
            {
            $sql2 = sed_sql_query("SELECT online_shield, online_action, online_hammer, online_lastseen FROM $db_online WHERE online_ip='".$usr['ip']."'");
            if ($row = sed_sql_fetcharray($sql2))
               {
               $shield_limit = $row['online_shield'];
               $shield_action = $row['online_action'];
               $shield_hammer = sed_shield_hammer($row['online_hammer'],$shield_action,$row['online_lastseen']);
               }
            }
         $sql2 = sed_sql_query("UPDATE $db_online SET online_lastseen='".$sys['now']."', online_location='".$location."', online_subloc='".sed_sql_prep($sys['sublocation'])."', online_hammer=".(int)$shield_hammer." WHERE online_ip='".$usr['ip']."'");
         }
      else
         {
         $sql2 = sed_sql_query("INSERT INTO $db_online (online_ip, online_name, online_lastseen, online_location, online_subloc, online_userid, online_shield, online_hammer) VALUES ('".$usr['ip']."', 'v', ".(int)$sys['now'].", '".$location."', '".sed_sql_prep($sys['sublocation'])."', -1, 0, 0)");
         }
      }
	}

if ($cfg['devmode'])
	{
	$out['devmode'] = "<h4>Dev-mode :</h4><table><tr><td><em>SQL query</em></td><td><em>Duration</em></td><td><em>Timeline</em></td><td><em>Query</em></td></tr>";
	$out['devmode'] .= "<tr><td colspan=\"2\">BEGIN</td>";
	$out['devmode'] .= "<td style=\"text-align:right;\">0.000 ms</td><td>&nbsp;</td></tr>";
	foreach ($sys['devmode']['queries'] as $k => $i)
		{
		$out['devmode'] .= "<tr><td>#".$i[0]." &nbsp;</td>";
		$out['devmode'] .= "<td style=\"text-align:right;\">".sprintf("%.3f",round($i[1]*1000, 3))." ms</td>";
		$out['devmode'] .= "<td style=\"text-align:right;\">".sprintf("%.3f",round($sys['devmode']['timeline'][$k]*1000, 3))." ms</td>";
		$out['devmode'] .= "<td style=\"text-align:left;\">".sed_cc($i[2])."</td></tr>";
		}
	$out['devmode'] .= "<tr><td colspan=\"2\">END</td>";
	$out['devmode'] .= "<td style=\"text-align:right;\">".sprintf("%.3f",$sys['creationtime'])." ms</td><td>&nbsp;</td></tr>";
	$out['devmode'] .= "</table><br />Total:".round($sys['tcount'],4)."s - Queries:".$sys['qcount']. " - Average:".round(($sys['tcount']/$sys['qcount']),5)."s/q";
	}

/*
========= DEBUG:START =========
if (is_array($sys['auth_log']))
	{ $out['devauth'] .= "AUTHLOG: ".implode(', ',$sys['auth_log']); }
$txt_r = ($usr['auth_read']) ? '1' : '0';
$txt_w = ($usr['auth_write']) ? '1' : '0';
$txt_a = ($usr['isadmin']) ? '1' : '0';
$out['devauth'] .= " &nbsp; AUTH_FINAL_RWA:".$txt_r.$txt_w.$txt_a;
$out['devmode']	 .= $out['devauth'];
========= DEBUG:END =========
*/

/* === Hook === */
$extp = sed_getextplugins('footer.main');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if ($cfg['enablecustomhf'])
	{ $mskin = sed_skinfile(array('footer', mb_strtolower($location))); }
else
	{ $mskin = "skins/".$usr['skin']."/footer.tpl"; }
$t = new XTemplate($mskin);

$t->assign(array (
	"FOOTER_BOTTOMLINE" => $out['bottomline'],
	"FOOTER_CREATIONTIME" => $out['creationtime'],
	"FOOTER_COPYRIGHT" => $out['copyright'],
	"FOOTER_SQLSTATISTICS" => $out['sqlstatistics'],
	"FOOTER_LOGSTATUS" => $out['logstatus'],
	"FOOTER_PMREMINDER" => $out['pmreminder'],
	"FOOTER_ADMINPANEL" => $out['adminpanel'],
	"FOOTER_DEVMODE" => $out['devmode']
	));

/* === Hook === */
$extp = sed_getextplugins('footer.tags');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if ($usr['id']>0)
	{ $t->parse("FOOTER.USER"); }
else
	{ $t->parse("FOOTER.GUEST"); }

$t->parse("FOOTER");
$t->out("FOOTER");

@ob_end_flush();
@ob_end_flush();

sed_sql_close();

?>
