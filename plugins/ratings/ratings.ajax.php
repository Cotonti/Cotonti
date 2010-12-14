<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

/**
 * AJAX handler for star ratings
 *
 * @package ratings
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$rcode = cot_import('rcode', 'G', 'ALP');
if (!empty($rcode))
{
	$code = $rcode;
}

$sql = $db->query("SELECT * FROM $db_ratings WHERE rating_code='$code' LIMIT 1");

if ($row = $sql->fetch())
{
	$rating_average = $row['rating_average'];
	$yetrated = TRUE;
	if ($rating_average<1)
	{
		$rating_average = 1;
	}
	elseif ($rating_average>10)
	{
		$rating_average = 10;
	}
	$rating_cntround = round($rating_average, 0);
}
else
{
	$yetrated = FALSE;
	$rating_average = 0;
	$rating_cntround = 0;
}

if (!empty($rcode))
{
	echo $rating_cntround;
	exit;
}

$inr = cot_import('inr', 'G', 'ALP');
$newrate = cot_import('rate_'.$code,'P', 'INT');

$newrate = (!empty($newrate)) ? $newrate : 0;

if (!$cfg['ratings_allowchange'])
{
	$alr_rated = $db->query("SELECT COUNT(*) FROM ".$db_rated." WHERE rated_userid=".$usr['id']." AND rated_code = '".$db->prep($code)."'")->fetchColumn();
}
else
{
	$alr_rated = 0;
}

if ($inr == 'send' && $newrate >= 0 && $newrate <= 10 && $auth_write && $alr_rated <= 0)
{
	/* == Hook for the plugins == */
	foreach (cot_getextplugins('ratings.send.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$sql = $db->query("DELETE FROM $db_rated WHERE rated_code='".$db->prep($code)."' AND rated_userid='".$usr['id']."' ");

	if (!$yetrated)
	{
		$sql = $db->query("INSERT INTO $db_ratings (rating_code, rating_state, rating_average, rating_creationdate, rating_text) VALUES ('".$db->prep($code)."', 0, ".(int)$newrate.", ".(int)$sys['now_offset'].", '') ");
	}

	$sql = ($newrate) ? $db->query("INSERT INTO $db_rated (rated_code, rated_userid, rated_value) VALUES ('".$db->prep($code)."', ".(int)$usr['id'].", ".(int)$newrate.")") : '';
	$sql = $db->query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$code'");
	$rating_voters = $sql->fetchColumn();
	if ($rating_voters > 0)
	{
		$ratingnewaverage = $db->query("SELECT AVG(rated_value) FROM $db_rated WHERE rated_code='$code'")->fetchColumn();
		$sql = $db->query("UPDATE $db_ratings SET rating_average='$ratingnewaverage' WHERE rating_code='$code'");
	}
	else
	{
		$sql = $db->query("DELETE FROM $db_ratings WHERE rating_code='$code' ");
	}

	/* == Hook for the plugins == */
	foreach (cot_getextplugins('ratings.send.done') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!COT_AJAX && cot_url_check($_SERVER['HTTP_REFERER']))
	{
		cot_redirect($_SERVER['HTTP_REFERER']);
	}
	exit;
}

?>
