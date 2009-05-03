<?PHP
/**
 * URL Transformation Rules editor.
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Trustmaster
 * @copyright Copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') && defined('SED_ADMIN') or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=urls'), $L['adm_urls']);
$adminhelp = $L['adm_help_urls'];

$a = sed_import('a', 'G', 'ALP');

$site_uri = SED_SITE_URI;

// Server type detection
if(stristr($_SERVER['SERVER_SOFTWARE'], 'apache'))
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
elseif(stristr($_SERVER['SERVER_SOFTWARE'], 'iis'))
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
elseif(stristr($_SERVER['SERVER_SOFTWARE'], 'nginx'))
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

if($a == 'save')
{
	// Fetch data
	$ut_area = sed_import('area', 'P', 'ARR');
	$ut_params = sed_import('params', 'P', 'ARR');
	$ut_format = sed_import('format', 'P', 'ARR');
	$htaccess = sed_import('htaccess', 'P', 'BOL');
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
			$error_string .= $L['adm_urls_callbacks'] . ': ' . sed_cc($ut_format[$i]) . '<br />';
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
				if($key != '*' && $val != '*' && !strstr($val, '|'))
				{
					$qs .= '&' . $key . '=' . urlencode($val);
				}
			}
		}
		// Correct the query string
		if(stristr($format, '?'))
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
		if(mb_strstr($htdata, '### COTONTI URLTRANS ###'))
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
	$adminmain .= '<h4>' . $L['adm_urls_your'] . ' <em>' . $conf_name . '</em>' . '</h4>';
	$adminmain .= '<pre class="code">' . $hta . '</pre>';
	if(!empty($error_string))
	{
		$adminmain .= '<div class="error">' . $error_string . $L['adm_urls_errors'] . '</div>';
	}
}

// Check urltrans.dat
if(!is_writeable('./datas/urltrans.dat'))
{
	$adminmain .= '<div class="error">' . $L['adm_urls_error_dat'] . '</div>';
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
$areabox = '<select name="area[]">';
foreach($areas as $ar)
{
	$areabox .= $ar == '*' ? '<option selected="selected">'.$ar.'</option>' : '<option>'.$ar.'</option>';
}
$areabox .= '</select>';
$admin_urls_form = sed_url('admin', "m=urls&a=save");
// Render rules table
$adminmain .= <<<HTM
<h4>{$L['adm_urls_rules']}</h4>
<script type="text/javascript" src="js/jquery.tablednd.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#rules").tableDnD({});
});

var ruleCount = 0;
function addRule() {
	$('#rules_top').after('<tr id="rule_' + ruleCount + '"><td>$areabox</td><td><input type="text" name="params[]" value="*" /></td><td><input type="text" name="format[]" value="" /></td><td><a href="#" onclick="$(\'#rule_' + ruleCount + '\').remove(); return false;">[X]</a></td></tr>');
	ruleCount++;
	return false;
}
</script>
<style>
tr.tDnD_whileDrag td {
	background-color: yellow;
}
</style>
<form action="{$admin_urls_form}" method="post">
<table id="rules" class="cells">
<tr id="rules_top">
	<td class="coltop">{$L['adm_urls_area']}</td>
	<td class="coltop">{$L['adm_urls_parameters']}</td>
	<td class="coltop">{$L['adm_urls_format']}</td>
	<td class="coltop">{$L['Delete']}</td>
</tr>
HTM;

$fp = fopen('./datas/urltrans.dat', 'r');
// Rules
$ii = 0;
while($line = trim(fgets($fp), " \t\r\n"))
{
	$parts = explode("\t", $line);
	$areabox = '<select name="area[]">';
	foreach($areas as $ar)
	{
		$areabox .= $ar == $parts[0] ? '<option selected="selected">'.$ar.'</option>' : '<option>'.$ar.'</option>';
	}
	$areabox .= '</select>';
	$oddeven = sed_build_oddeven($ii);
	$adminmain .= <<<HTM
	<tr id="rule_$ii">
		<td>$areabox</td>
		<td><input type="text" name="params[]" value="{$parts[1]}" /></td>
		<td><input type="text" name="format[]" value="{$parts[2]}" /></td>
		<td><a href="#" onclick="\$('#rule_$ii').remove(); return false;">[X]</a></td>
	</tr>
HTM;
	$ii++;
}
fclose($fp);

$htaccess = $serv_type == 'apache' && is_writeable('./'.$conf_name) ? '<br /><input type="checkbox" name="htaccess" /> ' . $L['adm_urls_htaccess'] : '';
$adminmain .= <<<HTM
<tr>
<td colspan="4">
<script type="text/javascript">
ruleCount = $ii;
</script>
<a href="#" onclick="return addRule()"><strong>{$L['adm_urls_new']}</strong></a>
</td>
</tr>
<noscript>
<tr><td>$areabox</td><td><input type="text" name="params[]" value="*" /></td><td><input type="text" name="format[]" value="" /></td><td>&nbsp;</td></tr>
</noscript>
</table>
$htaccess<br /><input type="submit" value="{$L['adm_urls_save']}" />
</form>
HTM;

?>