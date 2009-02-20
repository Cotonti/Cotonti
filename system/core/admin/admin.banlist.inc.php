<?PHP
/**
 * Administration panel - Banlist
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array (sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array (sed_url('admin', 'm=banlist'), $L['Banlist']);
$adminhelp = $L['adm_help_banlist'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

$t = new XTemplate(sed_skinfile('admin.banlist.inc', false, true));
$adminbanlist = '';

if ($a=='update')
{
	$id = sed_import('id', 'G', 'INT');
	$rbanlistip = sed_import('rbanlistip', 'P', 'TXT');
	$rbanlistemail = sed_sql_prep(sed_import('rbanlistemail', 'P', 'TXT'));
	$rbanlistreason = sed_sql_prep(sed_import('rbanlistreason', 'P', 'TXT'));
	$sql = (!empty($rbanlistip) || !empty($rbanlistemail)) ? sed_sql_query("UPDATE $db_banlist SET banlist_ip='$rbanlistip', banlist_email='$rbanlistemail', banlist_reason='$rbanlistreason' WHERE banlist_id='$id'") : '';
	if($ajax AND $sql)
	{
		$adminbanlist .= $L['alreadyupdatednewentry'];
	}
	elseif($sql)
	{
		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', "m=banlist&d=".$d, '', true));
		exit;
	}
	else
	{
		$adminbanlist .= $L['Error'];
	}
}
elseif ($a=='add')
{
	$nbanlistip = sed_import('nbanlistip', 'P', 'TXT');
	$nbanlistemail = sed_sql_prep(sed_import('nbanlistemail', 'P', 'TXT'));
	$nbanlistreason = sed_sql_prep(sed_import('nbanlistreason', 'P', 'TXT'));
	$nexpire = sed_import('nexpire', 'P', 'INT');

	$nbanlistip_cnt = explode('.', $nbanlistip);
	$nbanlistip = (count($nbanlistip_cnt)==4) ? $nbanlistip : '';

	if ($nexpire>0)
	{ $nexpire += $sys['now']; }
	$sql = (!empty($nbanlistip) || !empty($nbanlistemail)) ? sed_sql_query("INSERT INTO $db_banlist (banlist_ip, banlist_email, banlist_reason, banlist_expire) VALUES ('$nbanlistip', '$nbanlistemail', '$nbanlistreason', ".(int)$nexpire.")") : '';
	if($ajax AND $sql)
	{
		$adminbanlist .= $L['alreadyaddnewentry'];
	}
	elseif($sql)
	{
		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', "m=banlist&d=".$d, '', true));
		exit;
	}
	else
	{
		$adminbanlist .= $L['Error']."<br>nbanlistip=".$nbanlistip."<br>nbanlistemail=".$nbanlistemail;
	}
}
elseif ($a=='delete')
{
	sed_check_xg();
	$id = sed_import('id', 'G', 'INT');
	$sql = sed_sql_query("DELETE FROM $db_banlist WHERE banlist_id='$id'");
	if($ajax AND $sql)
	{
		$adminbanlist .= $L['alreadydeletednewentry'];
	}
	elseif($sql)
	{
		header("Location: " . SED_ABSOLUTE_URL . sed_url('admin', "m=banlist", '', true));
		exit;
	}
	else
	{
		$adminbanlist .= $L['Error'];
	}
}

$closediv = '';
$opendiv = '';
$totalitems = sed_sql_rowcount($db_banlist);
if($cfg['jquery'])
{
	$opendiv = "
<script type=\"text/javascript\">
//<![CDATA[
function gopage(list)
	{
		var d = list.d || 0;
		var url = list.url || '';
		var page = list.page || '';
		var rowid = list.rowid || '';
		if(page=='add')
		{
			var exp = document.addbanlist.nexpire.value;
			var bip = document.addbanlist.nbanlistip.value;
			var bem = document.addbanlist.nbanlistemail.value;
			var brs = document.addbanlist.nbanlistreason.value;
			var x = document.addbanlist.x.value;
			$.post('admin.php?m=banlist&ajax=1&a=add&d='+d,
					{nexpire: exp, nbanlistip: bip, nbanlistemail: bem, nbanlistreason: brs, x: x},
					 function(data){
						$('#pagtab').addClass('loading');
						$('#pagtab').html(data).hide().stop().fadeIn('slow');
						$('#pagtab').removeClass('loading');
					}
			);
		}
		else if(page=='update')
		{
			var bip = document.getElementById('savebanlist_'+rowid).rbanlistip.value;
			var bem = document.getElementById('savebanlist_'+rowid).rbanlistemail.value;
			var brs = document.getElementById('savebanlist_'+rowid).rbanlistreason.value;
			var x = document.getElementById('savebanlist_'+rowid).x.value;
			$.post('admin.php?m=banlist&ajax=1&a=update&id='+rowid+'&d='+d,
					{rbanlistip: bip, rbanlistemail: bem, rbanlistreason: brs, x: x},
					 function(data){
						$('#pagtab').addClass('loading');
						$('#pagtab').html(data).hide().stop().fadeIn('slow');
						$('#pagtab').removeClass('loading');
					}
			);
		}
		else
		{
			$.ajax({
			type: 'GET',
			url: 'admin.php?',
			data: '&m=banlist'+url+'&d='+d,
			beforeSend: function(){
				$('#pagtab').addClass('loading');
			},
			success: function(msg){
			$('#pagtab').removeClass('loading');
			$('#pagtab').html(msg).hide().stop().fadeIn('slow');
				},
			error: function(msg){
			$('#pagtab').removeClass('loading');
			alert('Error ajax reload page');
				}
			});
		}
		return false;
	}
//]]>
</script>
<div id=\"pagtab\">";
	$closediv = '</div>';
	$pagnav = sed_pagination(sed_url('admin','m=banlist'), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'gopage', "url: '&amp;ajax=1'");
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=banlist'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'gopage', "url: '&amp;ajax=1'");
}
else
{
	$pagnav = sed_pagination(sed_url('admin','m=banlist'), $d, $totalitems, $cfg['maxrowsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=banlist'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
}

$sql = sed_sql_query("SELECT * FROM $db_banlist ORDER by banlist_expire DESC, banlist_ip LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;

while ($row = sed_sql_fetcharray($sql))
{
	$t -> assign(array(
		"ADMIN_BANLIST_ID_ROW" => $row['banlist_id'],
		"ADMIN_BANLIST_URL" => sed_url('admin', 'm=banlist&a=update&id='.$row['banlist_id'].'&d='.$d),
		"ADMIN_BANLIST_URL_AJAX" => ($cfg['jquery']) ? " OnSubmit=\"return gopage({d: ".$d.", page: 'update', rowid: ".$row['banlist_id']."});\"" : "",
		"ADMIN_BANLIST_DELURL" => sed_url('admin', 'm=banlist&a=delete&id='.$row['banlist_id'].'&d='.$d.'&'.sed_xg()),
		"ADMIN_BANLIST_DELURL_AJAX" => ($cfg['jquery']) ? " OnClick=\"return gopage({url: '&amp;ajax=1&amp;a=delete&amp;id=".$row['banlist_id']."&amp;".sed_xg()."'});\"" : "",
		"ADMIN_BANLIST_EXPIRE" => ($row['banlist_expire']>0) ? date($cfg['dateformat'],$row['banlist_expire'])." GMT" : $L['adm_neverexpire'],
		"ADMIN_BANLIST_IP" => $row['banlist_ip'],
		"ADMIN_BANLIST_EMAIL" => $row['banlist_email'],
		"ADMIN_BANLIST_REASON" => $row['banlist_reason']
		));
	$t -> parse("BANLIST.ADMIN_BANLIST_ROW");
	$ii++;
}

if(!empty($adminbanlist))
{
	$t -> assign(array(
		"ADMIN_BANLIST_MESAGE" => $adminbanlist
		));
	$t -> parse("BANLIST.MESAGE");
}

$t -> assign(array(
	"ADMIN_BANLIST_AJAX_OPENDIV" => $opendiv,
	"ADMIN_BANLIST_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_BANLIST_PAGNAV" => $pagnav,
	"ADMIN_BANLIST_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_BANLIST_TOTALITEMS" => $totalitems,
	"ADMIN_BANLIST_COUNTER_ROW" => $ii,
	"ADMIN_BANLIST_INC_URLFORMADD" => sed_url('admin', 'm=banlist&a=add&d='.$d),
	"ADMIN_BANLIST_INC_URLFORMADD_AJAX" => ($cfg['jquery']) ? " OnSubmit=\"return gopage({d: ".$d.", page: 'add'});\"" : "",
	"ADMIN_BANLIST_AJAX_CLOSEDIV" => $closediv
	));

$t -> parse("BANLIST");
$adminmain = $t -> text("BANLIST");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>