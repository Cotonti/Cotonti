<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Overloads standard cot_url() function and loads URL
 * transformation rules
 *
 * @package urleditor
 * @version 0.9.2
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'urleditor');
cot_block($usr['isadmin']);

$t = new XTemplate(cot_tplfile('urleditor.admin', 'plug', true));

require_once cot_incfile('forms');
require_once cot_langfile('urleditor', 'plug');

$adminhelp = $L['adm_help_urls'];

$a = cot_import('a', 'G', 'ALP');

$site_uri = COT_SITE_URI;

// Server type detection
if (mb_stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false)
{
	$serv_type = 'apache';
	$conf_name = '.htaccess';
	$hta_prefix = <<<END
# Rewrite engine options
Options FollowSymLinks -Indexes
RewriteEngine On
# Server-relative path to Cotonti:
RewriteBase "$site_uri"
END;
	$hta_flags = '[QSA,NC,NE,L]';
	$hta_rule = 'RewriteRule';
	$hta_postfix = '';
	$rb = '^';
	$re = '';
	$loc = '';
	$hta_error = 'ErrorDocument';
}
elseif (mb_stripos($_SERVER['SERVER_SOFTWARE'], 'iis') !== false)
{
	$serv_type = 'iis';
	$conf_name = 'IsapiRewrite4.ini';
	$hta_prefix = '';
	$hta_flags = '[I,L]';
	$hta_rule = 'RewriteRule';
	$hta_postfix = '';
	$rb = '^/';
	$re = '';
	$loc = '/';
}
elseif (mb_stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false)
{
	$serv_type = 'nginx';
	$conf_name = 'nginx.conf';
	$loc = $site_uri;
	if($site_uri[0] != '/') $loc = '/'.$loc;
	if($site_uri[mb_strlen($site_uri) - 1] != '/') $loc .= '/';
	$hta_prefix = '';
	$hta_flags = 'last;';
	$hta_rule = 'rewrite';
	$rb = '"^'.$loc;
	$re = '"';
	$hta_error = 'error_page';
}
else
{
	$serv_type = 'unknown';
}

