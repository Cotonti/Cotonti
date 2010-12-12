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
}

?>
