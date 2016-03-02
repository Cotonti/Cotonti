<?php
/**
 * Ratings API
 *
 * @package Ratings
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
require_once cot_incfile('users', 'module');
require_once cot_langfile('ratings', 'plug');
require_once cot_incfile('ratings', 'plug', 'resources');

// Table name globals
cot::$db->registerTable('ratings');
cot::$db->registerTable('rated');

/**
 * Generates ratings display for a given item
 *
 * @param string $ext_name Module or plugin code
 * @param string $code Item identifier
 * @param string $cat Item category code (optional)
 * @param bool $readonly Display as read-only
 * @return array Rendered HTML output for ratings and average integer value as an array with 2 elements
 * @global CotDB $db
 */
function cot_ratings_display($ext_name, $code, $cat = '', $readonly = false)
{
	global $db, $db_ratings, $db_rated, $db_users, $cfg, $usr, $sys, $L, $R;

	// Check permissions
	list($auth_read, $auth_write, $auth_admin) = cot_auth('plug', 'ratings');

	$enabled = cot_ratings_enabled($ext_name, $cat, $code);

	if (!$auth_read || !$enabled && !$auth_admin)
	{
		return array('', 0);
	}

	// Get current rating value
	$sql = $db->query("SELECT r.*, (SELECT COUNT(*) FROM $db_rated WHERE rated_area = ? AND rated_code = ?) AS `cnt` FROM $db_ratings AS r
		WHERE rating_area = ? AND rating_code = ? LIMIT 1",
		array($ext_name, $code, $ext_name, $code));

	if ($row = $sql->fetch())
	{
		$rating_average = $row['rating_average'];
		$item_has_rating = true;
		if ($rating_average < 1)
		{
			$rating_average = $rating_average == 0.00 ? 0 : 1;
		}
		elseif ($rating_average > 10)
		{
			$rating_average = 10;
		}
		$rating_cntround = round($rating_average, 0);
		$rating_raters_count = $row['cnt'];
	}
	else
	{
		$item_has_rating = false;
		$rating_average = 0;
		$rating_cntround = 0;
		$rating_raters_count = 0;
	}

	// Render read-only image
	$rating_fancy =  cot_rc('icon_rating_stars', array('val' => $rating_cntround));
	if (!$auth_write || $readonly)
	{
		return array($rating_fancy, $rating_cntround, $rating_raters_count);
	}

	// Check if the user has voted already for this item
	$already_voted = false;
	if ($usr['id'] > 0)
	{
		$sql1 = $db->query("SELECT rated_value FROM $db_rated
			WHERE rated_area = ? AND rated_code = ? AND rated_userid = ?",
			array($ext_name, $code, $usr['id']));

		if ($rated_value = $sql1->fetchColumn())
		{
			$already_voted = true;
			$rating_uservote = $L['rat_alreadyvoted'] . ' (' . $rated_value . ')';
		}
	}

	if ($already_voted && !$cfg['plugin']['ratings']['ratings_allowchange'])
	{
		return array($rating_fancy, $rating_cntround, $rating_raters_count);
	}

	$t = new XTemplate(cot_tplfile('ratings', 'plug'));

	/* == Hook for the plugins == */
	foreach (cot_getextplugins('ratings.main') as $pl)
	{
		include $pl;
	}
	/* ===== */

	// Get some extra information about votes
	if ($item_has_rating)
	{
		$sql = $db->query("SELECT COUNT(*) FROM $db_rated
			WHERE rated_area = ? AND rated_code = ?",
			array($ext_name, $code));
		$rating_voters = $sql->fetchColumn();
		$rating_since = $L['rat_since'] . ' ' . cot_date('datetime_medium', $row['rating_creationdate']);
		$rating_since_stamp = $row['rating_creationdate'];
		$rating_averageimg = cot_rc('icon_rating_stars', array('val' => $rating_cntround));
	}
	else
	{
		$rating_voters = 0;
		$rating_since = '';
		$rating_since_stamp = '';
		$rating_averageimg = '';
	}

	// Assign tags
	$t->assign(array(
		'RATINGS_CODE' => $code,
		'RATINGS_AVERAGE' => round($rating_average),
		'RATINGS_AVERAGEIMG' => $rating_averageimg,
		'RATINGS_VOTERS' => $rating_voters,
		'RATINGS_SINCE' => $rating_since,
		'RATINGS_SINCE_STAMP' => $rating_since_stamp,
		'RATINGS_FANCYIMG' => $rating_fancy,
		'RATINGS_USERVOTE' => $rating_uservote
	));

	/* == Hook for the plugins == */
	foreach (cot_getextplugins('ratings.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	// Render voting form
	$vote_block = ($auth_write && (!$already_voted || $cfg['plugin']['ratings']['ratings_allowchange'])) ? 'NOTVOTED.' : 'VOTED.';
	for ($i = 1; $i <= 10; $i++)
	{
		$checked = ($i <= $rating_cntround) ? 'checked="checked"' : '';
		$t->assign(array(
			'RATINGS_ROW_VALUE' => $i,
			'RATINGS_ROW_TITLE' => $L['rat_choice' . $i],
			'RATINGS_ROW_CHECKED' => $checked,
		));
		$t->parse('RATINGS.' . $vote_block . 'RATINGS_ROW');
	}

	if ($vote_block == 'NOTVOTED.')
	{
		// 'r=ratings&area=' . $ext_name . '&code=' . $code.'&inr=send'
		$t->assign('RATINGS_FORM_SEND', cot_url('plug', array(
			'r' => 'ratings',
			'inr' => 'send',
			'area' => $ext_name,
			'code' => $code,
			'cat' => $cat
		)));
		$t->parse('RATINGS.NOTVOTED');
	}
	else
	{
		$t->parse('RATINGS.VOTED');
	}

	// Parse and return
	$t->parse('RATINGS');
	$res = $t->text('RATINGS');

	return array($res, round($rating_cntround), $rating_raters_count);
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
	if (isset($cfg[$ext_name][$cat]['enable_ratings'])
		|| isset($cfg[$ext_name]['enable_ratings'])
		|| isset($cfg['plugin'][$ext_name]['enable_ratings']))
	{
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
	return true;
}

/**
 * Removes ratings associated with an item
 *
 * @param string $area Item area code
 * @param string $code Item identifier
 * @global CotDB $db
 */
function cot_ratings_remove($area, $code)
{
	global $db, $db_ratings, $db_rated;

	$db->delete($db_rated, 'rated_area = ? AND rated_code = ?', array($area, $code));
	$db->delete($db_ratings, 'rating_area = ? AND rating_code = ?', array($area, $code));
}
