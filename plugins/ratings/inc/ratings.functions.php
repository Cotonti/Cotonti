<?php
/**
 * Ratings API
 *
 * @package ratings
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
require_once cot_incfile('users', 'module');
require_once cot_langfile('ratings', 'plug');
require_once cot_incfile('ratings', 'plug', 'resources');

// Table name globals
$db_ratings = isset($db_ratings) ? $db_ratings : $db_x . 'ratings';
$db_rated = isset($db_rated) ? $db_rated : $db_x . 'rated';

/**
 * Generates ratings display for a given item
 *
 * @param string $ext_name Module or plugin code
 * @param string $code Item identifier
 * @param string $cat Item category code (optional)
 * @param bool $readonly Display as read-only
 * @return array Rendered HTML output for ratings and average value
 */
function cot_ratings_display($ext_name, $code, $cat = '', $readonly = false)
{
	global $db, $db_ratings, $db_rated, $db_users, $cfg, $usr, $sys, $L, $R;
	static $called = false;

	list($auth_read, $auth_write, $auth_admin) = cot_auth('ratings', 'a');

	$enabled = cot_ratings_enabled($ext_name, $cat, $code);

	if (!$auth_read || !$enabled && !$auth_admin)
	{
		return array('', 0);
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

	$rating_fancy =  '';
	for ($i = 1; $i <= 10; $i++)
	{
		$star_class = ($i <= $rating_cntround) ? 'star-rating star-rating-on' : 'star-rating star-rating-readonly';
		$star_margin = (in_array(($i / 2), array(1, 2, 3, 4, 5))) ? '-8' : '0';
		$rating_fancy .= '<div style="width: 8px;" class="'.$star_class.'"><a style="margin-left: '.$star_margin.'px;" title="'.$L['rat_choice'.$i].'">'.$i.'</a></div>';
	}
	if (!$auth_write || $readonly)
	{
		return array($rating_fancy, $rating_cntround);
	}

	if ($usr['id'] > 0)
	{
		$sql1 = $db->query("SELECT rated_value FROM $db_rated WHERE rated_code='$code' AND rated_userid='".$usr['id']."' LIMIT 1");

		if ($row1 = $sql1->fetch())
		{
			$alreadyvoted = ($cfg['ratings_allowchange']) ? FALSE : TRUE;
			$rating_uservote = $L['rat_alreadyvoted']." (".$row1['rated_value'].")";
		}
	}

	$t = new XTemplate(cot_tplfile('ratings', 'plug'));

	if (!$called && $auth_write && !$alreadyvoted)
	{
		// Link JS and CSS
		$t->parse('RATINGS.RATINGS_INCLUDES');
		$called = true;
	}
	/* == Hook for the plugins == */
	foreach (cot_getextplugins('ratings.main') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($yetrated)
	{
		$sql = $db->query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$code' ");
		$rating_voters = $sql->fetchColumn();
		$rating_average = $row['rating_average'];
		$rating_since = $L['rat_since']." ".date($cfg['dateformat'], $row['rating_creationdate'] + $usr['timezone'] * 3600);
		if ($rating_average<1)
		{
			$rating_average = 1;
		}
		elseif ($ratingaverage > 10)
		{
			$rating_average = 10;
		}

		$rating = round($rating_average,0);
		$rating_averageimg = cot_rc('icon_rating_stars', array('val' => $rating));
		$sql = $db->query("SELECT COUNT(*) FROM $db_rated WHERE rated_code='$code' ");
		$rating_voters = $sql->fetchColumn();
	}
	else
	{
		$rating_voters = 0;
		$rating_since = '';
		$rating_average = 0;
		$rating_averageimg = '';
	}

	$t->assign(array(
		'RATINGS_CODE' => $code,
		'RATINGS_AVERAGE' => $rating_average,
		'RATINGS_RATING' => $rating,
		'RATINGS_AVERAGEIMG' => $rating_averageimg,
		'RATINGS_VOTERS' => $rating_voters,
		'RATINGS_SINCE' => $rating_since,
		'RATINGS_FANCYIMG' => $rating_fancy,
		'RATINGS_USERVOTE' => $rating_uservote
	));

	/* == Hook for the plugins == */
	foreach (cot_getextplugins('ratings.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$vote_block = ($usr['id'] > 0 && !$alreadyvoted) ? 'NOTVOTED.' : 'VOTED.';
	for ($i = 1; $i <= 10; $i++)
	{
		$checked = ($i == $rating_cntround) ? 'checked="checked"' : '';
		$t->assign(array(
			'RATINGS_ROW_VALUE' => $i,
			'RATINGS_ROW_TITLE' => $L['rat_choice'.$i],
			'RATINGS_ROW_CHECKED' => $checked,
		));
		$t->parse('RATINGS.'.$vote_block.'RATINGS_ROW');
	}
	if ($vote_block == 'NOTVOTED.')
	{
		$t->assign('RATINGS_FORM_SEND', sed_url('plug', 'r=ratings&inr=send'));
		$t->parse('RATINGS.NOTVOTED');
	}
	else
	{
		$t->parse('RATINGS.VOTED');
	}
	$t->parse('RATINGS');
	$res = $t->text('RATINGS');

	return array($res, $rating_average);
}

/**
 * Checks if ratings are enabled for specific extension and category
 *
 * @param string $ext_name Extension name
 * @param string $cat Category name or empty if checking the entire area
 * @param string $item Item code, not yet supported
 * @return bool
 */
function cot_ratings_enabled($ext_name, $cat = '', $item = '')
{
	global $cfg, $cot_modules;

	if (isset($cot_modules[$ext_name]))
	{
		return (bool) (isset($cfg[$ext_name][$cat]['enable_ratings']) ? $cfg[$ext_name][$cat]['enable_ratings']
			: $cfg[$ext_name]['enable_ratings']);
	}
	else
	{
		return (bool) $cfg['plugin'][$ext_name]['enable_ratings'];
	}
}

?>
