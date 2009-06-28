<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=search
File=tags
Hooks=standalone
Tags=
Order=
[END_SED_EXTPLUGIN]
==================== */

/**
 * Tag search
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Trustmaster (Vladimir Sibirov)
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') && defined('SED_PLUG') or die('Wrong URL');

$qs = sed_import('t', 'G', 'TXT');
if(empty($qs)) $qs = sed_import('t', 'P', 'TXT');

$tl = sed_import('tl', 'G', 'BOL');
if($tl) $qs = strtr($qs, $sed_translitb);

$d = (int) sed_import('d', 'G', 'INT');
$perpage = $cfg['plugin']['tags']['perpage'];

require_once $cfg['plugins_dir'].'/tags/inc/config.php';
require_once $cfg['plugins_dir'].'/tags/inc/functions.php';

// Array to register areas with tag functions provided
$tag_areas = array('pages', 'forums');

/* == Hook for the plugins == */
$extp = sed_getextplugins('tags.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->assign(array(
	'TAGS_ACTION' => sed_url('plug', 'e=tags&a=' . $a),
	'TAGS_HINT' => $L['tags_Query_hint'],
	'TAGS_QUERY' => sed_cc($qs)
));

if ($a == 'pages')
{
	if(empty($qs))
	{
		// Form and cloud
		sed_tag_search_form('pages');
	}
	else
	{
		// Search results
		$query = sed_tag_parse_query($qs);
		if(!empty($query))
		{
			sed_tag_search_pages($query);
		}
	}
}
elseif ($a == 'forums')
{
	if (empty($qs))
	{
		// Form and cloud
		sed_tag_search_form('forums');
	}
	else
	{
		// Search results
		$query = sed_tag_parse_query($qs);
		if(!empty($query))
		{
			sed_tag_search_forums($query);
		}
	}
}
elseif ($a == 'all')
{
	if (empty($qs))
	{
		// Form and cloud
		sed_tag_search_form('all');
	}
	else
	{
		// Search results
		$query = sed_tag_parse_query($qs);
		if(!empty($query))
		{
			foreach ($tag_areas as $area)
			{
				$tag_search_callback = 'sed_tag_search_' . $area;
				if (function_exists($tag_search_callback))
				{
					$tag_search_callback($query);
				}
			}
		}
	}
}
else
{
	/* == Hook for the plugins == */
	$extp = sed_getextplugins('tags.search.custom');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */
}

?>