/* === Hook === */
foreach (cot_getextplugins('admin.urls.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'save' && is_writable('./datas/urltrans.dat'))
{
	// Fetch data
	$ut_area = cot_import('area', 'P', 'ARR');
	$ut_params = cot_import('params', 'P', 'ARR');
	$ut_format = cot_import('format', 'P', 'ARR');
	$htaccess = cot_import('htaccess', 'P', 'BOL');

	/* === Hook === */
	foreach (cot_getextplugins('admin.urls.save') as $pl)
	{
		include $pl;
	}
	/* ===== */

	// Write header
	$fp = fopen('./datas/urltrans.dat', 'w');
	// Process and write
	$count = count($ut_area);
	// If the table is empty, restore the default rule
	if($count == 0)
	{
		$ut_area = array('*');
		$ut_params = array('*');
		$ut_format = array('{$_area}.php');
		$count = 1;
	}
	// Continue processing
	$hta = empty($hta_prefix) ? '' : $hta_prefix . "\n";
	$var_pattern = '[^/&?#]+';
	$mainurl = parse_url($cfg['mainurl']);
	$host = preg_quote($mainurl['host']);
	$path = preg_quote(COT_SITE_URI);
	// Pepend rules to fix static data when using dynamic categories
	if($serv_type != 'nginx')
	{
		$hta .= $hta_rule . ' ' . $rb . '(datas|images|js|themes)/(.*)$' . $re . ' ' . $loc . '$1/$2' . ' ' . $hta_flags . "\n";
	}
	for($i = 0; $i < $count; $i++)
	{
		if(empty($ut_format[$i]) || empty($ut_params[$i]))
		{
			// Ignore empty rules
			continue;
		}
		// Write the rule to urltrans.dat
		fputs($fp, $ut_area[$i] . "\t" . $ut_params[$i] . "\t" . $ut_format[$i] . "\n");
		if($ut_area[$i] == '*' && $ut_params[$i] == '*' && $ut_format[$i] == '{$_area}.php')
		{
			// Default rule doesn't need any rewrite rules
			continue;
		}
        $has_callbacks = false;
		if(preg_match('#\{[\w_]+\(\)\}#', $ut_format[$i]))
		{
			// Rule with callback, requires custom rewrite
			cot_message($L['adm_urls_callbacks'] . ': ' . htmlspecialchars($ut_format[$i]), 'warning');
            $has_callbacks = true;
			continue;
		}
        if ($has_callbacks)
        {
            cot_message('adm_urls_errors');
        }
		// Remove unsets
		$ut_format[$i] = preg_replace('#\{\!\$.+?\}#', '', $ut_format[$i]);
		// Set some defaults
		$hta_line = $hta_rule . ' ' . $rb;
		$format = $ut_format[$i];
		$area = $ut_area[$i] == '*' ? $var_pattern : $ut_area[$i];
		mb_parse_str($ut_params[$i], $params);
		$j = 0;
		$k = 0;
		$m_count = 0;
		$qs = '';
		$area_sub = '';
		if(preg_match('#^https?\://([^/]+)/(.*)$#', $format, $mt)/* && $serv_type == 'apache'*/)
		{
			// Subdomains support
			$format = $mt[2];
			$pattern = preg_quote($mt[2]);
			$rhost = $mt[1];
			$hta_host = preg_quote($rhost);
			if(preg_match_all('#\{\$(\w+)\}#', $rhost, $mt, PREG_SET_ORDER))
			{
				// Perform domain submask substitutions
				$mm_count = count($mt);
				$jj = 0;
				$kk = 0;
				for($jj = 0; $jj < $mm_count; $jj++)
				{
					$key = $mt[$jj][1];
					if($key == '_area')
					{
						if($area != $var_pattern)
						{
							$hta_host = str_replace(preg_quote($mt[$jj][0]), preg_quote($area), $hta_host);
							$kk++;
						}
						else
						{
							$hta_host = str_replace(preg_quote($mt[$jj][0]), '('.$area.')', $hta_host);
							$area_sub = '%' . ($jj - $kk + 1);
						}
					}
					elseif($key == '_host')
					{
						$hta_host = str_replace(preg_quote($mt[$jj][0]), $host, $hta_host);
						$kk++;
					}
					else
					{
						$hta_host = str_replace(preg_quote($mt[$jj][0]), '('.$var_pattern.')', $hta_host);
						$qs .= '&' . $key . '=%' . ($jj - $kk + 1);
						unset($params[$key]);
					}
				}
			}
			$hta .= "RewriteCond %{HTTP_HOST} ^$hta_host$ [NC]\n";
		}
		else
		{
			$pattern = preg_quote($format);
		}
		if(preg_match_all('#\{\$(\w+)\}#', $format, $mt, PREG_SET_ORDER))
		{
			// Perform substitutions for variables used in format
			$m_count = count($mt);
			for($j = 0; $j < $m_count; $j++)
			{
				$key = $mt[$j][1];
				if($key == '_area')
				{
					if($area != $var_pattern)
					{
						$pattern = str_replace(preg_quote($mt[$j][0]), preg_quote($area), $pattern);
						$k++;
					}
					else
					{
						$pattern = str_replace(preg_quote($mt[$j][0]), '('.$area.')', $pattern);
						$area_sub = '$' . ($j - $k + 1);
					}
				}
				elseif($key == '_host')
				{
					$pattern = str_replace(preg_quote($mt[$j][0]), $host, $pattern);
					$k++;
				}
				elseif($key == '_rhost')
				{
					$pattern = str_replace(preg_quote($mt[$j][0]), $var_pattern, $pattern);
					$k++;
				}
				elseif($key == '_path')
				{
					$pattern = str_replace(preg_quote($mt[$j][0]), $path, $pattern);
					$k++;
				}
				else
				{
					$pattern = str_replace(preg_quote($mt[$j][0]), '('.$var_pattern.')', $pattern);
					$qs .= '&' . $key . '=$' . ($j - $k + 1);
					unset($params[$key]);
				}
			}
		}
		// Complete the query string with static paramaters set but not used in format
		if(count($params) > 0)
		{
			foreach($params as $key => $val)
			{
				if ($key != '*' && $val != '*' && mb_strpos($val, '|') === false)
				{
					$qs .= '&' . $key . '=' . urlencode($val);
				}
			}
		}
		// Correct the query string
		if (mb_strpos($format, '?') !== false)
		{
			if(empty($qs))
			{
				$qs = mb_substr($format, mb_strpos($format, '?'));
			}
			else
			{
				$qs = mb_substr($format, mb_strpos($format, '?')) . $qs;
			}
		}
		elseif(!empty($qs))
		{
			$qs[0] = '?';
		}
		// Finalize the rewrite rule
		$pattern .= '(.*)$';
		$qs .= '$'. ($m_count - $k + 1);
		$area = empty($area_sub) ? $area : $area_sub;
		$hta_line .= $pattern . $re . ' ' . $loc . $area . '.php' . $qs . ' ' . $hta_flags;
		$hta .= $hta_line . "\n";
	}
	fclose($fp);
	if($htaccess)
	{
		$custom_htaccess = cot_import('custom_htaccess', 'P', 'NOC');
		$htdata = file_get_contents('.htaccess');
		if (mb_strpos($htdata, "\n### COTONTI URLTRANS ###\n") !== false)
		{
			$htparts = explode("\n### COTONTI URLTRANS ###\n", $htdata);
			$htparts[1] = $hta;
			if (count($htparts) == 3)
			{
				$htparts[3] = $htparts[2];
			}
			$htparts[2] = $custom_htaccess;
		}
		else
		{
			$htparts[0] = $htdata;
			$htparts[1] = $hta;
			$htparts[2] = $custom_htaccess;
			$htparts[3] = '';
		}
		$htdata = implode("\n### COTONTI URLTRANS ###\n", $htparts);
		file_put_contents('.htaccess', $htdata);
		$hta = $htdata;
	}

	$t->assign(array(
		'ADMIN_URLS_CONF_NAME' => $conf_name,
		'ADMIN_URLS_HTA' => $hta
	));
	$t->parse('MAIN.HTA');

	$cache && $cache->db->remove('cot_urltrans', 'system');
}

// Check urltrans.dat
if(!is_writeable('./datas/urltrans.dat'))
{
	cot_error('adm_urls_error_dat');
}

// Get list of valid areas
$areas = array('*', 'plug', 'login');
$res = $db->query("SELECT ct_code FROM $db_core WHERE ct_plug = 0 ORDER BY ct_code");
foreach ($res->fetchAll() as $row)
{
	$areas[] = $row['ct_code'];
}
sort($areas);

// New rule contents
foreach($areas as $ar)
{
	$t->assign(array(
		'ADMIN_URLS_AREABOX_SELECTED' => ($ar == '*') ? ' selected="selected"' : '',
		'ADMIN_URLS_AREABOX_ITEM' => $ar
	));
	$t->parse('MAIN.AREABOX');
}

if (is_readable('./datas/urltrans.dat'))
{
	$fp = fopen('./datas/urltrans.dat', 'r');
	// Rules
	$ii = 0;
	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('admin.urls.loop');
	/* ===== */
	while($line = trim(fgets($fp), " \t\r\n"))
	{
		$parts = preg_split('#\s+#', $line);

		$t->assign(array(
			'ADMIN_URLS_ROW_I' => $ii,
			'ADMIN_URLS_ROW_AREAS' => cot_selectbox($parts[0], 'area[]', $areas, $areas, false),
			'ADMIN_URLS_ROW_PARTS1' => cot_inputbox('text', 'params[]', $parts[1]),
			'ADMIN_URLS_ROW_PARTS2' => cot_inputbox('text', 'format[]', $parts[2]),
			'ADMIN_URLS_ROW_ODDEVEN' => cot_build_oddeven($ii)
		));

		/* === Hook - Part2 : Include === */
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse('MAIN.ROW');
		$ii++;
	}
	fclose($fp);
}

$htaccess = ($serv_type == 'apache' && is_writeable('./'.$conf_name)) ? true : false;
if ($htaccess)
{
	$htdata = file_get_contents('.htaccess');
	$htparts = explode("\n### COTONTI URLTRANS ###\n", $htdata);
	if (count($htparts) == 4)
	{
		$t->assign('ADMIN_URLS_CUSTOM_HTACCESS', $htparts[2]);
	}
}

// Error and message reporting
cot_display_messages($t);

$t->assign(array(
	'ADMIN_URLS_II' => $ii,
	'ADMIN_URLS_FORM_URL' => cot_url('admin', 'm=other&p=urleditor&a=save'),
	'ADMIN_URLS_ROW_AREAS' => cot_selectbox('*', 'area[]', $areas, $areas, false),
	'ADMIN_URLS_ROW_PARTS1' => cot_inputbox('text', 'params[]', ''),
	'ADMIN_URLS_ROW_PARTS2' => cot_inputbox('text', 'format[]', ''),
	'ADMIN_URLS_ROW_ODDEVEN' => cot_build_oddeven($ii)
));

/* === Hook  === */
foreach (cot_getextplugins('admin.urls.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (COT_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>