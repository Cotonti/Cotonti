<?php
/**
 * URL Transformation Rules editor.
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Trustmaster
 * @copyright Copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.urls'));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=urls'), $L['adm_urls']);
$adminhelp = $L['adm_help_urls'];

$a = sed_import('a', 'G', 'ALP');

$site_uri = SED_SITE_URI;

// Server type detection
if (mb_stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false)
{
	$serv_type = 'apache';
	$conf_name = '.htaccess';
	$hta_prefix = <<<END
# Rewrite engine options
Options FollowSymLinks -Indexes
RewriteEngine On
# Server-relative path to seditio:
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
	if($site_uri[strlen($site_uri) - 1] != '/') $loc .= '/';
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
$extp = sed_getextplugins('admin.urls.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'save')
{
	// Fetch data
	$ut_area = sed_import('area', 'P', 'ARR');
	$ut_params = sed_import('params', 'P', 'ARR');
	$ut_format = sed_import('format', 'P', 'ARR');
	$htaccess = sed_import('htaccess', 'P', 'BOL');

	/* === Hook === */
	$extp = sed_getextplugins('admin.urls.save');
	foreach ($extp as $pl)
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
	$path = preg_quote(SED_SITE_URI);
	// Pepend rules to fix static data when using dynamic categories
	if($serv_type != 'nginx')
	{
		$hta .= $hta_rule . ' ' . $rb . '(datas|images|js|skins)/(.*)$' . $re . ' ' . $loc . '$1/$2' . ' ' . $hta_flags . "\n";
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
		if(preg_match('#\{[\w_]+\(\)\}#', $ut_format[$i]))
		{
			// Rule with callback, requires custom rewrite
			$error_string .= $L['adm_urls_callbacks'] . ': ' . htmlspecialchars($ut_format[$i]) . '<br />';
			continue;
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
				$qs = substr($format, strpos($format, '?'));
			}
			else
			{
				$qs = substr($format, strpos($format, '?')) . $qs;
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
		$htdata = file_get_contents('.htaccess');
		if (mb_strpos($htdata, '### COTONTI URLTRANS ###') !== false)
		{
			$htparts = explode('### COTONTI URLTRANS ###', $htdata);
			$htdata = $htparts[0] . "\n### COTONTI URLTRANS ###\n$hta\n### COTONTI URLTRANS ###\n" . $htparts[2];
		}
		else
		{
			$htdata .= "\n### COTONTI URLTRANS ###\n$hta\n### COTONTI URLTRANS ###\n";
		}
		file_put_contents('.htaccess', $htdata);
	}

	$t->assign(array(
		'ADMIN_URLS_CONF_NAME' => $conf_name,
		'ADMIN_URLS_HTA' => $hta
	));
	$t->parse('MAIN.HTA');

	if(!empty($error_string))
	{
		$adminwarnings = $error_string . $L['adm_urls_errors'];
	}
}

// Check urltrans.dat
if(!is_writeable('./datas/urltrans.dat'))
{
	$adminwarnings .= $L['adm_urls_error_dat'];
}

// Get list of valid areas
$areas = array('*');
$dp = opendir('.');
while($f = readdir($dp))
{
	if(preg_match('#(.+)\.php$#', $f, $mt))
	{
		$areas[] = $mt[1];
	}
}
closedir($dp);
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

$fp = fopen('./datas/urltrans.dat', 'r');
// Rules
$ii = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.urls.loop');
/* ===== */
while($line = trim(fgets($fp), " \t\r\n"))
{
	$parts = explode("\t", $line);

	$areabox = '<select name="area[]">
';
	foreach($areas as $ar)
	{
		$areabox .= ($ar == $parts[0]) ? '	<option selected="selected">'.$ar.'</option>
' : '	<option>'.$ar.'</option>
';
		$t->assign(array(
			'ADMIN_URLS_AREABOX_SELECTED' => ($ar == $parts[0]) ? ' selected="selected"' : '',
			'ADMIN_URLS_AREABOX_ITEM' => $ar
		));
		$t->parse('MAIN.ROW.AREABOX2');
	}
	$areabox .= '</select>
';

	$t->assign(array(
		'ADMIN_URLS_ROW_I' => $ii,
		'ADMIN_URLS_ROW_PARTS1' => $parts[1],
		'ADMIN_URLS_ROW_PARTS2' => $parts[2],
		'ADMIN_URLS_ROW_ODDEVEN' => sed_build_oddeven($ii)
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

$htaccess = ($serv_type == 'apache' && is_writeable('./'.$conf_name)) ? true : false;

$is_adminwarnings = isset($adminwarnings);

$t->assign(array(
	'ADMIN_URLS_II' => $ii,
	'ADMIN_URLS_FORM_URL' => sed_url('admin', 'm=urls&a=save'),
	'ADMIN_URLS_AREABOX' => $areabox,
	'ADMIN_URLS_ADMINWARNINGS' => $adminwarnings
));

/* === Hook  === */
$extp = sed_getextplugins('admin.urls.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (SED_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>