<?php
/**
 * Main function library.
 *
 * @package Cotonti
 * @version 0.9.12
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

// System requirements check
if (!defined('COT_INSTALL'))
{
	(function_exists('version_compare') && version_compare(PHP_VERSION, '5.2.3', '>=')) or die('Cotonti system requirements: PHP 5.2.3 or above.'); // TODO: Need translate
	extension_loaded('mbstring') or die('Cotonti system requirements: mbstring PHP extension must be loaded.'); // TODO: Need translate
}

// Group constants
define('COT_GROUP_DEFAULT', 0);
define('COT_GROUP_GUESTS', 1);
define('COT_GROUP_INACTIVE', 2);
define('COT_GROUP_BANNED', 3);
define('COT_GROUP_MEMBERS', 4);
define('COT_GROUP_SUPERADMINS', 5);
define('COT_GROUP_MODERATORS', 6);

/* ======== Pre-sets ========= */

$out = array();
$plu = array();
$sys = array();
$usr = array();

$i = explode(' ', microtime());
$sys['starttime'] = $i[1] + $i[0];

$cfg['version'] = '0.9.12.1';
$cfg['dbversion'] = '0.9.12';

// Set default file permissions if not present in config
if (!isset($cfg['file_perms']))
{
	$cfg['file_perms'] = 0664;
}
if (!isset($cfg['dir_perms']))
{
	$cfg['dir_perms'] = 0777;
}

/**
 * Registry for captcha functions
 */
$cot_captcha = array();

/**
 * Registry for hash functions
 */
$cot_hash_funcs = array('md5', 'sha1', 'sha256');

/**
 * Array of custom cot_import() filter callbacks
 */
$cot_import_filters = array();

/**
 * Custom e-mail send callbacks
 */
$cot_mail_senders = array();

/**
 * Custom parser functions registry
 */
$cot_parsers = array();

/**
 * Parameters to be automatically appended to all URLs if present
 */
$cot_url_appendix = array();

/*
 * =========================== System Functions ===============================
*/

/**
 * Strips everything but alphanumeric, hyphens and underscores
 *
 * @param string $text Input
 * @return string
 */
function cot_alphaonly($text)
{
	return(preg_replace('/[^a-zA-Z0-9\-_]/', '', $text));
}

/**
 * Generic autoloader function to be used with spl_autoload_register
 *
 * @param string $class Class name
 */
function cot_autoload($class)
{
	global $cfg, $env;
	if ($env['type'] == 'module')
	{
		$paths[] = "{$cfg['modules_dir']}/{$env['ext']}/classes/$class.php";
	}
	elseif ($env['type'] == 'plug')
	{
		$paths[] = "{$cfg['plugins_dir']}/{$env['ext']}/classes/$class.php";
	}
	$paths[] = "{$cfg['system_dir']}/classes/$class.php";
	foreach ($paths as $path)
	{
		if (file_exists($path))
		{
			require $path;
			return;
		}
	}
}

/**
 * Truncates a string
 *
 * @param string $res Source string
 * @param int $l Length
 * @return string
 */
function cot_cutstring($res, $l)
{
	if (mb_strlen($res)>$l)
	{
		$res = mb_substr($res, 0, ($l-3)).'...';
	}
	return $res;
}

/**
 * Returns name part of the caller file. Use this in plugins to detect from which file
 * current hook part was included. Example:
 * <code>
 * if (cot_get_caller() == 'users.details')
 * {
 *     // We are called from users.details
 * }
 * else if (cot_get_caller() == 'header')
 * {
 *     // We are called from header
 * }
 * </code>
 * @return string Caller file basename without .php suffix on success, 'unknown' or error
 */
function cot_get_caller()
{
	$bt = debug_backtrace();
	if (isset($bt[1]) && in_array($bt[1]['function'], array('include', 'require_once', 'require', 'include_once')))
	{
		return preg_replace('#\.php$#', '', basename($bt[1]['file']));
	}
	else
	{
		return 'unknown';
	}
}

/**
 * Returns a list of plugins registered for a hook
 *
 * @param string $hook Hook name
 * @param string $cond Permissions
 * @return array
 * @global Cache $cache
 */
function cot_getextplugins($hook, $cond='R')
{
	global $cot_plugins, $cache, $cfg, $cot_hooks_fired;

	if ($cfg['debug_mode'])
	{
		$cot_hooks_fired[] = $hook;
	}

	$extplugins = array();

	if (isset($cot_plugins[$hook]) && is_array($cot_plugins[$hook]))
	{
		foreach($cot_plugins[$hook] as $k)
		{
			if ($k['pl_module'])
			{
				$dir = $cfg['modules_dir'];
				$cat = $k['pl_code'];
				$opt = 'a';
			}
			else
			{
				$dir = $cfg['plugins_dir'];
				$cat = 'plug';
				$opt = $k['pl_code'];
			}
			if (cot_auth($cat, $opt, $cond))
			{
				$extplugins[] = $dir . '/' . $k['pl_file'];
			}
		}
	}

	// Trigger cache handlers
	$cache && $cache->trigger($hook);

	return $extplugins;
}

/**
 * Imports data from the outer world
 *
 * @param string $name Variable name
 * @param string $source Source type: G/GET, P/POST, C/COOKIE, R/REQUEST, PUT, DELETE or D/DIRECT (variable filtering)
 * @param string $filter Filter type
 * @param int $maxlen Length limit
 * @param bool $dieonerror Die with fatal error on wrong input
 * @param bool $buffer Try to load from input buffer (previously submitted) if current value is empty
 * @return mixed
 */
function cot_import($name, $source, $filter, $maxlen = 0, $dieonerror = false, $buffer = false)
{
	global $cot_import_filters, $_PUT, $_PATCH, $_DELETE;

	if ($_SERVER['REQUEST_METHOD'] == 'PUT' && is_null($_PUT))
	{
		parse_str(file_get_contents('php://input'), $_PUT);
	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'PATCH' && is_null($_PATCH))
	{
		parse_str(file_get_contents('php://input'), $_PATCH);
	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE' && is_null($_DELETE))
	{
		parse_str(file_get_contents('php://input'), $_DELETE);
	}

	switch($source)
	{
		case 'G':
		case 'GET':
			$v = (isset($_GET[$name])) ? $_GET[$name] : NULL;
			$log = TRUE;
			break;

		case 'P':
		case 'POST':
			$v = (isset($_POST[$name])) ? $_POST[$name] : NULL;
			$log = TRUE;
			if ($filter=='ARR')
			{
				if ($buffer)
				{
					$v = cot_import_buffered($name, $v, null);
				}
				return($v);
			}
			break;

		case 'PUT':
			$v = (isset($_PUT[$name])) ? $_PUT[$name] : NULL;
			$log = TRUE;
			break;

		case 'PATCH':
			$v = (isset($_PATCH[$name])) ? $_PATCH[$name] : NULL;
			$log = TRUE;
			break;

		case 'DELETE':
			$v = (isset($_DELETE[$name])) ? $_DELETE[$name] : NULL;
			$log = TRUE;
			break;

		case 'R':
		case 'REQUEST':
			$v = (isset($_REQUEST[$name])) ? $_REQUEST[$name] : NULL;
			$log = TRUE;
			break;

		case 'C':
		case 'COOKIE':
			$v = (isset($_COOKIE[$name])) ? $_COOKIE[$name] : NULL;
			$log = TRUE;
			break;

		case 'D':
		case 'DIRECT':
			$v = $name;
			$log = FALSE;
			break;

		default:
			cot_diefatal('Unknown source for a variable : <br />Name = '.$name.'<br />Source = '.$source.' ? (must be G, P, C or D)');
			break;
	}

	if (MQGPC && ($source=='G' || $source=='P' || $source=='C') && $v != NULL && $filter != 'ARR')
	{
		$v = stripslashes($v);
	}

	if (($v === '' || $v === NULL) && $buffer)
	{
		$v = cot_import_buffered($name, $v, null);
		return $v;
	}

	if ($v === null)
	{
		return null;
	}

	if ($maxlen>0)
	{
		$v = mb_substr($v, 0, $maxlen);
	}

	$pass = FALSE;
	$defret = NULL;

	// Custom filter support
	if (is_array($cot_import_filters[$filter]))
	{
		foreach ($cot_import_filters[$filter] as $func)
		{
			$v = $func($v, $name);
		}
		return $v;
	}

	switch($filter)
	{
		case 'INT':
			if (is_numeric($v) && floor($v)==$v)
			{
				$pass = TRUE;
				$v = (int) $v;
			}
			break;

		case 'NUM':
			if (is_numeric($v))
			{
				$pass = TRUE;
				$v = (float) $v;
			}
			break;

		case 'TXT':
			$v = trim($v);
			if (mb_strpos($v, '<')===FALSE)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = str_replace('<', '&lt;', $v);
			}
			break;

		case 'ALP':
			$v = trim($v);
			$f = cot_alphaonly($v);
			if ($v == $f)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = $f;
			}
			break;

		case 'PSW':
			$v = trim($v);
			$f = preg_replace('#[\'"&<>]#', '', $v);
			$f = mb_substr($f, 0 ,32);

			if ($v == $f)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = $f;
			}
			break;

		case 'HTM':
			$v = trim($v);
			$pass = TRUE;
			break;

		case 'ARR':
			$pass = TRUE;
			break;

		case 'BOL':
			if ($v == '1' || $v == 'on')
			{
				$pass = TRUE;
				$v = TRUE;
			}
			elseif ($v=='0' || $v=='off')
			{
				$pass = TRUE;
				$v = FALSE;
			}
			else
			{
				$defret = FALSE;
			}
			break;

		case 'NOC':
			$pass = TRUE;
			break;

		default:
			cot_diefatal('Unknown filter for a variable : <br />Var = '.$v.'<br />Filter = &quot;'.$filter.'&quot; ?');
			break;
	}

	if (!$pass || !in_array($filter, array('INT', 'NUM', 'BOL', 'ARR')))
	{
		$v = preg_replace('/(&#\d+)(?![\d;])/', '$1;', $v);
	}
	if ($pass)
	{
		return $v;
	}
	else
	{
		if ($log)
		{
			cot_log_import($source, $filter, $name, $v);
		}
		if ($dieonerror)
		{
			cot_diefatal('Wrong input.');
		}
		else
		{
			return $defret;
		}
	}
}

/**
 * Puts POST data into the cross-request buffer
 */
function cot_import_buffer_save()
{
	// Referer contains an original form link
	if (cot_url_check($_SERVER['HTTP_REFERER']))
	{
		// Extract the server-relative part
		$url = parse_url($_SERVER['HTTP_REFERER']);
		// Strip ajax param from the query
		$url['query'] = str_replace('&_ajax=1', '', $url['query']);
		$path = empty($url['query']) ? $url['path'] : $url['path'] . '?' . $url['query'];
		$hash = md5($path);
		// Save the buffer
		$_SESSION['cot_buffer'][$hash] = $_POST;
	}

}

/**
 * Attempts to fetch a buffered value for a variable previously imported
 * if the currently imported value is empty
 *
 * @param string $name Input name
 * @param mixed $value Currently imported value
 * @param mixed $null null import
 * @return mixed Input value or NULL if the variable is not in the buffer
 */
function cot_import_buffered($name, $value, $null = '')
{
	// Params hash for current form
	$uri = str_replace('&_ajax=1', '', $_SERVER['REQUEST_URI']);
	$hash = md5($uri);
	if ($value === '' || $value === null
		|| isset($_SESSION['cot_buffer'][$hash][$name]) && !empty($_SESSION['cot_buffer'][$hash][$name]))
	{
		if (isset($_SESSION['cot_buffer'][$hash][$name]))
		{
			return $_SESSION['cot_buffer'][$hash][$name];
		}
		else
		{
			return $null;
		}
	}
	else
	{
		return $value;
	}
}

/**
 * Imports date stamp
 *
 * @param string $name Variable name preffix
 * @param bool $usertimezone Use user timezone
 * @param bool $returnarray Return Date Array
 * @param string $source Source type: P (POST), C (COOKIE) or D (variable filtering)
 * @return mixed
 */
function cot_import_date($name, $usertimezone = true, $returnarray = false, $source = 'P')
{
	global $usr;
	//$name = preg_match('#^(\w+)\[(.*?)\]$#', $name, $mt) ? $mt[1] : $name;
	$date = cot_import($name, $source, 'ARR');

	$year = cot_import($date['year'], 'D', 'INT');
	$month = cot_import($date['month'], 'D', 'INT');
	$day = cot_import($date['day'], 'D', 'INT');
	$hour = cot_import($date['hour'], 'D', 'INT');
	$minute = cot_import($date['minute'], 'D', 'INT');

	if (($month && $day && $year) || ($day && $minute))
	{
		$timestamp = cot_mktime($hour, $minute, 0, $month, $day, $year);
	}
	else
	{
		$string = cot_import($date['string'], 'D', 'TXT');
		$format = cot_import($date['format'], 'D', 'TXT');
		if ($string && $format)
		{
			$timestamp = cot_date2stamp($string, $format);
		}
		else
		{
			return NULL;
		}
	}
	if ($usertimezone)
	{
		$timestamp -= $usr['timezone'] * 3600;
	}
	if ($returnarray)
	{
		$result = array();
		$result['stamp'] = $timestamp;
		$result['year'] = (int)date('Y', $timestamp);
		$result['month'] = (int)date('m', $timestamp);
		$result['day'] = (int)date('d', $timestamp);
		$result['hour'] = (int)date('H', $timestamp);
		$result['minute'] = (int)date('i', $timestamp);
		return $result;
	}
	return $timestamp;
}

/**
 * Imports pagination indexes
 *
 * @param string $var_name URL parameter name, e.g. 'pg' or 'd'
 * @param int $max_items Max items per page
 * @return array Array containing 3 items: page number, database offset and argument for URLs
 */
function cot_import_pagenav($var_name, $max_items = 0)
{
	global $cfg;

	if($max_items <= 0)
	{
		$max_items = $cfg['maxrowsperpage'];
	}

	if($max_items <= 0)
	{
		throw new Exception('Invalid $max_items ('.$max_items.') for pagination.');
	}

	if ($cfg['easypagenav'])
	{
		$page = (int) cot_import($var_name, 'G', 'INT');
		if ($page < 0)
		{
			cot_die_message(404);
		}
		elseif ($page == 0)
		{
			$page = 1;
		}
		$offset = ($page - 1) * $max_items;
		$urlnum = $page <= 1 ? 0 : $page;
	}
	else
	{
		$offset = (int) cot_import($var_name, 'G', 'INT');
		if ($offset < 0)
		{
			cot_die_message(404);
		}
		if ($offset % $max_items != 0)
		{
			$offset -= $offset % $max_items;
		}
		$page = floor($offset / $max_items) + 1;
		$urlnum = $offset;
	}
	$urlnum = ($urlnum > 0) ? $urlnum : '';

	return array($page, $offset, $urlnum);
}

/**
 * Checks the email
 *
 * @param string $res input string
 * @return bool True if email valid
 */
function cot_check_email($res)
{
	  return mb_strlen($res) > 4 && preg_match('#^[\w\p{L}][\.\w\p{L}\-]*@[\w\p{L}\.\-]+\.[\w\p{L}]+$#u', $res);
}

/**
 * Sends mail with standard PHP mail().
 * If cot_mail_custom() function exists, it will be called instead of the PHP
 * function. This way custom mail delivery methods, such as SMTP, are
 * supported.
 *
 * @global $cfg
 * @param string $fmail Recipient
 * @param string $subject Subject
 * @param string $body Message body
 * @param string $headers Message headers
 * @param bool $customtemplate Use custom template
 * @param string $additional_parameters Additional parameters passed to sendmail
 * @return bool
 */
function cot_mail($fmail, $subject, $body, $headers='', $customtemplate = false, $additional_parameters = null, $html = false)
{
	global $cfg, $cot_mail_senders;

	if (function_exists('cot_mail_custom'))
	{
		return cot_mail_custom($fmail, $subject, $body, $headers, $customtemplate, $additional_parameters, $html);
	}

	if (is_array($cot_mail_senders) && count($cot_mail_senders) > 0)
	{
		foreach ($cot_mail_senders as $func)
		{
			$ret &= $func($fmail, $subject, $body, $headers, $additional_parameters, $html);
		}
		return $ret;
	}

	if (empty($fmail))
	{
		return false;
	}
	else
	{
		$sitemaintitle = mb_encode_mimeheader($cfg['maintitle'], 'UTF-8', 'B', "\n");

		$headers = (empty($headers)) ? "From: \"" . $sitemaintitle . "\" <" . $cfg['adminemail'] . ">\n" . "Reply-To: <" . $cfg['adminemail'] . ">\n"
				: $headers;
		$headers .= "Message-ID: <" . md5(uniqid(microtime())) . "@" . $_SERVER['SERVER_NAME'] . ">\n";

		$type_body = $html ? "html" : "plain";
		$headers .= "Content-Type: text/".$type_body."; charset=UTF-8\n";
		$headers .= "Content-Transfer-Encoding: 8bit\n";

		if (!$customtemplate)
		{
			$body_params = array(
				'SITE_TITLE' => $cfg['maintitle'],
				'SITE_URL' => $cfg['mainurl'],
				'SITE_DESCRIPTION' => $cfg['subtitle'],
				'ADMIN_EMAIL' => $cfg['adminemail'],
				'MAIL_SUBJECT' => $subject,
				'MAIL_BODY' => $body
			);

			$subject_params = array(
				'SITE_TITLE' => $cfg['maintitle'],
				'SITE_DESCRIPTION' => $cfg['subtitle'],
				'MAIL_SUBJECT' => $subject
			);

			$subject = cot_title($cfg['subject_mail'], $subject_params, false);
			$body = cot_title(str_replace("\r\n", "\n", $cfg['body_mail']), $body_params, false);
		}
		$subject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\n");

		if (ini_get('safe_mode'))
		{
			mail($fmail, $subject, $body, $headers);
		}
		else
		{
			mail($fmail, $subject, $body, $headers, $additional_parameters);
		}
		return true;
	}
}

/**
 * Checks if a module is currently installed and active
 *
 * @global array $cot_modules Module registry
 * @param string $name Module name
 * @return bool
 */
function cot_module_active($name)
{
	global $cot_modules;
	return isset($cot_modules[$name]);
}

/**
 * Standard SED output filters, adds XSS protection to forms
 *
 * @param unknown_type $output
 * @return unknown
 */
function cot_outputfilters($output)
{
	/* === Hook === */
	foreach (cot_getextplugins('output') as $pl)
	{
		include realpath(dirname(__FILE__).'/..') . '/' . $pl;
	}
	/* ==== */

	$output = preg_replace('#<form\s+[^>]*method=["\']?post["\']?[^>]*>#i', '$0' . cot_xp(), $output);

	return($output);
}

/**
 * Checks if a plugin is currently installed and active
 *
 * @global array $cot_plugins_active Active plugins registry
 * @param string $name Plugin name
 * @return bool
 */
function cot_plugin_active($name)
{
	global $cot_plugins_active;
	return is_array($cot_plugins_active) && isset($cot_plugins_active[$name]);
}

/**
 * Removes a directory recursively
 * @param string $dir Directory path
 * @return int Number of files and folders removed
 */
function cot_rmdir($dir)
{
	static $cnt = 0;
	$dp = opendir($dir);
	while ($f = readdir($dp))
	{
		$path = $dir . '/' . $f;
		if ($f != '.' && $f != '..' && is_dir($path))
		{
			cot_rmdir($path);
		}
		elseif ($f != '.' && $f != '..')
		{
			unlink($path);
			$cnt++;
		}
	}
	closedir($dp);
	rmdir($dir);
	$cnt++;
	return $cnt;
}

/**
 * Sends standard HTTP headers and disables browser cache
 *
 * @param string $content_type Content-Type value (without charset)
 * @param string $response_code HTTP response code, e.g. '404 Not Found'
 * @return bool
 */
function cot_sendheaders($content_type = 'text/html', $response_code = '200 OK')
{
	$protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
	header($protocol . ' ' . $response_code);
	header('Expires: Mon, Apr 01 1974 00:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: post-check=0,pre-check=0', FALSE);
	header('Content-Type: '.$content_type.'; charset=UTF-8');
	header('Cache-Control: no-store,no-cache,must-revalidate');
	header('Cache-Control: post-check=0,pre-check=0', FALSE);
	header('Pragma: no-cache');
	return TRUE;
}

/**
 * Set cookie with optional HttpOnly flag
 * @param string $name The name of the cookie
 * @param string $value The value of the cookie
 * @param int $expire The time the cookie expires in unixtime
 * @param string $path The path on the server in which the cookie will be available on.
 * @param string $domain The domain that the cookie is available.
 * @param bool $secure Indicates that the cookie should only be transmitted over a secure HTTPS connection. When set to TRUE, the cookie will only be set if a secure connection exists.
 * @param bool $httponly HttpOnly flag
 * @return bool
 */
function cot_setcookie($name, $value, $expire = '', $path='', $domain='', $secure = false, $httponly = true)
{
	global $cfg;

	if (mb_strpos($domain, '.') === FALSE)
	{
		// Some browsers don't support cookies for local domains
		$domain = '';
	}

	$domain = (empty($domain)) ? $cfg['cookiedomain'] : $domain;
	$path = (empty($path)) ? $cfg['cookiepath'] : $path;
	$expire = (empty($expire)) ? time()+$cfg['cookielifetime'] : $expire;

	if ($domain != '' && $domain != 'localhost')
	{
		// Make sure www. is stripped and leading dot is added for subdomain support on some browsers
		if (mb_strtolower(mb_substr($domain, 0, 4)) == 'www.')
		{
			$domain = mb_substr($domain, 4);
		}
		if ($domain[0] != '.')
		{
			$domain = '.'.$domain;
		}
	}
	else
	{
		$domain = false;
	}

	return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

/**
 * Performs actions required right before shutdown
 * @global CotDB $db
 * @global Cache $cache
 */
function cot_shutdown()
{
	global $cache, $db;
	// Clear import buffer if everything's OK on POST
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !cot_error_found())
	{
		unset($_SESSION['cot_buffer']);
	}
	while (ob_get_level() > 0)
	{
		ob_end_flush();
	}
	// Need to destroy cache before DB connection is lost
	$cache && $cache->db && $cache->db->flush();
	$cache = null;
	$db = null;
}

/**
 * Generates a title string by replacing submasks with assigned values
 *
 * @param string $area Area maskname or actual mask
 * @param array $params An associative array of available parameters
 * @param bool $escape Escape HTML special characters
 * @return string
 */
function cot_title($mask, $params = array(), $escape = true)
{
	global $cfg;
	$res = (!empty($cfg[$mask])) ? $cfg[$mask] : $mask;
	is_array($params) ? $args = $params : mb_parse_str($params, $args);
	if (preg_match_all('#\{(.+?)\}#', $res, $matches, PREG_SET_ORDER))
	{
		foreach($matches as $m)
		{
			$var = $m[1];
			$val = $escape ? htmlspecialchars($args[$var], ENT_COMPAT, 'UTF-8', false) : $args[$var];
			$res = str_replace($m[0], $val, $res);
		}
	}
	return $res;
}

/**
 * Generates random string within hexadecimal range
 *
 * @param int $length Length
 * @return string
 */
function cot_unique($length = 16)
{
	$string = sha1(mt_rand());
	if ($length > 40)
	{
		for ($i=0; $i < floor($length / 40); $i++)
		{
			$string .= sha1(mt_rand());
		}
	}
	return(substr($string, 0, $length));
}

/**
 * Generates random string within specified charlist
 *
 * @param int $length String length
 * @param string $charlist Allowed characters, defaults to alphanumeric chars
 * @return string and numbers ($pass)
 */
function cot_randomstring($length = 8, $charlist = null)
{
	if (!is_string($charlist))
		$charlist = 'ABCDEFGHIJKLMNOPRSTUVYZabcdefghijklmnoprstuvyz0123456789';
	$max = strlen($charlist) - 1;
	for ($i=0; $i < $length; $i++)
	{
		$string .= $charlist[mt_rand(0, $max)];
	}
	return $string;
}

/*
 * =========================== Structure functions ===========================
 */

/**
 * Loads comlete category structure into array
 * @global CotDB $db
 */
function cot_load_structure()
{
	global $db, $db_structure, $cfg, $cot_extrafields, $structure;
	$structure = array();
	if (defined('COT_UPGRADE'))
	{
		$sql = $db->query("SELECT * FROM $db_structure ORDER BY structure_path ASC");
		$row['structure_area'] = 'page';
	}
	else
	{
		$sql = $db->query("SELECT * FROM $db_structure ORDER BY structure_area ASC, structure_path ASC");
	}

	/* == Hook: Part 1 ==*/
	$extp = cot_getextplugins('structure');
	/* ================= */

	$path = array(); // code path tree
	$tpath = array(); // title path tree
	$tpls = array(); // tpl codes tree

	foreach ($sql->fetchAll() as $row)
	{
		$last_dot = mb_strrpos($row['structure_path'], '.');

		$row['structure_tpl'] = empty($row['structure_tpl']) ? $row['structure_code'] : $row['structure_tpl'];

		if ($last_dot > 0)
		{
			$path1 = mb_substr($row['structure_path'], 0, $last_dot);
			$path[$row['structure_path']] = $path[$path1] . '.' . $row['structure_code'];
			$separaror = ($cfg['separator'] == strip_tags($cfg['separator'])) ? ' ' . $cfg['separator'] . ' ' : ' \ ';
			$tpath[$row['structure_path']] = $tpath[$path1] . $separaror . $row['structure_title'];
			$parent_dot = mb_strrpos($path[$path1], '.');
			$parent = ($parent_dot > 0) ? mb_substr($path[$path1], $parent_dot + 1) : $path[$path1];
		}
		else
		{
			$path[$row['structure_path']] = $row['structure_code'];
			$tpath[$row['structure_path']] = $row['structure_title'];
			$parent = $row['structure_code']; // self
		}

		if ($row['structure_tpl'] == 'same_as_parent')
		{
			$row['structure_tpl'] = $tpls[$parent];
		}

		$tpls[$row['structure_code']] = $row['structure_tpl'];

		$structure[$row['structure_area']][$row['structure_code']] = array(
			'path' => $path[$row['structure_path']],
			'tpath' => $tpath[$row['structure_path']],
			'rpath' => $row['structure_path'],
			'id' => $row['structure_id'],
			'tpl' => $row['structure_tpl'],
			'title' => $row['structure_title'],
			'desc' => $row['structure_desc'],
			'icon' => $row['structure_icon'],
			'locked' => $row['structure_locked'],
			'count' => $row['structure_count']
		);

		if (is_array($cot_extrafields[$db_structure]))
		{
			foreach ($cot_extrafields[$db_structure] as $exfld)
			{
				$structure[$row['structure_area']][$row['structure_code']][$exfld['field_name']] = $row['structure_'.$exfld['field_name']];
			}
		}

		/* == Hook: Part 2 ==*/
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ================= */
	}
}

/**
 * Gets an array of category children
 *
 * @param string $area Area code
 * @param string $cat Cat code
 * @param bool $allsublev All sublevels array
 * @param bool $firstcat Add main cat
 * @param bool $userrights Check userrights
 * @param bool $sqlprep use $db->prep function
 * @return array
 * @global CotDB $db
 */
function cot_structure_children($area, $cat, $allsublev = true,  $firstcat = true, $userrights = true, $sqlprep = true)
{
	global $structure, $db;

	$mtch = $structure[$area][$cat]['path'].'.';
	$mtchlen = mb_strlen($mtch);
	$mtchlvl = mb_substr_count($mtch,".");

	$catsub = array();
	if ($cat != '' && $firstcat && (($userrights && cot_auth($area, $cat, 'R') || !$userrights)))
	{
		$catsub[] = $cat;
	}

	foreach ($structure[$area] as $i => $x)
	{
		if (($cat == '' || mb_substr($x['path'], 0, $mtchlen) == $mtch) && (($userrights && cot_auth($area, $i, 'R') || !$userrights)))
		{
			//$subcat = mb_substr($x['path'], $mtchlen + 1);
			if ($cat == '' || $allsublev || (!$allsublev && mb_substr_count($x['path'],".") == $mtchlvl))
			{
				$i = ($sqlprep) ? $db->prep($i) : $i;
				$catsub[] = $i;
			}
		}
	}
	return($catsub);
}

/**
 * Gets an array of category parents
 *
 * @param string $area Area code
 * @param string $cat Cat code
 * @param string $type Type 'full', 'first', 'last'
 * @return mixed
 */
function cot_structure_parents($area, $cat, $type = 'full')
{
	global $structure;
	$pathcodes = explode('.', $structure[$area][$cat]['path']);

	if ($type == 'first')
	{
		reset($pathcodes);
		$pathcodes = current($pathcodes);
	}
	elseif ($type == 'last')
	{
		$pathcodes = end($pathcodes);
	}

	return $pathcodes;
}

/*
 * ================================= Authorization Subsystem ==================================
*/

/**
 * Returns specific access permissions
 *
 * @param string $area Cotonti area
 * @param string $option Option to access
 * @param string $mask Access mask
 * @return mixed
 */
function cot_auth($area, $option, $mask = 'RWA')
{
	global $sys, $usr;

	$mn['R'] = 1;
	$mn['W'] = 2;
	$mn['1'] = 4;
	$mn['2'] = 8;
	$mn['3'] = 16;
	$mn['4'] = 32;
	$mn['5'] = 64;
	$mn['A'] = 128;

	$masks = str_split($mask);
	$res = array();

	foreach ($masks as $k => $ml)
	{
		if (empty($mn[$ml]))
		{
			$sys['auth_log'][] = $area.'.'.$option.'.'.$ml.'=0';
			$res[] = FALSE;
		}
		elseif ($option == 'any')
		{
			$cnt = 0;

			if (is_array($usr['auth'][$area]))
			{
				foreach ($usr['auth'][$area] as $k => $g)
				{
					$cnt += (($g & $mn[$ml]) == $mn[$ml]);
				}
			}
			$cnt = ($cnt == 0 && $usr['auth']['admin']['a'] && $ml == 'A') ? 1 : $cnt;

			$sys['auth_log'][] = ($cnt > 0) ? $area.'.'.$option.'.'.$ml.'=1' : $area.'.'.$option.'.'.$ml.'=0';
			$res[] = ($cnt > 0) ? TRUE : FALSE;
		}
		else
		{
			$sys['auth_log'][] = (($usr['auth'][$area][$option] & $mn[$ml]) == $mn[$ml]) ? $area.'.'.$option.'.'.$ml.'=1' : $area.'.'.$option.'.'.$ml.'=0';
			$res[] = (($usr['auth'][$area][$option] & $mn[$ml]) == $mn[$ml]) ? TRUE : FALSE;
		}
	}
	return (count($res) == 1) ? $res[0] : $res;
}

/**
 * Builds Access Control List (ACL) for a specific user
 *
 * @param int $userid User ID
 * @param int $maingrp User main group
 * @return array
 * @global CotDB $db
 */
function cot_auth_build($userid, $maingrp = 0)
{
	global $db, $db_auth, $db_groups_users;

	$groups = array();
	$authgrid = array();

	if ($userid == 0 || $maingrp == 0)
	{
		$groups[] = 1;
	}
	else
	{
		$groups[] = $maingrp;
		$sql = $db->query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid=$userid");

		while ($row = $sql->fetch())
		{
			$groups[] = $row['gru_groupid'];
		}
		$sql->closeCursor();
	}

	$sql_groups = implode(',', $groups);
	$sql = $db->query("SELECT auth_code, auth_option, auth_rights FROM $db_auth WHERE auth_groupid IN (".$sql_groups.") ORDER BY auth_code ASC, auth_option ASC");

	while ($row = $sql->fetch())
	{
		$authgrid[$row['auth_code']][$row['auth_option']] |= $row['auth_rights'];
	}
	$sql->closeCursor();

	return $authgrid;
}

/**
 * Block user if he is not allowed to access the page
 *
 * @param bool $allowed Authorization result
 * @return bool
 */
function cot_block($allowed)
{
	if (!$allowed)
	{
		global $sys, $env;
		$env['status'] = '403 Forbidden';
		cot_redirect(cot_url('message', 'msg=930&'.$sys['url_redirect'], '', true));
	}
	return FALSE;
}


/**
 * Block guests from viewing the page
 *
 * @return bool
 */
function cot_blockguests()
{
	global $env, $usr, $sys;

	if ($usr['id'] < 1)
	{
		$env['status'] = '403 Forbidden';
		cot_redirect(cot_url('message', "msg=930&".$sys['url_redirect'], '', true));
	}
	return FALSE;
}

/*
 * =========================== Output forming functions ===========================
 */

/**
 * Renders breadcrumbs string from array of path crumbs
 *
 * @param array $crumbs Path crumbs as an array: { {$url1, $title1}, {$url2, $title2},..}
 * @param bool $home Whether to include link to home page in the root
 * @param bool $nolast If TRUE, last crumb will be rendered as plain text rather than hyperlink
 * @param bool $plain If TRUE plain titles will be rendered instead of hyperlinks
 * @param string $inrc Item template
 * @param string $separator Items separator
 * @return string
 */
function cot_breadcrumbs($crumbs, $home = true, $nolast = false, $plain = false, $inrc = '', $separator = '')
{
	global $cfg, $L;
	$tmp = array();
	if ($home)
	{
		$maintitle = (empty($L['breadcrumbmaintitle'])) ? $cfg['maintitle'] : $L['breadcrumbmaintitle'];
		array_unshift($crumbs, array($cfg['mainurl'], $maintitle));
	}
	$cnt = count($crumbs);
	for ($i = 0; $i < $cnt; $i++)
	{
		$elem = '';
		$params = is_array($crumbs[$i]) ? array(
			'url' => (!empty($crumbs[$i][0])) ? $crumbs[$i][0] : '#',
			'title' => htmlspecialchars($crumbs[$i][1], ENT_COMPAT, 'UTF-8', false)
		) : $params = array('title' => $crumbs[$i]);
		if ($plain || ($nolast && $i === $cnt - 1) || !isset($params['url']))
		{
			$crumb = cot_rc('breadcrumbs_plain', $params);
			if ($crumb == 'breadcrumbs_plain')
			{
				$crumb = cot_rc('string_catpath', $params);
			}
		}
		else
		{
			$crumb = cot_rc('breadcrumbs_link', $params);
			if ($crumb == 'breadcrumbs_link')
			{
				$crumb = cot_rc('link_catpath', $params);
			}
		}
		if ($i == 0)
		{
			$elem = cot_rc('breadcrumbs_first', array('crumb' => $crumb));
		}
		if ($i == $cnt - 1)
		{
			$elem = cot_rc('breadcrumbs_last', array('crumb' => $crumb));
		}
		if (!$elem || $elem == 'breadcrumbs_first' || $elem == 'breadcrumbs_last')
		{
			$elem = cot_rc('breadcrumbs_crumb', array('crumb' => $crumb));
		}
		if ($elem == 'breadcrumbs_crumb')
		{
			$elem = $crumb;
		}
		if(!empty($inrc))
		{
			$elem = cot_rc($inrc, array('elem' => $elem));
		}
		$tmp[] = $elem;
	}
	$separator = (!empty($separator) || !empty($inrc)) ? $separator : cot_rc('breadcrumbs_separator');
	$separator = ($separator == 'breadcrumbs_separator') ? $cfg['separator'] : $separator;
	$separator = (!empty($inrc) && (mb_strlen($separator) > 2 || empty($separator))) ? $separator : ' '.$separator.' ';

	$breadcrumbs = implode($separator, $tmp);
	$container = cot_rc('breadcrumbs_container', array('crumbs' => $breadcrumbs));
	return ($container == 'breadcrumbs_container') ? $breadcrumbs : $container;
}

/**
 * Calculates age out of date of birth.
 *
 * @param int $birthdate Timestamp or a string according to format 'YYYY-MM-DD'
 * @return int Age in years or NULL on failure
 */
function cot_build_age($birthdate)
{
	if (is_string($birthdate))
	{
		$birthdate = strtotime($birthdate);
	}
	if (is_null($birthdate) || $birthdate === false || $birthdate === -1)
	{
		return null;
	}

	list($birth_y, $birth_m, $birth_d) = explode('-', cot_date('Y-m-d', $birthdate));
	list($now_y, $now_m, $now_d) = explode('-', cot_date('Y-m-d'));

	$age = $now_y - $birth_y - 1;

	if ($birth_m < $now_m || ($birth_m == $now_m && $birth_d <= $now_d))
	{
		$age += 1;
	}

	return ($age < 0) ? $age + 136 : $age;
}

/**
 * Builds category path for cot_breadcrumbs()
 *
 * @param string $area Area code
 * @param string $cat Category code
 * @return array
 * @see cot_breadcrumbs()
 */
function cot_structure_buildpath($area, $cat)
{
	global $structure;
	$tmp = array();
	$pathcodes = explode('.', $structure[$area][$cat]['path']);
	foreach ($pathcodes as $x)
	{
		if ($x != 'system')
		{
			$tmp[] = array(cot_url($area, 'c='.$x), $structure[$area][$x]['title']);
		}
	}
	return $tmp;
}

/**
 * Returns country text button
 *
 * @param string $flag Country code
 * @return string
 */
function cot_build_country($flag)
{
	global $cot_countries;
	if (!$cot_countries) include_once cot_langfile('countries', 'core');
	$flag = (empty($flag)) ? '00' : $flag;
	return cot_rc_link(cot_url('users', 'f=country_'.$flag), $cot_countries[$flag], array(
		'title' => $cot_countries[$flag]
	));
}

/**
 * Returns user email link
 *
 * @param string $email E-mail address
 * @param bool $hide Hide email option
 * @return string
 */
function cot_build_email($email, $hide = false)
{
	global $L;
	if ($hide)
	{
		return $L['Hidden'];
	}
	elseif (!empty($email) && cot_check_email($email))
	{
		$link = cot_rc('link_email', array('email' => $email));
		return function_exists('cot_obfuscate') ? cot_obfuscate($link) : $link;
	}
}

/**
 * Generate human-readable filesize.
 *
 * @param float $bytes
 *	Filesize in bytes
 * @param int $decimals
 *	Number of decimals to show.
 * @param mixed $round
 *	Round up to this number of decimals.
 *	Set false to disable or null to inherit from $decimals.
 * @param bool $binary Use binary instead of decimal calculation.
 *  Set TRUE for the IEC binary standard where 1 Kibibyte = 1024 bytes
 *  Set FALSE for the SI/IEEE decimal standard where 1 Kilobyte = 1000 bytes
 * @param string $smallestunit
 *  Key of the smallest unit to show. Any number smaller than this will return 'Less than 1 ...'.
 *  Effectively its a way to cut off $units at a certain key.
 * @return string
 */
function cot_build_filesize($bytes, $decimals = 0, $round = null, $binary = false, $smallestunit = null)
{
	global $Ls;
	$units = $binary ? array(
		'1099511627776' => $Ls['Tebibytes'],
		'1073741824' => $Ls['Gibibytes'],
		'1048576' => $Ls['Mebibytes'],
		'1024' => $Ls['Kibibytes'],
		'1' => $Ls['Bytes']
	) : array(
		'1000000000000' => $Ls['Terabytes'],
		'1000000000' => $Ls['Gigabytes'],
		'1000000' => $Ls['Megabytes'],
		'1000' => $Ls['Kilobytes'],
		'1' => $Ls['Bytes']
	);
	return cot_build_friendlynumber($bytes, $units, 1, $decimals, $round, $smallestunit);
}

/**
 * Returns country flag button
 *
 * @param string $flag Country code
 * @return string
 */
function cot_build_flag($flag)
{
	global $cot_countries;
	if (!$cot_countries) include_once cot_langfile('countries', 'core');
	$flag = (empty($flag)) ? '00' : $flag;
	return cot_rc_link(cot_url('users', 'f=country_'.$flag),
		cot_rc('icon_flag', array('code' => $flag, 'alt' => $flag)),
		array('title' => $cot_countries[$flag])
	);
}

/**
 * Generic function for generating a human-readable number with localized units.
 *
 * @param float $number
 *	Input number to convert, based on the unit with size (key) 1.
 * @param array $units
 *	Array of units as $relativesize => $unit.
 *  Example: array('3600' => 'hours', '60' => 'minutes', '1' => 'seconds').
 *	Where 'seconds' is the base unit, since it has a value of 1. Hours has a value of
 *	3600, since one hour contains 3600 seconds. Values can be given as strings or integers.
 * @param int $levels
 *	Number of levels to return.
 *	"3 hours 45 minutes" = 2 levels.
 * @param int $decimals
 *	Number of decimals to show in last level.
 *	"2 minutes 20.5 seconds" = 2 levels, 1 decimals.
 * @param mixed $round
 *	Number of decimals to round the last level up to, can also be negative, see round().
 *	Set false to disable or null to inherit from $decimals.
 * @param string $smallestunit
 *  Key of the smallest unit to show. Any number smaller than this will return 'Less than 1 ...'.
 *  Effectively its a way to cut off $units at a certain key.
 * @return string
 */
function cot_build_friendlynumber($number, $units, $levels = 1, $decimals = 0, $round = null, $smallestunit = null)
{
	global $L;
	if (!is_array($units)) return '';
	$pieces = array();

	// First sort from big to small
	ksort($units, SORT_NUMERIC);
	$units = array_reverse($units, true);

	// Trim units after $smallestunit
	if (array_key_exists($smallestunit, $units))
	{
		$offset = array_search($smallestunit, array_keys($units));
		$units = array_slice($units, 0, $offset+1, true);
	}

	if ($number == 0)
	{
		// Return smallest possible unit
		$units = array_reverse(array_values($units));
		return cot_declension(0, $units[0]);
	}

	foreach ($units as $size => $expr)
	{
		$size = floatval($size);
		if ($number >= $size)
		{
			$levels--;
			$num = $number / $size;
			$number -= floor($num) * $size;
			if ($number > 0 && $levels > 0)
			{
				// There's more to come, so no decimals yet.
				$pieces[] = cot_declension(floor($num), $expr);
			}
			else
			{
				// Last item gets decimals and rounding.
				$pieces[] = cot_build_number($num, $decimals, $round). ' ' .
							cot_declension($num, $expr, true, true);
				break;
			}
			if ($levels == 0)
			{
				break;
			}
		}
	}
	if (count($pieces) == 0)
	{
		// Smaller than smallest possible unit
		$expr = array_reverse(array_values($units));
		return $L['LessThan'] . ' ' . cot_declension(1, $expr[0]);
	}
	return implode(' ', $pieces);
}

/**
 * Returns IP Search link
 *
 * @param string $ip IP mask
 * @return string
 */
function cot_build_ipsearch($ip)
{
	global $sys;
	if (!empty($ip))
	{
		if(cot_plugin_active('ipsearch'))
		{
			return cot_rc_link(cot_url('admin', 'm=other&p=ipsearch&a=search&id='.$ip.'&x='.$sys['xk']), $ip);
		}
		else
		{
			return $ip;
		}
	}
	return '';
}

/**
 * Wrapper for number_format() using locale number formatting and optional rounding.
 *
 * @param float $number
 *	Number to format
 * @param int $decimals
 *	Number of decimals to return
 * @param mixed $round
 *	Round up to this number of decimals.
 *	Set false to disable or null to inherit from $decimals.
 * @return string
 */
function cot_build_number($number, $decimals = 0, $round = null)
{
	global $Ln;
	if ($round === null) $round = $decimals;
	if ($round !== false)
	{
		$number = round($number, $round);
	}
	return number_format($number, $decimals, $Ln['decimal_point'], $Ln['thousands_separator']);
}

/**
 * Odd/even class choser for row
 *
 * @param int $number Row number
 * @return string
 */
function cot_build_oddeven($number)
{
	return ($number % 2 == 0 ) ? 'even' : 'odd';
}

/**
 * Returns stars image for user level
 *
 * @param int $level User level
 * @return unknown
 */
function cot_build_stars($level)
{
	if ($level > 0 and $level < 100)
	{
		$stars = floor($level / 10) + 1;
		return cot_rc('icon_stars', array('val' => $stars));
	}
	else
	{
		return '';
	}
}

/**
 * Returns readable time difference or 'Just now'.
 *
 * @param int $time Timestamp
 * @param int $recently Seconds during which to show 'Just now'
 * @return string
 */
function cot_build_timeago($time, $recently = 60)
{
	global $L, $sys;
	if ($sys['now'] - $time < $recently)
	{
		return $L['JustNow'];
	}
	return cot_build_timegap($time) . ' ' . $L['Ago'];
}

/**
 * Returns time gap between two timestamps
 *
 * @param int $t1
 *	Timestamp 1 (oldest, smallest value).
 * @param int $t2
 *	Timestamp 2 (latest, largest value).
 * @param int $levels
 *	Number of concatenated units to return.
 * @param int $decimals
 *	Number of decimals to show on last level.
 * @param mixed $round
 *	Round up last level to this number of decimals.
 *	Set false to disable or null to inherit from $decimals.
 * @param string $smallestunit
 *  Key of the smallest unit to show. Any number smaller than this will return 'Less than 1 ...'.
 *  Effectively its a way to cut off $units at a certain key.
 * @return string
 */
function cot_build_timegap($t1, $t2 = null, $levels = 1, $decimals = 0, $round = null, $smallestunit = null)
{
	global $Ls, $sys;
	$units = array(
		'31536000' => $Ls['Years'],
		'2592000' => $Ls['Months'],
		'604800' => $Ls['Weeks'],
		'86400' => $Ls['Days'],
		'3600' => $Ls['Hours'],
		'60' => $Ls['Minutes'],
		'1' => $Ls['Seconds'],
		'0.001' => $Ls['Milliseconds']
	);
	if ($t2 === null)
	{
		$t2 = $sys['now'];
	}
	$gap = $t2 - $t1;
	return cot_build_friendlynumber($gap, $units, $levels, $decimals, $round, $smallestunit);
}

/**
 * Returns timezone offset formatted according to ISO 8601
 *
 * @param float $offset Timezone offset in seconds or hours. Set NULL for unknown timezone.
 * @param bool $withgmt Include 'GMT' in the returned string.
 * @param bool $short Use format without minutes, like GMT+1
 * @return string Textual timezone like GMT+1:00
 */
function cot_build_timezone($offset, $withgmt = true, $short = false)
{
	$gmt = $withgmt ? 'GMT' : '';
	if (is_null($offset))
	{
		return $short ? "$gmt-00" : "$gmt-00:00";
	}
	if ($offset == 0)
	{
		return $short ? "$gmt+00" : "$gmt+00:00";
	}
	$format = $short ? 'H' : 'H:i';
	$abs = abs($offset);
	$seconds = $abs < 100 ? $abs * 3600 : $abs; // detect hours or seconds
	$time = gmdate($format, $seconds);
	return ($offset > 0) ? "$gmt+$time" : "$gmt-$time";
}

/**
 * Returns link for URL
 *
 * @param string $text URL
 * @param int $maxlen Max. allowed length
 * @return unknown
 */
function cot_build_url($text, $maxlen=64)
{
	if (!empty($text))
	{
		if (mb_strpos($text, 'http://') !== 0)
		{
			$text='http://'. $text;
		}
		$text = htmlspecialchars($text);
		$text = cot_rc_link($text, cot_cutstring($text, $maxlen));
	}
	return $text;
}

/**
 * Returns link to user profile
 *
 * @param int $id User ID
 * @param string $user User name
 * @param mixed $extra_attrs Extra link tag attributes as a string or associative array,
 *		e.g. array('class' => 'usergrp_admin')
 * @return string
 */
function cot_build_user($id, $user, $extra_attrs = '')
{
	if (!$id)
	{
		return empty($user) ? '' : $user;
	}
	else
	{
		return empty($user) ? '?' : cot_rc_link(cot_url('users', 'm=details&id='.$id.'&u='.$user), $user, $extra_attrs);
	}
}

/**
 * Returns group link (button)
 *
 * @param int $grpid Group ID
 * @param bool $title Return group title instead of name
 * @return string
 */
function cot_build_group($grpid, $title = false)
{
	if (empty($grpid))
		return '';
	global $cot_groups, $L;

	$type = ($title) ? 'title' : 'name';
	if ($cot_groups[$grpid]['hidden'])
	{
		if (cot_auth('users', 'a', 'A'))
		{
			return cot_rc_link(cot_url('users', 'gm=' . $grpid), $cot_groups[$grpid][$type] . ' (' . $L['Hidden'] . ')');
		}
		else
		{
			return $L['Hidden'];
		}
	}
	else
	{
		return cot_rc_link(cot_url('users', 'gm=' . $grpid), $cot_groups[$grpid][$type]);
	}
}

/**
 * Returns user group icon
 *
 * @param string $src Image file path
 * @return string
 */
function cot_build_groupicon($src)
{
	return ($src) ? cot_rc("icon_group", array('src' => $src)) : '';
}

/**
 * Returns all user tags for XTemplate
 *
 * @param mixed $user_data User Info Array
 * @param string $tag_prefix Prefix for tags
 * @param string $emptyname Name text if user is not exist
 * @param bool $allgroups Build info about all user groups
 * @param bool $cacheitem Cache tags
 * @return array
 * @global CotDB $db
 */
function cot_generate_usertags($user_data, $tag_prefix = '', $emptyname='', $allgroups = false, $cacheitem = true)
{
	global $db, $cot_extrafields, $cot_groups, $cfg, $L, $user_cache, $db_users;

	static $extp_first = null, $extp_main = null;

	$return_array = array();

	if (is_null($extp_first))
	{
		$extp_first = cot_getextplugins('usertags.first');
		$extp_main = cot_getextplugins('usertags.main');
	}

	/* === Hook === */
	foreach ($extp_first as $pl)
	{
		include $pl;
	}
	/* ===== */

	$user_id = is_array($user_data) ? (int)$user_data['user_id'] : (is_numeric($user_data) ? (int)$user_data : 0);
	if (isset($user_cache[$user_id]))
	{
		$temp_array = $user_cache[$user_id];
	}
	else
	{
		if (!is_array($user_data) && $user_id > 0)
		{
			$sql = $db->query("SELECT * FROM $db_users WHERE user_id = $user_id LIMIT 1");
			$user_data = $sql->fetch();
		}

		if (is_array($user_data) && $user_data['user_id'] > 0 && !empty($user_data['user_name']))
		{
			$user_data['user_birthdate'] = cot_date2stamp($user_data['user_birthdate']);
			$user_data['user_text'] = cot_parse($user_data['user_text'], $cfg['users']['usertextimg']);

			$temp_array = array(
				'ID' => $user_data['user_id'],
				'NAME' => cot_build_user($user_data['user_id'], htmlspecialchars($user_data['user_name'])),
				'NICKNAME' => htmlspecialchars($user_data['user_name']),
				'DETAILSLINK' => cot_url('users', 'm=details&id=' . $user_data['user_id'].'&u='.htmlspecialchars($user_data['user_name'])),
				'DETAILSLINKSHORT' => cot_url('users', 'm=details&id=' . $user_data['user_id']),
				'TITLE' => $cot_groups[$user_data['user_maingrp']]['title'],
				'MAINGRP' => cot_build_group($user_data['user_maingrp']),
				'MAINGRPID' => $user_data['user_maingrp'],
				'MAINGRPNAME' => $cot_groups[$user_data['user_maingrp']]['name'],
				'MAINGRPTITLE' => cot_build_group($user_data['user_maingrp'], true),
				'MAINGRPSTARS' => cot_build_stars($cot_groups[$user_data['user_maingrp']]['level']),
				'MAINGRPICON' => cot_build_groupicon($cot_groups[$user_data['user_maingrp']]['icon']),
				'COUNTRY' => cot_build_country($user_data['user_country']),
				'COUNTRYFLAG' => cot_build_flag($user_data['user_country']),
				'TEXT' => $user_data['user_text'],
				'EMAIL' => cot_build_email($user_data['user_email'], $user_data['user_hideemail']),
				'THEME' => $user_data['user_theme'],
				'SCHEME' => $user_data['user_scheme'],
				'GENDER' => ($user_data['user_gender'] == '' || $user_data['user_gender'] == 'U') ? '' : $L['Gender_' . $user_data['user_gender']],
				'BIRTHDATE' => (is_null($user_data['user_birthdate'])) ? '' : cot_date('date_full', $user_data['user_birthdate']),
				'BIRTHDATE_STAMP' => (is_null($user_data['user_birthdate'])) ? '' : $user_data['user_birthdate'],
				'AGE' => (is_null($user_data['user_birthdate'])) ? '' : cot_build_age($user_data['user_birthdate']),
				'TIMEZONE' => cot_build_timezone(cot_timezone_offset($user_data['user_timezone'], false, false)) . ' ' .str_replace('_', ' ', $user_data['user_timezone']),
				'REGDATE' => cot_date('datetime_medium', $user_data['user_regdate']),
				'REGDATE_STAMP' => $user_data['user_regdate'],
				'LASTLOG' => cot_date('datetime_medium', $user_data['user_lastlog']),
				'LASTLOG_STAMP' => $user_data['user_lastlog'],
				'LOGCOUNT' => $user_data['user_logcount'],
				'POSTCOUNT' => $user_data['user_postcount'],
				'LASTIP' => $user_data['user_lastip']
			);

			if ($allgroups)
			{
				$temp_array['GROUPS'] = cot_build_groupsms($user_data['user_id'], FALSE, $user_data['user_maingrp']);
			}
			// Extra fields
			if (isset($cot_extrafields[$db_users]))
			{
				foreach ($cot_extrafields[$db_users] as $exfld)
				{
					$temp_array[strtoupper($exfld['field_name'])] = cot_build_extrafields_data('user', $exfld, $user_data['user_' . $exfld['field_name']]);
					$temp_array[strtoupper($exfld['field_name']) . '_TITLE'] = isset($L['user_' . $exfld['field_name'] . '_title']) ? $L['user_' . $exfld['field_name'] . '_title'] : $exfld['field_description'];
				}
			}
		}
		else
		{
			$temp_array = array(
				'ID' => 0,
				'NAME' => (!empty($emptyname)) ? $emptyname : $L['Deleted'],
				'NICKNAME' => (!empty($emptyname)) ? $emptyname : $L['Deleted'],
				'MAINGRP' => cot_build_group(1),
				'MAINGRPID' => 1,
				'MAINGRPSTARS' => '',
				'MAINGRPICON' => cot_build_groupicon($cot_groups[1]['icon']),
				'COUNTRY' => cot_build_country(''),
				'COUNTRYFLAG' => cot_build_flag(''),
				'TEXT' => '',
				'EMAIL' => '',
				'GENDER' => '',
				'BIRTHDATE' => '',
				'BIRTHDATE_STAMP' => '',
				'AGE' => '',
				'REGDATE' => '',
				'REGDATE_STAMP' => '',
				'POSTCOUNT' => '',
				'LASTIP' => ''
			);
		}

		/* === Hook === */
		foreach ($extp_main as $pl)
		{
			include $pl;
		}
		/* ===== */

		$cacheitem && $user_cache[$user_data['user_id']] = $temp_array;
	}
	foreach ($temp_array as $key => $val)
	{
		$return_array[$tag_prefix . $key] = $val;
	}
	return $return_array;
}

/**
 * Resize an image
 *
 * @param string $source Original image path.
 * @param string $target Target path for saving, or 'return' to return the resized image data directly.
 * @param int $target_width Maximum width of resized image.
 * @param int $target_height Maximum height of resized image.
 * @param string $crop Crop the image to a certain ratio. Set to 'fit' to calculate ratio from target width and height.
 * @param string $fillcolor Color fill a transparent gif or png.
 * @param int $quality JPEG quality
 * @param bool $sharpen Sharpen JPEG image after resize.
 * @return mixed Boolean or image resource, depending on $target
 */
function cot_imageresize($source, $target='return', $target_width=99999, $target_height=99999, $crop='', $fillcolor='', $quality=90, $sharpen=true)
{
	if (!file_exists($source)) return;
	$source_size = getimagesize($source);
	if(!$source_size) return;
	$mimetype = $source_size['mime'];
	if (substr($mimetype, 0, 6) != 'image/') return;

	// Prevent from loading images taking more than 100M of memory
	if (!isset($source_size['channels'])) $source_size['channels'] = 1;
	$required_memory = $source_size[0] * $source_size[1] * ($source_size['bits'] / 8) * $source_size['channels'] * 2.5;
	$memory_limit = trim(ini_get('memory_limit'));
	$last = strtolower($memory_limit[strlen($memory_limit)-1]);
	switch ($last)
	{
		case 'g':
			$memory_limit *= 1024;
		case 'm':
			$memory_limit *= 1024;
		case 'k':
			$memory_limit *= 1024;
	}
	if ($memory_limit > 0)
	{
		$avail_memory = $memory_limit - memory_get_usage();
		if ($required_memory > $avail_memory * 0.7)
		{
			return;
		}
	}

	$source_width = $source_size[0];
	$source_height = $source_size[1];
	if($target_width > $source_width) $target_width = $source_width; $noscaling_x = true;
	if($target_height > $source_height) $target_height = $source_height; $noscaling_y = true;

	$fillcolor = preg_replace('/[^0-9a-fA-F]/', '', (string)$fillcolor);
	if (!$fillcolor && $noscaling_x && $noscaling_y)
	{
		$data = file_get_contents($source);
		if($target == 'return') return $data;
	}

	$offsetX = 0;
	$offsetY = 0;

	if($crop)
	{
		$crop = ($crop == 'fit') ? array($target_width, $target_height) : explode(':', (string)$crop);
		if(count($crop) == 2)
		{
			$source_ratio = $source_width / $source_height;
			$target_ratio = (float)$crop[0] / (float)$crop[1];

			if ($source_ratio < $target_ratio)
			{
				$temp = $source_height;
				$source_height = $source_width / $target_ratio;
				$offsetY = ($temp - $source_height) / 2;
			}
			if ($source_ratio > $target_ratio)
			{
				$temp = $source_width;
				$source_width = $source_height * $target_ratio;
				$offsetX = ($temp - $source_width) / 2;
			}
		}
	}

	$width_ratio = $target_width / $source_width;
	$height_ratio = $target_height / $source_height;
	if ($width_ratio * $source_height < $target_height)
	{
		$target_height = ceil($width_ratio * $source_height);
	}
	else
	{
		$target_width = ceil($height_ratio * $source_width);
	}

	//ini_set('memory_limit', '100M');
	$canvas = imagecreatetruecolor($target_width, $target_height);

	switch($mimetype)
	{
		case 'image/gif':
			$fn_create = 'imagecreatefromgif';
			$fn_output = 'imagegif';
			$mimetype = 'image/gif';
			//$quality = round(10 - ($quality / 10));
			$sharpen = false;
		break;

		case 'image/x-png':
		case 'image/png':
			$fn_create = 'imagecreatefrompng';
			$fn_output = 'imagepng';
			$quality = round(10 - ($quality / 10));
			$sharpen = false;
		break;

		default:
			$fn_create = 'imagecreatefromjpeg';
			$fn_output = 'imagejpeg';
			$sharpen = ($target_width < 75 || $target_height < 75) ? false : $sharpen;
		break;
	}
	$source_data = $fn_create($source);

	if (in_array($mimetype, array('image/gif', 'image/png')))
	{
		if (!$fillcolor)
		{
			imagealphablending($canvas, false);
			imagesavealpha($canvas, true);
		}
		elseif(strlen($fillcolor) == 6 || strlen($fillcolor) == 3)
		{
			$background	= (strlen($fillcolor) == 6) ?
				imagecolorallocate($canvas, hexdec($fillcolor[0].$fillcolor[1]), hexdec($fillcolor[2].$fillcolor[3]), hexdec($fillcolor[4].$fillcolor[5])):
				imagecolorallocate($canvas, hexdec($fillcolor[0].$fillcolor[0]), hexdec($fillcolor[1].$fillcolor[1]), hexdec($fillcolor[2].$fillcolor[2]));
			imagefill($canvas, 0, 0, $background);
		}
	}
	imagecopyresampled($canvas, $source_data, 0, 0, $offsetX, $offsetY, $target_width, $target_height, $source_width, $source_height);
	imagedestroy($source_data);
	$canvas = ($sharpen) ? cot_imagesharpen($canvas, $source_width, $target_width) : $canvas;

	if($target == 'return')
	{
		ob_start();
		$fn_output($canvas, null, $quality);
		$data = ob_get_contents();
		ob_end_clean();
		imagedestroy($canvas);
		return $data;
	}
	else
	{
		$result = $fn_output($canvas, $target, $quality);
		imagedestroy($canvas);
		return $result;
	}
}

// Fix for non-bundled GD versions
// Made by Chao Xu(Mgccl) 2/28/07
// www.webdevlogs.com
// V 1.0
if (!function_exists('imageconvolution'))
{
	function imageconvolution($src, $filter, $filter_div, $offset)
	{
		if ($src == NULL)
		{
			return 0;
		}

		$sx = imagesx($src);
		$sy = imagesy($src);
		$srcback = imagecreatetruecolor($sx, $sy);
		imagecopy($srcback, $src, 0, 0, 0, 0, $sx, $sy);

		if ($srcback == NULL)
		{
			return 0;
		}
		// Fix here
		// $pxl array was the problem so simply set it with very low values
		$pxl = array(1, 1);
		// this little fix worked for me as the undefined array threw out errors

		for ($y = 0; $y < $sy; ++$y)
		{
			for ($x = 0; $x < $sx; ++$x)
			{
				$new_r = $new_g = $new_b = 0;
				$alpha = imagecolorat($srcback, $pxl[0], $pxl[1]);
				$new_a = $alpha >> 24;

				for ($j = 0; $j < 3; ++$j)
				{
					$yv = min(max($y - 1 + $j, 0), $sy - 1);
					for ($i = 0; $i < 3; ++$i)
					{
						$pxl = array(min(max($x - 1 + $i, 0), $sx - 1), $yv);
						$rgb = imagecolorat($srcback, $pxl[0], $pxl[1]);
						$new_r += ( ($rgb >> 16) & 0xFF) * $filter[$j][$i];
						$new_g += ( ($rgb >> 8) & 0xFF) * $filter[$j][$i];
						$new_b += ( $rgb & 0xFF) * $filter[$j][$i];
					}
				}

				$new_r = ($new_r / $filter_div) + $offset;
				$new_g = ($new_g / $filter_div) + $offset;
				$new_b = ($new_b / $filter_div) + $offset;

				$new_r = ($new_r > 255) ? 255 : (($new_r < 0) ? 0 : $new_r);
				$new_g = ($new_g > 255) ? 255 : (($new_g < 0) ? 0 : $new_g);
				$new_b = ($new_b > 255) ? 255 : (($new_b < 0) ? 0 : $new_b);

				$new_pxl = imagecolorallocatealpha($src, (int) $new_r, (int) $new_g, (int) $new_b, $new_a);
				if ($new_pxl == -1)
				{
					$new_pxl = imagecolorclosestalpha($src, (int) $new_r, (int) $new_g, (int) $new_b, $new_a);
				}
				if (($y >= 0) && ($y < $sy))
				{
					imagesetpixel($src, $x, $y, $new_pxl);
				}
			}
		}
		imagedestroy($srcback);
		return 1;
	}
}

/**
 * Sharpen an image after resize
 *
 * @param image resource $imgdata Image resource from an image creation function
 * @param int $source_width Width of image before resize
 * @param int $target_width Width of image to sharpen (after resize)
 * @return image resource
 */
function cot_imagesharpen($imgdata, $source_width, $target_width)
{
	$s = $target_width * (750.0 / $source_width);
	$a = 52;
	$b = -0.27810650887573124;
	$c = .00047337278106508946;
	$sharpness = max(round($a+$b*$s+$c*$s*$s), 0);
	$sharpenmatrix = array(
		array(-1, -2, -1),
		array(-2, $sharpness + 12, -2),
		array(-1, -2, -1)
	);
	imageconvolution($imgdata, $sharpenmatrix, $sharpness, 0);
	return $imgdata;
}

/**
 * Returns Theme/Scheme selection dropdown
 *
 * @param string $selected_theme Seleced theme
 * @param string $selected_scheme Seleced color scheme
 * @param string $name Dropdown name
 * @return string
 */
function cot_selectbox_theme($selected_theme, $selected_scheme, $input_name)
{
	global $cfg;
	require_once cot_incfile('extensions');
	$handle = opendir($cfg['themes_dir']);
	while ($f = readdir($handle))
	{
		if (mb_strpos($f, '.') === FALSE && is_dir("{$cfg['themes_dir']}/$f") && $f != "admin")
		{
			$themelist[] = $f;
		}
	}
	closedir($handle);
	sort($themelist);

	$values = array();
	$titles = array();
	foreach ($themelist as $x)
	{
		$themeinfo = "{$cfg['themes_dir']}/$x/$x.php";
		if (file_exists($themeinfo))
		{
			$info = cot_infoget($themeinfo, 'COT_THEME');
			if ($info)
			{
				if (empty($info['Schemes']))
				{
					$values[] = "$x:default";
					$titles[] = $info['Name'];
				}
				else
				{
					$schemes = explode(',', $info['Schemes']);
					sort($schemes);
					foreach ($schemes as $sc)
					{
						$sc = explode(':', $sc);
						$values[] = $x . ':' . $sc[0];
						$titles[] = count($schemes) > 1 ? $info['Name'] .  ' (' . $sc[1] . ')' : $info['Name'];
					}
				}
			}
			else
			{
				$values[] = "$x:default";
				$titles[] = $x;
			}
		}
		else
		{
			$values[] = "$x:default";
			$titles[] = $x;
		}
	}

	return cot_selectbox("$selected_theme:$selected_scheme", $input_name, $values, $titles, false);
}

/*
 * ======================== Error & Message + Logs API ========================
 */

/**
 * If condition is true, triggers an error with given message and source
 *
 * @param bool $condition Boolean condition
 * @param string $message Error message or message key
 * @param string $src Error source field name
 */
function cot_check($condition, $message, $src = 'default')
{
	if ($condition)
	{
		cot_error($message, $src);
	}
}

/**
 * Checks if there are messages to display
 *
 * @param string $src If non-emtpy, check messages in this specific source only
 * @param string $class If non-empty, check messages of this specific class only
 * @return bool
 */
function cot_check_messages($src = '', $class = '')
{
	global $error_string;

	if (empty($src) && empty($class))
	{
		return (is_array($_SESSION['cot_messages']) && count($_SESSION['cot_messages']) > 0)
			|| !empty($error_string);
	}

	if (!is_array($_SESSION['cot_messages']))
	{
		return false;
	}

	if (empty($src))
	{
		foreach ($_SESSION['cot_messages'] as $src => $grp)
		{
			foreach ($grp as $msg)
			{
				if ($msg['class'] == $class)
				{
					return true;
				}
			}
		}
	}
	elseif (empty($class))
	{
		return count($_SESSION['cot_messages'][$src]) > 0;
	}
	else
	{
		foreach ($_SESSION['cot_messages'][$src] as $msg)
		{
			if ($msg['class'] == $class)
			{
				return true;
			}
		}
	}

	return false;
}

/**
 * Clears error and other messages after they have bin displayed
 * @param string $src If non-emtpy, clear messages in this specific source only
 * @param string $class If non-empty, clear messages of this specific class only
 * @see cot_error()
 * @see cot_message()
 */
function cot_clear_messages($src = '', $class = '')
{
	global $error_string;

	if (empty($src) && empty($class))
	{
		unset($_SESSION['cot_messages']);
		unset($error_string);
	}

	if (!is_array($_SESSION['cot_messages']))
	{
		return;
	}

	if (empty($src))
	{
		foreach ($_SESSION['cot_messages'] as $src => $grp)
		{
			$new_grp = array();
			foreach ($grp as $msg)
			{
				if ($msg['class'] != $class)
				{
					$new_grp[] = $msg;
				}
			}
			if (count($new_grp) > 0)
			{
				$_SESSION['cot_messages'][$src] = $new_grp;
			}
			else
			{
				unset($_SESSION['cot_messages'][$src]);
			}
		}
	}
	elseif (empty($class))
	{
		unset($_SESSION['cot_messages'][$src]);
	}
	else
	{
		$new_grp = array();
		foreach ($_SESSION['cot_messages'][$src] as $msg)
		{
			if ($msg['class'] != $class)
			{
				$new_grp[] = $msg;
			}
		}
		if (count($new_grp) > 0)
		{
			$_SESSION['cot_messages'][$src] = $new_grp;
		}
		else
		{
			unset($_SESSION['cot_messages'][$src]);
		}
	}
}

/**
 * Terminates script execution and performs redirect
 *
 * @param bool $cond Really die?
 * @param bool $notfound Page not found?
 * @return bool
 */
function cot_die($cond = true, $notfound = false)
{
	if ($cond)
	{
		$msg = $notfound ? '404' : '950';

		cot_die_message($msg, true);
	}
	return FALSE;
}

/**
 * Terminates script execution with fatal error
 *
 * @param string $text Reason
 * @param string $title Message title
 */
function cot_diefatal($text='Reason is unknown.', $title='Fatal error')
{
	global $cfg;

	if ($cfg['display_errors'])
	{
		$message_body = '<p><em>'.@date('Y-m-d H:i').'</em></p>';
		$message_body .= '<p>'.$text.'</p>';
		ob_clean();
		debug_print_backtrace();
		$backtrace = ob_get_contents();
		ob_clean();
		$message_body .= '<pre style="overflow:auto">'.$backtrace.'</pre>';
		$message_body .= '<hr /><a href="'.$cfg['mainurl'].'">'.$cfg['maintitle'].'</a>';
		cot_die_message(500, true, $title, $message_body);
	}
	else
	{
		$backtrace = debug_backtrace();
		if (isset($backtrace[1]))
		{
			$text .= ' in file ' . $backtrace[1]['file'] . ' at line ' . $backtrace[1]['line'] . ' function ' . $backtrace[1]['function'] . '(' . implode(', ', $backtrace[1]['args']) . ')';
		}
		error_log("$title: $text");
		cot_die_message(503, true);
	}
}

/**
 * Terminates script execution and displays message page
 *
 * @param int $code Message code
 * @param bool $header Render page header
 */
function cot_die_message($code, $header = TRUE, $message_title = '', $message_body = '')
{
	// Globals and requirements
	global $error_string, $out, $L, $R;
    $LL = is_array($L) ? $L : array();
    require_once cot_langfile('message', 'core');
    $L = array_merge($L, $LL);

	if (cot_error_found() && $_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// Save the POST data
		cot_import_buffer_save();
		if (!empty($error_string))
		{
			// Message should not be lost
			cot_error($error_string);
		}
	}
	// Determine response header
	static $msg_status = array(
		100 => '403 Forbidden',
		101 => '200 OK',
		102 => '200 OK',
		105 => '200 OK',
		106 => '200 OK',
		109 => '200 OK',
		117 => '403 Forbidden',
		118 => '200 OK',
		151 => '403 Forbidden',
		152 => '403 Forbidden',
		153 => '403 Forbidden',
		157 => '403 Forbidden',
		300 => '200 OK',
		400 => '400 Bad Request',
		401 => '401 Authorization Required',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		500 => '500 Internal Server Error',
		503 => '503 Service Unavailable',
		602 => '403 Forbidden',
		603 => '403 Forbidden',
		900 => '503 Service Unavailable',
		904 => '403 Forbidden',
		907 => '404 Not Found',
		911 => '404 Not Found',
		915 => '200 OK',
		916 => '200 OK',
		920 => '200 OK',
		930 => '403 Forbidden',
		940 => '403 Forbidden',
		950 => '403 Forbidden',
		951 => '503 Service Unavailable'
	);

	if (!$out['meta_contenttype'])
	{
		$out['meta_contenttype'] = 'text/html';
	}
	cot_sendheaders($out['meta_contenttype'], $msg_status[$code]);

	// Determine message title and body
	$title = empty($message_title) ? $L['msg' . $code . '_title'] : $message_title;
	$body = empty($message_body) ? $L['msg' . $code . '_body'] : $message_body;

	// Render the message page
	$tpl_type = defined('COT_ADMIN') ? 'core' : 'module';
	$tpl_path = '';
	$stylesheet = file_exists(cot_schemefile()) ? '<link rel="stylesheet" type="text/css" href="'.cot_schemefile().'"/>' : '';
	if ($header)
	{
		$tpl_path = cot_tplfile("error.$code", $tpl_type);
		if ($tpl_path)
		{
			$header = false;
		}
		else
		{
			echo '<html><head><title>'.$title.'</title><meta name="robots" content="noindex" />'.$R['code_basehref'].$stylesheet.'</head><body><div class="block">';
		}
	}

	if (empty($tpl_path))
	{
		$tpl_path = cot_tplfile('message', $tpl_type);
	}

	if (empty($tpl_path) || !file_exists($tpl_path))
	{
		echo $body;
	}
	else
	{
		$t = new XTemplate($tpl_path);

		$t->assign(array(
			'AJAX_MODE' => COT_AJAX,
			'MESSAGE_BASEHREF' => $R['code_basehref'],
			'MESSAGE_STYLESHEET' => $stylesheet,
			'MESSAGE_TITLE' => $title,
			'MESSAGE_BODY' => $body
		));

		$t->parse('MAIN');
		$t->out('MAIN');
	}

	if ($header)
	{
		echo '</div></body></html>';
	}
	exit;
}

/**
 * Renders different messages on page
 *
 * @param XTemplate $tpl Current template object reference
 * @param string $block Current template block
 */
function cot_display_messages($tpl, $block = 'MAIN')
{
	global $L;
	if (!cot_check_messages())
	{
		return;
	}
	$block = (!empty($block)) ? $block.'.' : '';
	$errors = cot_get_messages('', 'error');
	if (count($errors) > 0)
	{
		foreach ($errors as $msg)
		{
			$text = isset($L[$msg['text']]) ? $L[$msg['text']] : $msg['text'];
			$tpl->assign('ERROR_ROW_MSG', $text);
			$tpl->parse($block.'ERROR.ERROR_ROW');
		}
		$tpl->parse($block.'ERROR');
	}
	$warnings = cot_get_messages('', 'warning');
	if (count($warnings) > 0)
	{
		foreach ($warnings as $msg)
		{
			$text = isset($L[$msg['text']]) ? $L[$msg['text']] : $msg['text'];
			$tpl->assign('WARNING_ROW_MSG', $text);
			$tpl->parse($block.'WARNING.WARNING_ROW');
		}
		$tpl->parse($block.'WARNING');
	}
	$okays = cot_get_messages('', 'ok');
	if (count($okays) > 0)
	{
		foreach ($okays as $msg)
		{
			$text = isset($L[$msg['text']]) ? $L[$msg['text']] : $msg['text'];
			$tpl->assign('DONE_ROW_MSG', $text);
			$tpl->parse($block.'DONE.DONE_ROW');
		}
		$tpl->parse($block.'DONE');
	}
	cot_clear_messages();
}

/**
 * Records an error message to be displayed on results page
 *
 * @global int $cot_error Global error counter
 * @param string $message Message lang string code or full text
 * @param string $src Error source identifier, such as field name for invalid input
 * @see cot_message()
 */
function cot_error($message, $src = 'default')
{
	global $cot_error;
	$cot_error ? $cot_error++ : $cot_error = 1;
	cot_message($message, 'error', $src);
}

/**
 * Checks if any errors have been previously detected during current script execution
 *
 * @global int $cot_error Global error counter
 * @global string $error_string Obsolete error message container string
 * @return bool TRUE if any errors were found, FALSE otherwise
 */
function cot_error_found()
{
	global $cot_error, $error_string;
	return (bool) $cot_error || !empty($error_string);
}

/**
 * Returns an array of messages for a specific source and/or class
 *
 * @param string $src Message source identifier. Search in all sources if empty
 * @param string $class Message class. Search for all classes if empty
 * @return array Array of message strings
 */
function cot_get_messages($src = 'default', $class = '')
{
	$messages = array();
	if (empty($src) && empty($class))
	{
		return $_SESSION['cot_messages'];
	}

	if (!is_array($_SESSION['cot_messages']))
	{
		return $messages;
	}

	if (empty($src))
	{
		foreach ($_SESSION['cot_messages'] as $src => $grp)
		{
			foreach ($grp as $msg)
			{
				if (!empty($class) && $msg['class'] != $class)
				{
					continue;
				}
				$messages[] = $msg;
			}
		}
	}
	elseif (is_array($_SESSION['cot_messages'][$src]))
	{
		if (empty($class))
		{
			return $_SESSION['cot_messages'][$src];
		}
		else
		{
			foreach ($_SESSION['cot_messages'][$src] as $msg)
			{
				if ($msg['class'] != $class)
				{
					continue;
				}
				$messages[] = $msg;
			}
		}
	}
	return $messages;
}

/**
 * Collects all messages and implodes them into a single string
 * @param string $src Origin of the target messages
 * @param string $class Group messages of selected class only. Empty to group all
 * @return string Composite HTML string
 * @see cot_error()
 * @see cot_get_messages()
 * @see cot_message()
 */
function cot_implode_messages($src = 'default', $class = '')
{
	global $R, $L, $error_string;
	$res = '';

	if (!is_array($_SESSION['cot_messages']))
	{
		return;
	}

	$messages = cot_get_messages($src, $class);
	foreach ($messages as $msg)
	{
		$text = isset($L[$msg['text']]) ? $L[$msg['text']] : $msg['text'];
		$res .= cot_rc('code_msg_line', array('class' => $msg['class'], 'text' => $text));
	}

	if (!empty($error_string) && (empty($class) || $class == 'error'))
	{
		$res .= cot_rc('code_msg_line', array('class' => 'error', 'text' => $error_string));
	}
	return empty($res) ? '' : cot_rc('code_msg_begin', array('class' => empty($class) ? 'message' : $class))
		. $res . $R['code_msg_end'];
}

/**
 * Logs an event
 *
 * @param string $text Event description
 * @param string $group Event group
 * @global CotDB $db
 */
function cot_log($text, $group='def')
{
	global $db, $db_logger, $sys, $usr, $_SERVER;

	$db->insert($db_logger, array(
		'log_date' => (int)$sys['now'],
		'log_ip' => $usr['ip'],
		'log_name' => $usr['name'],
		'log_group' => $group,
		'log_text' => $text.' - '.$_SERVER['REQUEST_URI']
		));
}

/**
 * Logs wrong input
 *
 * @param string $s Source type
 * @param string $e Filter type
 * @param string $v Variable name
 * @param string $o Value
 */
function cot_log_import($s, $e, $v, $o)
{
	if ($e == 'PSW') $o = str_repeat('*', mb_strlen($o));
	$text = "A variable type check failed, expecting ".$s."/".$e." for '".$v."' : ".$o;
	cot_log($text, 'sec');
}

/**
 * Records a generic message to be displayed on results page
 * @param string $text Message lang string code or full text
 * @param string $class Message class: 'status', 'error', 'ok', 'notice', etc.
 * @param string $src Message source identifier
 * @see cot_error()
 */
function cot_message($text, $class = 'ok', $src = 'default')
{
	global $cfg;
	if (!$cfg['msg_separate'])
	{
		// Force the src to default if all errors are displayed in the same place
		$src = 'default';
	}
	$_SESSION['cot_messages'][$src][] = array(
		'text' => $text,
		'class' => $class
	);
}

/*
 * =============================== File Path Functions ========================
*/

/**
 * Returns path to include file
 *
 * @param string $name Extension or API name
 * @param string $type Extension type: 'module', 'plug' or 'core' for core API
 * @param string $part Name of the extension part
 * @return string File path
 */
function cot_incfile($name, $type = 'core', $part = 'functions')
{
	global $cfg;
	if ($type == 'core')
	{
		return $cfg['system_dir'] . "/$name.php";
	}
	elseif ($type == 'plug')
	{
		return $cfg['plugins_dir']."/$name/inc/$name.$part.php";
	}
	elseif ($name == 'admin')
	{
		// Built-in extensions
		return $cfg['system_dir']."/$name/$name.$part.php";
	}
	else
	{
		return $cfg['modules_dir']."/$name/inc/$name.$part.php";
	}
}

/**
 * Returns a language file path for an extension or core part.
 *
 * @param string $name Part name (area code or plugin name)
 * @param string $type Part type: 'plug', 'module' or 'core'
 * @param string $default Default (fallback) language code
 * @param string $lang Set this to override global $lang
 * @return string
 */
function cot_langfile($name, $type = 'plug', $default = 'en', $lang = null)
{
	global $cfg;
	if (!is_string($lang))
	{
		global $lang;
	}
	if ($type == 'module')
	{
		if (@file_exists($cfg['lang_dir']."/$lang/modules/$name.$lang.lang.php"))
		{
			return $cfg['lang_dir']."/$lang/modules/$name.$lang.lang.php";
		}
		elseif (@file_exists($cfg['modules_dir']."/$name/lang/$name.$lang.lang.php"))
		{
			return $cfg['modules_dir']."/$name/lang/$name.$lang.lang.php";
		}
		else
		{
			return $cfg['modules_dir']."/$name/lang/$name.$default.lang.php";
		}
	}
	elseif ($type == 'core')
	{
		if (@file_exists($cfg['lang_dir']."/$lang/$name.$lang.lang.php"))
		{
			return $cfg['lang_dir']."/$lang/$name.$lang.lang.php";
		}
		else
		{
			return $cfg['lang_dir']."/$default/$name.$default.lang.php";
		}
	}
	else
	{
		if (@file_exists($cfg['lang_dir']."/$lang/plugins/$name.$lang.lang.php"))
		{
			return $cfg['lang_dir']."/$lang/plugins/$name.$lang.lang.php";
		}
		elseif (@file_exists($cfg['plugins_dir']."/$name/lang/$name.$lang.lang.php"))
		{
			return $cfg['plugins_dir']."/$name/lang/$name.$lang.lang.php";
		}
		else
		{
			return $cfg['plugins_dir']."/$name/lang/$name.$default.lang.php";
		}
	}
}

/**
 * Tries to detect and fetch a user scheme CSS file or returns FALSE on error.
 *
 * @global array $usr User object
 * @global array $cfg Configuration
 * @global array $out Output vars
 * @return mixed
 */
function cot_schemefile()
{
	global $usr, $cfg, $out;

	if (file_exists("{$cfg['themes_dir']}/{$usr['theme']}/{$usr['scheme']}.css"))
	{
		return "{$cfg['themes_dir']}/{$usr['theme']}/{$usr['scheme']}.css";
	}
	elseif (is_dir("{$cfg['themes_dir']}/{$usr['theme']}/css/"))
	{
		if (file_exists("{$cfg['themes_dir']}/{$usr['theme']}/css/{$usr['scheme']}.css"))
		{
			return "{$cfg['themes_dir']}/{$usr['theme']}/css/{$usr['scheme']}.css";
		}
		elseif (file_exists("{$cfg['themes_dir']}/{$usr['theme']}/css/{$cfg['defaultscheme']}.css"))
		{
			$out['notices_array'][] = $L['com_schemefail'];
			$usr['scheme'] = $cfg['defaultscheme'];
			return "{$cfg['themes_dir']}/{$usr['theme']}/css/{$cfg['defaultscheme']}.css";
		}
	}
	elseif (is_dir("{$cfg['themes_dir']}/{$usr['theme']}"))
	{
		if (file_exists("{$cfg['themes_dir']}/{$usr['theme']}/{$cfg['defaultscheme']}.css"))
		{
			$out['notices_array'][] = $L['com_schemefail'];
			$usr['scheme'] = $cfg['defaultscheme'];
			return "{$cfg['themes_dir']}/{$usr['theme']}/{$cfg['defaultscheme']}.css";
		}
		elseif (file_exists("{$cfg['themes_dir']}/{$usr['theme']}/{$usr['theme']}.css"))
		{
			$out['notices_array'][] = $L['com_schemefail'];
			$usr['scheme'] = $usr['theme'];
			return "{$cfg['themes_dir']}/{$usr['theme']}/{$usr['theme']}.css";
		}
		elseif (file_exists("{$cfg['themes_dir']}/{$usr['theme']}/style.css"))
		{
			$out['notices_array'][] = $L['com_schemefail'];
			$usr['scheme'] = 'style';
			return "{$cfg['themes_dir']}/{$usr['theme']}/style.css";
		}
	}

	$out['notices_array'][] = $L['com_schemefail'];
	if (file_exists("{$cfg['themes_dir']}/{$cfg['defaulttheme']}/{$cfg['defaultscheme']}.css"))
	{
		$usr['theme'] = $cfg['defaulttheme'];
		$usr['scheme'] = $cfg['defaultscheme'];
		return "{$cfg['themes_dir']}/{$cfg['defaulttheme']}/{$cfg['defaultscheme']}.css";
	}
	elseif (file_exists("{$cfg['themes_dir']}/{$cfg['defaulttheme']}/css/{$cfg['defaultscheme']}.css"))
	{
		$usr['theme'] = $cfg['defaulttheme'];
		$usr['scheme'] = $cfg['defaultscheme'];
		return "{$cfg['themes_dir']}/{$cfg['defaulttheme']}/css/{$cfg['defaultscheme']}.css";
	}
	else
	{
		return false;
	}
}

/**
 * Returns path to a template file. The default search order is:
 * 1) Current theme folder (plugins/ subdir for plugins, admin/ subdir for admin)
 * 2) Default theme folder (if current is not default)
 * 3) tpl subdir in module/plugin folder (fallback template)
 *
 * @param mixed $base Item name (string), or base names (array)
 * @param string $type Extension type: 'plug', 'module' or 'core'
 * @param bool $admin Use admin theme file if present. Tries to determine from base string by default.
 * @return string
 */
function cot_tplfile($base, $type = 'module', $admin = null)
{
	global $usr, $cfg;

	// Get base name parts
	if (is_string($base) && mb_strpos($base, '.') !== false)
	{
		$base = explode('.', $base);
	}
	if (!is_array($base))
	{
		$base = array($base);
	}
	if (is_null($admin))
	{
		$admin = ($base[0] == 'admin' || ($base[1] && $base[1] == 'admin'));
	}
	$scan_dirs = array();

	// Possible search directories depending on extension type
	if ($type == 'plug')
	{
		// Plugin template paths
		$admin && !empty($cfg['admintheme']) && $scan_dirs[] = "{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/plugins/";
		$admin && $scan_dirs[] = "{$cfg['themes_dir']}/{$usr['theme']}/admin/plugins/";
		$scan_dirs[] = "{$cfg['themes_dir']}/{$usr['theme']}/plugins/";
		$scan_dirs[] = "{$cfg['themes_dir']}/{$usr['theme']}/plugins/{$base[0]}/";
		$scan_dirs[] = "{$cfg['plugins_dir']}/{$base[0]}/tpl/";
	}
	elseif ($type == 'core' && in_array($base[0], array('admin', 'header', 'footer', 'message')))
	{
		// Built-in core modules
		!empty($cfg['admintheme']) && $scan_dirs[] = "{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/";
		$scan_dirs[] = "{$cfg['themes_dir']}/{$usr['theme']}/admin/";
		$scan_dirs[] = "{$cfg['system_dir']}/admin/tpl/";
	}
	else
	{
		// Module template paths
		$admin && !empty($cfg['admintheme']) && $scan_dirs[] = "{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/modules/";
		$admin && $scan_dirs[] = "{$cfg['themes_dir']}/{$usr['theme']}/admin/modules/";
		$scan_dirs[] = "{$cfg['themes_dir']}/{$usr['theme']}/";
		$scan_dirs[] = "{$cfg['themes_dir']}/{$usr['theme']}/modules/";
		$scan_dirs[] = "{$cfg['themes_dir']}/{$usr['theme']}/modules/{$base[0]}/";
		$scan_dirs[] = "{$cfg['modules_dir']}/{$base[0]}/tpl/";
	}

	// Build template file name from base parts glued with dots
	$base_depth = count($base);
	for ($i = $base_depth; $i > 0; $i--)
	{
		$levels = array_slice($base, 0, $i);
		$themefile = implode('.', $levels) . '.tpl';
		// Search in all available directories
		foreach ($scan_dirs as $dir)
		{
			if (file_exists($dir . $themefile))
			{
				return $dir . $themefile;
			}
		}
	}

	return false;
}

/*
 * ============================ Date and Time Functions =======================
*/

/**
 * Localized version of PHP date()
 *
 * @see http://php.net/manual/en/function.date.php
 * @param string $format Date/time format as defined in $Ldt or according to PHP date() format
 * @param int $timestamp Unix timestamp
 * @param bool $usertimezone Offset the date with current user's timezone
 * @return string
 */
function cot_date($format, $timestamp = null, $usertimezone = true)
{
	global $lang, $L, $Ldt, $usr, $sys;
	if (is_null($timestamp))
	{
		$timestamp = $sys['now'];
	}
	if ($usertimezone)
	{
		$timestamp += $usr['timezone'] * 3600;
	}
	$datetime = ($Ldt[$format]) ? @date($Ldt[$format], $timestamp) : @date($format, $timestamp);
	$search = array(
		'Monday', 'Tuesday', 'Wednesday', 'Thursday',
		'Friday', 'Saturday', 'Sunday',
		'Mon', 'Tue', 'Wed', 'Thu',
		'Fri', 'Sat', 'Sun',
		'January', 'February', 'March',
		'April', 'May', 'June',
		'July', 'August', 'September',
		'October', 'November', 'December',
		'Jan', 'Feb', 'Mar',
		'Apr', 'May', 'Jun',
		'Jul', 'Aug', 'Sep',
		'Oct', 'Nov', 'Dec'
	);
	$replace = array(
		$L['Monday'], $L['Tuesday'], $L['Wednesday'], $L['Thursday'],
		$L['Friday'], $L['Saturday'], $L['Sunday'],
		$L['Monday_s'], $L['Tuesday_s'], $L['Wednesday_s'], $L['Thursday_s'],
		$L['Friday_s'], $L['Saturday_s'], $L['Sunday_s'],
		$L['January'], $L['February'], $L['March'],
		$L['April'], $L['May'], $L['June'],
		$L['July'], $L['August'], $L['September'],
		$L['October'], $L['November'], $L['December'],
		$L['January_s'], $L['February_s'], $L['March_s'],
		$L['April_s'], $L['May_s'], $L['June_s'],
		$L['July_s'], $L['August_s'], $L['September_s'],
		$L['October_s'], $L['November_s'], $L['December_s']
	);
	return ($lang == 'en') ? $datetime : str_replace($search, $replace, $datetime);
}

/**
 * Creates UNIX timestamp out of a date
 *
 * @param int $hour Hours
 * @param int $minute Minutes
 * @param int $second Seconds
 * @param int $month Month
 * @param int $date Day of the month
 * @param int $year Year
 * @return int
 */
function cot_mktime($hour = false, $minute = false, $second = false, $month = false, $date = false, $year = false)
{
	if ($hour === false)  $hour  = date ('G');
	if ($minute === false) $minute = date ('i');
	if ($second === false) $second = date ('s');
	if ($month === false)  $month  = date ('n');
	if ($date === false)  $date  = date ('j');
	if ($year === false)  $year  = date ('Y');

	return mktime ((int) $hour, (int) $minute, (int) $second, (int) $month, (int) $date, (int) $year);
}

if (!function_exists('strptime'))
{
	/**
	 * strptime() for Windows
	 * @author ex/yks toolkit
	 * @license MIT
	 * @param string $date
	 * @param string $format
	 * @return boolean
	 */
	function strptime($date, $format)
	{
		$masks = array(
			'%d' => '(?P<d>[0-9]{2})',
			'%m' => '(?P<m>[0-9]{2})',
			'%Y' => '(?P<Y>[0-9]{4})',
			'%H' => '(?P<H>[0-9]{2})',
			'%M' => '(?P<M>[0-9]{2})',
			'%S' => '(?P<S>[0-9]{2})'
		);

		$rexep = "#" . strtr(preg_quote($format), $masks) . "#";
		if (!preg_match($rexep, $date, $out))
			return false;

		$ret = array(
			"tm_sec" => (int) $out['S'],
			"tm_min" => (int) $out['M'],
			"tm_hour" => (int) $out['H'],
			"tm_mday" => (int) $out['d'],
			"tm_mon" => $out['m'] ? $out['m'] - 1 : 0,
			"tm_year" => $out['Y'] > 1900 ? $out['Y'] - 1900 : 0,
		);
		return $ret;
	}
}

/**
 * Converts date into UNIX timestamp.
 * Like strptime() but using formatting as specified for date().
 *
 * @param string $date
 *	Formatted date as a string.
 * @param string $format
 *	Format on which to base the conversion.
 *	Defaults to MySQL date format.
 *	Can also be set to 'auto', in which case
 *	it will rely on strtotime for parsing.
 * @return int UNIX timestamp or NULL for 0000-00-00
 */
function cot_date2stamp($date, $format = null)
{
	if ($date == '0000-00-00') return null;
	if (!$format)
	{
		preg_match('#(\d{4})-(\d{2})-(\d{2})#', $date, $m);
		return mktime(0, 0, 0, (int) $m[2], (int) $m[3], (int) $m[1]);
	}
	if ($format == 'auto')
	{
		return strtotime($date);
	}
	$format = cot_date2strftime($format);
	$m = strptime($date, $format);
	return mktime(
		(int)$m['tm_hour'], (int)$m['tm_min'], (int)$m['tm_sec'],
		(int)$m['tm_mon']+1, (int)$m['tm_mday'], (int)$m['tm_year']+1900
	);
}

/**
 * Converts UNIX timestamp into MySQL date
 *
 * @param int $stamp UNIX timestamp
 * @return string MySQL date
 */
function cot_stamp2date($stamp)
{
	return date('Y-m-d', $stamp);
}

/**
 * Convert a date format to a strftime format.
 * Timezone conversion is done for unix. Windows users must exchange %z and %Z.
 * Unsupported date formats : S, n, t, L, B, G, u, e, I, P, Z, c, r
 * Unsupported strftime formats : %U, %W, %C, %g, %r, %R, %T, %X, %c, %D, %F, %x
 *
 * @author Cotonti Team
 * @see http://php.net/manual/en/function.strftime.php
 * @param string $format A format for date().
 * @return string Format usable for strftime().
 */
function cot_date2strftime($format) {

    $chars = array(
        'd' => '%d', 'D' => '%a', 'j' => '%e', 'l' => '%A',
		'N' => '%u', 'w' => '%w', 'z' => '%j', 'W' => '%V',
		'F' => '%B', 'm' => '%m', 'M' => '%b', 'o' => '%G',
		'Y' => '%Y', 'y' => '%y', 'a' => '%P', 'A' => '%p',
		'g' => '%l', 'h' => '%I', 'H' => '%H', 'i' => '%M',
		's' => '%S', 'O' => '%z', 'T' => '%Z', 'U' => '%s'
    );
    return strtr((string)$format, $chars);
}

/**
 * Returns a list of timezones sorted by GMT offset.
 *
 * @param bool $withgmt Return 'GMT' as the first option, otherwise it won't be included
 * @param bool $dst Include DST in timezone offsets, if DST is in effect there right now
 * @return array Multidimensional array. Each timezone has the following keys:
 *  'identifier' - PHP timezone name, e.g. "America/El_Salvador"
 *  'offset' - GMT offset in seconds, e.g. -21600
 *  'title' - Localized timezone name, e.g. "America/El Salvador"
 *  'description' - Hourly GMT offset and localized name, e.g. "GMT-06:00 America/El Salvador"
 */
function cot_timezone_list($withgmt = false, $dst = false)
{
	global $Ltz;
	if (!$Ltz) include cot_langfile('countries', 'core');
	static $timezones = array();
	if (!$timezones)
	{
		$timezonelist = array();
		$regions = array('Africa', 'America', 'Antarctica', 'Asia', 'Atlantic', 'Europe', 'Indian', 'Pacific');
		$identifiers = DateTimeZone::listIdentifiers();
		foreach ($identifiers as $timezone)
		{
			list ($region, $city) = explode('/', $timezone, 2);
			if (!in_array($region, $regions)) continue;
			$offset = cot_timezone_offset($timezone, false, $dst);
			$gmtoffset = cot_build_timezone($offset);
			$title = $Ltz[$timezone] ? $Ltz[$timezone] : $region.'/'.str_replace('_', ' ', $city);
			$timezonelist[] = array(
				'identifier' => $timezone,
				'offset' => $offset,
				'title' => $title,
				'description' => "$gmtoffset $title"
			);
		}
		foreach ($timezonelist as $k => $tz) {
			$offsets[$k] = $tz['offset'];
			$names[$k] = $tz['name'];
		}
		array_multisort($offsets, SORT_ASC, $names, SORT_ASC, $timezonelist);
		$timezones = $timezonelist;
	}
	return $withgmt ? array_merge(array(array('name' => 'GMT', 'offset' => 0, 'description' => 'GMT')), $timezones) : $timezones;
}

/**
 * Returns the offset from GMT in seconds or hours, with or without DST.
 * Example: Europe/Amsterdam returns 3600 (GMT+1) in the winter, but 7200 (GMT+2) in the summer (DST).
 * Whether or not to apply DST is determined automatically by PHP, but can be disabled.
 * A list of supported timezone identifiers is here: http://php.net/manual/en/timezones.php
 *
 * @param string $tz Timezone identifier (e.g. Europe/Amsterdam)
 * @param bool $hours Return hours instead of seconds
 * @param bool $dst Include DST in offset if DST is in effect right now
 * @return mixed Timezone difference in seconds (int) or hours (float)
 */
function cot_timezone_offset($tz, $hours = false, $dst = true)
{
	if (!$tz || in_array($tz, array('UTC', 'GMT', 'Universal', 'UCT', 'Zulu'))) return 0;
	try
	{
		// $origin_dtz = new DateTimeZone('UTC');
		$remote_dtz = new DateTimeZone($tz);
		if (!$dst)
		{
			// Standard offset is in Winter
			$standard_offset = $remote_dtz->getOffset(new DateTime("next year January 1"));
			// $trans = cot_timezone_transitions($tz);
			// $dstoffset = ($trans['current']['isdst']) ? $trans['current']['offset'] - $trans['previous']['offset'] : 0;
		}
		// $origin_dt = new DateTime('now', $origin_dtz);
		$remote_dt = new DateTime('now', $remote_dtz);
	}
	catch(Exception $e)
	{
		return null;
	}
    // $offset = $remote_dtz->getOffset($remote_dt) - $origin_dtz->getOffset($origin_dt) - $dstoffset;
   	$offset = $dst ? $remote_dtz->getOffset($remote_dt) : $standard_offset;
	return $hours ? floatval($offset / 3600) : $offset;
}

/**
 * Returns a list of possible timezones based on country and/or GMT offset.
 *
 * @param string $countrycode 2 char lowercase country code
 * @param int $gmtoffset Offset from GMT in seconds
 * @return array Numeric array of timezone identifiers
 */
function cot_timezone_search($countrycode = '', $gmtoffset = null)
{
	global $cot_timezones;
	$res = array();
	$both = ($countrycode && is_int($gmtoffset));
	foreach ($cot_timezones as $tz => $info)
	{
		$countrymatch = ($info[0] == $countrycode);
		$offsetmatch = (is_int($gmtoffset) && ($info[1] == $gmtoffset || $info[2] == $gmtoffset));
		if (($countrymatch && $offsetmatch) || (!$both && ($countrymatch || $offsetmatch)))
		{
			$res[] = $tz;
		}
	}
	sort($res);
	return $res;
}

/**
 * Returns previous, current and next transition in a certain timezone.
 * Useful for detecting if DST is currently in effect.
 *
 * @param string $tz Timezone identifier, must be one of PHP supported timezones
 * @return array Multidimensional array with keys 'previous', 'current' and 'next',
 *  each containing an element of the result of DateTimeZone::getTransitions
 * @see http://www.php.net/manual/en/datetimezone.gettransitions.php
 */
function cot_timezone_transitions($tz)
{
	global $sys;
	try
	{
		$dtz = new DateTimeZone($tz);
	}
	catch(Exception $e)
	{
		return null;
	}
	$transitions = array_reverse((array)$dtz->getTransitions());
	foreach ($transitions as $key => $transition)
	{
		if ($transition['ts'] < $sys['now'])
		{
			return array(
				'previous' => $transitions[$key+1],
				'current' => $transition,
				'next' => $transitions[$key-1],
			);
		}
	}
}

/*
 * ================================== Pagination ==============================
*/

/**
 * Page navigation (pagination) builder. Uses URL transformation and resource strings,
 * returns an associative array, containing:
 * ['prev'] - first and previous page buttons
 * ['main'] - buttons with page numbers, including current
 * ['next'] - next and last page buttons
 * ['last'] - last page with number
 *
 * @param string $module Site area or script name
 * @param mixed $params URL parameters as array or parameter string
 * @param int $current Current page offset
 * @param int $entries Total rows
 * @param int $perpage Rows per page
 * @param string $characters It is symbol for parametre which transfer pagination
 * @param string $hash Hash part of the url (including #)
 * @param bool $ajax Add AJAX support
 * @param string $target_div Target div ID if $ajax is true
 * @param string $ajax_module Site area name for ajax if different from $module
 * @param string $ajax_params URL parameters for ajax if $ajax_module is not empty
 * @return array
 */
function cot_pagenav($module, $params, $current, $entries, $perpage, $characters = 'd', $hash = '',
	$ajax = false, $target_div = '', $ajax_module = '', $ajax_params = array())
{
	if (function_exists('cot_pagenav_custom'))
	{
		// For custom pagination functions in plugins
		return cot_pagenav_custom($module, $params, $current, $entries, $perpage, $characters, $hash,
			$ajax, $target_div, $ajax_module, $ajax_params);
	}

	global $cfg;

	if (!$perpage)
	{
		$perpage = $cfg['maxrowsperpage'] ? $cfg['maxrowsperpage'] : 1;
	}

	$onpage = $entries - $current;
	if ($onpage > $perpage) $onpage = $perpage;

	if ($entries <= $perpage)
	{
		return array(
			'onpage' => $onpage,
			'entries' => $entries
		);
	}

	$each_side = 3; // Links each side

	is_array($params) ? $args = $params : parse_str($params, $args);
	if ($ajax)
	{
		$ajax_rel = !empty($ajax_module);
		$ajax_rel && is_string($ajax_params) ? parse_str($ajax_params, $ajax_args) : $ajax_args = $ajax_params;
		$event = ' class="ajax"';
		if (empty($target_div))
		{
			$base_rel = $ajax_rel ? ' rel="get;' : '';
		}
		else
		{
			$base_rel = $ajax_rel ? ' rel="get-'.$target_div.';' : ' rel="get-'.$target_div.'"';
		}
	}
	else
	{
		$ajax_rel = false;
		$event = '';
	}
	$rel = '';

	$totalpages = ceil($entries / $perpage);
	$currentpage = floor($current / $perpage) + 1;
	$cur_left = $currentpage - $each_side;
	if ($cur_left < 1) $cur_left = 1;
	$cur_right = $currentpage + $each_side;
	if ($cur_right > $totalpages) $cur_right = $totalpages;

	// Main block

	$before = '';
	$pages = '';
	$after = '';
	$i = 1;
	$n = 0;
	while ($i < $cur_left)
	{
		if ($cfg['easypagenav'])
		{
			$args[$characters] = $i == 1 ? null : $i;
		}
		else
		{
			$args[$characters] = ($i - 1) * $perpage;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$before .= cot_rc('link_pagenav_main', array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $i
		));
		if ($i < $cur_left - 2)
		{
			$before .= $R['link_pagenav_gap'];
		}
		elseif ($i == $cur_left - 2)
		{
			$args[$characters] = $i * $perpage;
			if ($ajax_rel)
			{
				$ajax_args[$characters] = $args[$characters];
				$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
			}
			else
			{
				$rel = $base_rel;
			}
			$before .= cot_rc('link_pagenav_main', array(
				'url' => cot_url($module, $args, $hash),
				'event' => $event,
				'rel' => $rel,
				'num' => $i + 1
			));
		}
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	for ($j = $cur_left; $j <= $cur_right; $j++)
	{
		if ($cfg['easypagenav'])
		{
			$args[$characters] = $j == 1 ? null : $j;
		}
		else
		{
			$args[$characters] = ($j - 1) * $perpage;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$rc = $j == $currentpage ? 'current' : 'main';
		$pages .= cot_rc('link_pagenav_'.$rc, array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $j
		));
	}
	while ($i <= $cur_right)
	{
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	while ($i < $totalpages)
	{
		if ($i > $cur_right + 2)
		{
			$after .= $R['link_pagenav_gap'];
		}
		elseif ($i == $cur_right + 2)
		{
			if ($cfg['easypagenav'])
			{
				$args[$characters] = $i == 2 ? null : $i - 1;
			}
			else
			{
				$args[$characters] = ($i - 2) * $perpage;
			}
			if ($ajax_rel)
			{
				$ajax_args[$characters] = $args[$characters];
				$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
			}
			else
			{
				$rel = $base_rel;
			}
			$after .= cot_rc('link_pagenav_main', array(
				'url' => cot_url($module, $args, $hash),
				'event' => $event,
				'rel' => $rel,
				'num' => $i - 1
			));
		}
		if ($cfg['easypagenav'])
		{
			$args[$characters] = $i == 1 ? null : $i;
		}
		else
		{
			$args[$characters] = ($i - 1) * $perpage;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$after .= cot_rc('link_pagenav_main', array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $i
		));
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	$pages = $before.$pages.$after;

	// Previous/next

	if ($current > 0)
	{
		$prev_n = $current - $perpage;
		if ($prev_n < 0)
		{
			$prev_n = 0;
		}
		if ($cfg['easypagenav'])
		{
			$num = floor($prev_n / $perpage) + 1;
			$args[$characters] = $num == 1 ? null : $num;
		}
		else
		{
			$args[$characters] = $prev_n;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$prevlink = cot_url($module, $args, $hash);
		$prev = cot_rc('link_pagenav_prev', array(
			'url' => $prevlink,
			'event' => $event,
			'rel' => $rel,
			'num' => $prev_n + 1
		));
		$args[$characters] = 0;
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$firstlink = cot_url($module, $args, $hash);
		$first = cot_rc('link_pagenav_first', array(
			'url' => $firstlink,
			'event' => $event,
			'rel' => $rel,
			'num' => 1
		));
	}

	if (($current + $perpage) < $entries)
	{
		$next_n = $current + $perpage;
		if ($cfg['easypagenav'])
		{
			$num = floor($next_n / $perpage) + 1;
			$args[$characters] = $num == 1 ? null : $num;
		}
		else
		{
			$args[$characters] = $next_n;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$nextlink = cot_url($module, $args, $hash);
		$next = cot_rc('link_pagenav_next', array(
			'url' => $nextlink,
			'event' => $event,
			'rel' => $rel,
			'num' => $next_n + 1
		));
		$last_n = ($totalpages - 1) * $perpage;
		if ($cfg['easypagenav'])
		{
			$num = floor($last_n / $perpage) + 1;
			$args[$characters] = $num == 1 ? null : $num;
		}
		else
		{
			$args[$characters] = $last_n;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$lastlink = cot_url($module, $args, $hash);
		$last = cot_rc('link_pagenav_last', array(
			'url' => $lastlink,
			'event' => $event,
			'rel' => $rel,
			'num' => $last_n + 1
		));
		$lastn  = (($last +  $perpage)<$totalpages) ?
			cot_rc('link_pagenav_main', array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => floor($last_n / $perpage) + 1
			)): FALSE;
	}

	return array(
		'prev' => $first.$prev,
		'main' => $pages,
		'next' => $next.$last,
		'last' => $lastn,
		'current' => $currentpage,
		'firstlink' => $firstlink,
		'prevlink' => $prevlink,
		'nextlink' => $nextlink,
		'lastlink' => $lastlink,
		'total' => $totalpages,
		'onpage' => $onpage,
		'entries' => $entries
	);
}

/*
 * ============================== Text parsing API ============================
 */

/**
 * Returns the list of available rich text editors
 *
 * @return array
 */
function cot_get_editors()
{
	global $cot_plugins;
	$list = array('none');
	if (is_array($cot_plugins['editor']))
	{
		foreach ($cot_plugins['editor'] as $k)
		{
			if (cot_auth('plug', $k['pl_code'], 'W'))
			{
				$list[] = $k['pl_code'];
			}
		}
	}
	return $list;
}

/**
 * Returns the list of available markup parsers
 *
 * @return array
 */
function cot_get_parsers()
{
	global $cot_plugins;
	$list = array('none');
	if (is_array($cot_plugins['parser']))
	{
		foreach ($cot_plugins['parser'] as $k)
		{
			if (cot_auth('plug', $k['pl_code'], 'W'))
			{
				$list[] = $k['pl_code'];
			}
		}
	}
	return $list;
}

/**
 * Parses text body
 *
 * @param string $text Source text
 * @param bool $enable_markup Enable markup or plain text output
 * @param string $parser Non-default parser to use
 * @return string
 */
function cot_parse($text, $enable_markup = true, $parser = '')
{
	global $cfg, $cot_plugins;

	$plain = true;
	if ($enable_markup)
	{
		if (empty($parser))
		{
			$parser = $cfg['parser'];
		}
		if (!empty($parser) && $parser != 'none')
		{
			$func = "cot_parse_$parser";
			if (function_exists($func))
			{
				$text = $func($text);
				$plain = false;
			}
			else
			{
				// Load the appropriate parser
				if (is_array($cot_plugins['parser']))
				{
					foreach ($cot_plugins['parser'] as $k)
					{
						if ($k['pl_code'] == $parser && cot_auth('plug', $k['pl_code'], 'R'))
						{
							include $cfg['plugins_dir'] . '/' . $k['pl_file'];
							$text = $func($text);
							$plain = false;
							break;
						}
					}
				}
			}
		}
	}

	if ($plain)
	{
		$text = nl2br(htmlspecialchars($text));
	}

	/* == Hook == */
	foreach (cot_getextplugins('parser.last') as $pl)
	{
		include $pl;
	}
	/* ===== */

	return $text;
}

/**
 * Automatically detect and parse URLs in text into HTML
 *
 * @param string $text Text body
 * @return string
 */
function cot_parse_autourls($text)
{
	$text = preg_replace('`(^|\s)(http|https|ftp)://([^\s"\'\[]+)`', '$1<a href="$2://$3">$2://$3</a>', $text);
	return $text;
}

/**
 * Truncates text.
 *
 * Cuts a string to the length of $length
 *
 * @param string  $text String to truncate.
 * @param integer $length Length of returned string, including ellipsis.
 * @param boolean $considerhtml If true, HTML tags would be handled correctly *
 * @param boolean $exact If false, $text will not be cut mid-word
 * @param string  $cuttext Adds text if truncated
 * @return string trimmed string.
 */
function cot_string_truncate($text, $length = 100, $considerhtml = true, $exact = false, $cuttext = '')
{
	if ($considerhtml)
	{
		// if the plain text is shorter than the maximum length, return the whole text
		if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length)
		{
			return $text;
		}
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

		$total_length = 0;
		$open_tags = array();
		$truncate = '';

		foreach ($lines as $line_matchings)
		{
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1]))
			{
				// if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1]))
				{
					// do nothing
					// if tag is a closing tag (f.e. </b>)
				}
				elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings))
				{
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false)
					{
						unset($open_tags[$pos]);
					}
					// if tag is an opening tag (f.e. <b>)
				}
				elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings))
				{
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, mb_strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}

			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length)
			{
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE))
				{
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity)
					{
						if ($entity[1]+1-$entities_length <= $left)
						{
							$left--;
							$entities_length += mb_strlen($entity[0]);
						}
						else
						{
							// no more characters left
							break;
						}
					}
				}
				$truncate .= mb_substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			}
			else
			{
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}

			// if the maximum length is reached, get off the loop
			if ($total_length >= $length)
			{
				break;
			}
		}
	}
	else
	{
		if (mb_strlen($text) <= $length)
		{
			return $text;
		}
		else
		{
			$truncate = mb_substr($text, 0, $length);
		}
	}

	if (!$exact)
	{
		// ...search the last occurance of a space...
		if (mb_strrpos($truncate, ' ') > 0)
		{
			$pos1 = mb_strrpos($truncate, ' ');
			$pos2 = mb_strrpos($truncate, '>');
			$spos = ($pos2 < $pos1) ? $pos1 : ($pos2+1);
			if (isset($spos))
			{
				// ...and cut the text in this position
				$truncate = mb_substr($truncate, 0, $spos);
			}
		}
	}
	$truncate .= $cuttext;
	if ($considerhtml)
	{
		// close all unclosed html-tags
		foreach ($open_tags as $tag)
		{
			$truncate .= '</'.$tag.'>';
		}
	}
	return $truncate;
}

/**
 * Wraps text
 *
 * @param string $str Source text
 * @param int $wrap Wrapping boundary
 * @return string
 */
function cot_wraptext($str, $wrap = 80)
{
	if (!empty($str))
	{
		$str = preg_replace('/([^\n\r ?&\.\/<>\"\\-]{'.$wrap.'})/', " \\1\n", $str);
	}
	return $str;
}

/*
 * ============================== Resource Strings ============================
 */

/**
 * Gets the list of $L and $R keys overridden in a theme PHP file
 * @param  string $theme_file Path to theme PHP file
 * @return array
 */
function cot_themerc_list($theme_file)
{
	$L = array();
	$R = array();
	include $theme_file;
	return array(
		array_keys($L),
		array_keys($R)
	);
}

/**
 * Resource string formatter function. Takes a string with predefined variable substitution, e.g.
 * 'My {$pet} likes {$food}. And {$pet} is hungry!' and an assotiative array of substitution values, e.g.
 * array('pet' => 'rabbit', 'food' => 'carrots') and assembles a formatted result. If {$var} cannot be found
 * in $args, it will be taken from global scope. You can also use parameter strings instead of arrays, e.g.
 * 'pet=rabbit&food=carrots'. Or omit the second parameter in case all substitutions are globals.
 *
 * @global array $R Resource strings
 * @global array $L Language strings, support resource sequences too
 * @param string $name Name of the $R item or a resource string itself
 * @param array $params Associative array of arguments or a parameter string
 * @return string Assembled resource string
 */
function cot_rc($name, $params = array())
{
	global $R, $L, $theme_reload;
	if (isset($R[$name]) && is_array($theme_reload))
	{
		$R[$name] = (!empty($theme_reload['R'][$name])) ? $theme_reload['R'][$name] : $R[$name];
	}
	elseif (isset($L[$name]) && is_array($theme_reload))
	{
		$L[$name] = (!empty($theme_reload['L'][$name])) ? $theme_reload['L'][$name] : $L[$name];
	}

	$res = isset($R[$name]) ? $R[$name]
		: (isset($L[$name]) ? $L[$name] : $name);
	is_array($params) ? $args = $params : parse_str($params, $args);
	if (preg_match_all('#\{\$(\w+)\}#', $res, $matches, PREG_SET_ORDER))
	{
		foreach($matches as $m)
		{
			$var = $m[1];
			$res = str_replace($m[0], (isset($args[$var]) ? $args[$var] : $GLOBALS[$var]), $res);
		}
	}
	return $res;
}

/**
 * Converts custom attributes to a string if necessary
 *
 * @param mixed $attrs A string or associative array
 * @return string
 */
function cot_rc_attr_string($attrs)
{
	$attr_str = '';
	if (is_array($attrs))
	{
		foreach ($attrs as $key => $val)
		{
			$attr_str .= ' ' . $key . '="' . htmlspecialchars($val) . '"';
		}
	}
	elseif ($attrs)
	{
		$attr_str = ' ' . $attrs;
	}
	return $attr_str;
}

/**
 * Consolidates local JS and CSS resources used during script execution,
 * prepares cache images and links to them
 *
 * @global array $cot_rc_html Blocks of HTML tags to be included in the header and footer of the document
 * @global array $cot_rc_reg Header/Footer resource registry
 * @global array $cfg Configuration
 * @global array $env Environment strings
 * @global array $usr User object
 * @global Cache $cache
 */
function cot_rc_consolidate()
{
	global $cache, $cfg, $cot_rc_html, $cot_rc_reg, $L, $R, $usr, $theme;

	$is_admin_section = defined('COT_ADMIN');
	$cot_rc_reg = array();

	// Load standard resources
	cot_rc_add_standard();

	// Invoke rc handlers
	foreach (cot_getextplugins('rc') as $pl)
	{
		include $pl;
	}
	if (!$is_admin_section)
	{
		if (file_exists("{$cfg['themes_dir']}/{$usr['theme']}/{$usr['theme']}.rc.php"))
		{
			include "{$cfg['themes_dir']}/{$usr['theme']}/{$usr['theme']}.rc.php";
		}
	}

	if (!is_array($cot_rc_reg))
	{
		return false;
	}

	// CSS should go first
	ksort($cot_rc_reg);

	// Build the header outputs
	$cot_rc_html[$theme] = array();

	// Consolidate resources
	if ($cache && $cfg['headrc_consolidate'] && !$is_admin_section)
	{
		clearstatcache();
		foreach ($cot_rc_reg as $type => $scope_data)
		{
			if ($type == 'css')
			{
				$separator = "\n";
			}
			elseif ($type == 'js')
			{
				$separator = "\n;";
			}
			// Consolidation
			foreach ($scope_data as $scope => $ordered_files)
			{
				$target_path = $cfg['cache_dir'] . "/static/$scope.$theme.$type";

				$files = array();
				foreach ($ordered_files as $order => $o_files)
				{
					$files = array_merge($files, $o_files);
				}
				$files = array_unique($files);

				$code = '';
				$modified = false;

				if (!file_exists($target_path))
				{
					// Just compile a new cache file
					$file_list = $files;
					$modified = true;
				}
				else
				{
					// Load the list of files already cached
					$file_list = unserialize(file_get_contents("$target_path.idx"));

					// Check presense or modification time for each file
					foreach ($files as $path)
					{
						if (!in_array($path, $file_list) || filemtime($path) >= filemtime($target_path))
						{
							$modified = true;
							break;
						}
					}
				}

				if ($modified)
				{
					// Reconsolidate cache
					$current_path = str_replace('\\', '/', realpath('.'));
					foreach ($files as $path)
					{
						// Get file contents and remove BOM
						$file_code = str_replace(pack('CCC', 0xef, 0xbb, 0xbf), '', file_get_contents($path));

						if ($type == 'css')
						{
							if (strpos($path, '._.') !== false)
							{
								// Restore original file path
								$path = str_replace('._.', '/', basename($path));
							}
							if ($path[0] === '/')
							{
								$path = mb_substr($path, 1);
							}
							$file_path = str_replace('\\', '/', dirname(realpath($path)));
							$relative_path = str_replace($current_path, '', $file_path);
							if ($relative_path[0] === '/')
							{
								$relative_path = mb_substr($relative_path, 1);
							}
							// Apply CSS imports
							if (preg_match_all('#@import\s+url\((\'|")?([^)]+)\1?\);#i', $file_code, $mt, PREG_SET_ORDER))
							{
								foreach ($mt as $m)
								{
									if (preg_match('#^https?://#i', $m[2]))
									{
										$filename = $m[2];
									}
									else
									{
										$filename = empty($relative_path) ? $m[2] : $relative_path . '/' . $m[2];
									}
									$file_code = str_replace($m[0], file_get_contents($filename), $file_code);
								}
							}
							// Fix URLs
							if (preg_match_all('#\burl\((\'|")?([^\)"\']+)\1?\)#i', $file_code, $mt, PREG_SET_ORDER))
							{
								foreach ($mt as $m)
								{
									$filename = empty($relative_path) ? $m[2] : $relative_path . '/' . $m[2];
									$filename = str_replace($current_path, '', str_replace('\\', '/',realpath($filename)));
									if (!$filename)
									{
										continue;
									}
									if ($filename[0] === '/')
									{
										$filename = mb_substr($filename, 1);
									}
									$file_code = str_replace($m[0], 'url("' . $filename . '")', $file_code);
								}
							}
						}
						$code .= $file_code . $separator;
					}

					file_put_contents($target_path, $code);
					if ($cfg['gzip'])
					{
						file_put_contents("$target_path.gz", gzencode($code));
					}
					file_put_contents("$target_path.idx", serialize($files));
				}

				$rc_url = "rc.php?rc=$scope.$theme.$type";
				$cot_rc_html[$theme][$scope] .= cot_rc("code_rc_{$type}_file", array('url' => $rc_url));
			}
		}
		// Save the output
		$cache && $cache->db->store('cot_rc_html', $cot_rc_html);
	}
	else
	{
		$log = array(); // log paths to avoid duplicates
		foreach ($cot_rc_reg as $type => $scope_data)
		{
			if (is_array($cot_rc_reg[$type]['files']))
			{
				foreach ($cot_rc_reg[$type]['files'] as $scope => $scope_data)
				{
					foreach ($scope_data as $order => $files)
					{
						foreach ($files as $file)
						{
							if (!in_array($file, $log))
							{
								$cot_rc_html[$theme][$scope] .= cot_rc("code_rc_{$type}_file", array('url' => $file)) . "\n";
								$log[] = $file;
							}
						}
					}
				}
			}
			if (is_array($cot_rc_reg[$type]['embed']))
			{
				foreach ($cot_rc_reg[$type]['embed'] as $scope => $scope_data)
				{
					foreach ($scope_data as $order => $code)
					{
						$cot_rc_html[$theme][$scope] .= cot_rc("code_rc_{$type}_embed", array('code' => $code)) . "\n";
					}
				}
			}
		}
	}
}

/**
 * Puts a portion of embedded code into the header/footer CSS/JS resource registry.
 *
 * It is strongly recommended to use files for CSS/JS whenever possible
 * and call cot_rc_add_file() function for them instead of embedding code
 * into the page and using this function. This function should be used for
 * dynamically generated code, which cannot be stored in static files.
 *
 * @global array $cot_rc_reg_css Header CSS resource registry
 * @param string $identifier Alphanumeric identifier for the piece, used to control updates, etc.
 * @param string $code Embedded stylesheet or script code
 * @param string $scope Resource scope. See description of this parameter in cot_rc_add_file() docs.
 * @param string $type Resource type: 'js' or 'css'
 * @param int $order Order priority number
 * @return bool This function always returns TRUE
 * @see cot_rc_add_file()
 * @global Cache $cache
 */
function cot_rc_add_embed($identifier, $code, $scope = 'global', $type = 'js', $order = 50)
{
	global $cache, $cfg, $cot_rc_reg, $cot_rc_skip_minification;

	if ($cfg['headrc_consolidate'] && $cache && !defined('COT_ADMIN'))
	{
		// Save as file
		$path = $cfg['cache_dir'] . '/static/' . $identifier . '.' . $type;
		if (!file_exists($path) || md5($code) != md5_file($path))
		{
			if ($cfg['headrc_minify'] && !$cot_rc_skip_minification)
			{
				$code = cot_rc_minify($code, $type);
			}
			file_put_contents($path, $code);
		}
		$cot_rc_reg[$type][$scope][$order][] = $path;
	}
	else
	{
		$separator = $type == 'js' ? "\n;" : "\n";
		$cot_rc_reg[$type]['embed'][$scope][$order] .= $code . $separator;
	}
	return true;
}

/**
 * Puts a JS/CSS file into the footer resource registry to be consolidated with other
 * such resources and stored in cache.
 *
 * It is recommened to use files instead of embedded code and use this function
 * instead of cot_rc_add_js_embed(). Use this way for any sort of static JavaScript or
 * CSS linking.
 *
 * Do not put any private data in any of resource files - it is not secure. If you really need it,
 * then use direct output instead.
 *
 * @global array $cot_rc_reg JavaScript/CSS footer/header resource registry
 * @param string $path Path to a *.js script or *.css stylesheet
 * @param mixed $scope Resource scope. Scope is a selector of domain where resource is used. Valid scopes are:
 *	'global' - global for entire site, will be included everywhere, this is the most static and persistent scope;
 *	'guest' - for unregistered visitors only;
 *	'user' - for registered members only;
 *	'group_123' - for members of a specific group (maingrp), in this example of group with id=123.
 * It is recommended to use 'global' scope whenever possible because it delivers best caching opportunities.
 * @param int $order Order priority number
 * @return bool Returns TRUE normally, FALSE is file was not found
 * @global Cache $cache
 */
function cot_rc_add_file($path, $scope = 'global', $order = 50)
{
	global $cache, $cfg, $cot_rc_reg, $cot_rc_skip_minification;
	if (!file_exists($path))
	{
		return false;
	}

	$type = preg_match('#\.(min\.)?(js|css)$#', mb_strtolower($path), $m) ? $m[2] : 'js';

	if ($cache && $cfg['headrc_consolidate'] && !defined('COT_ADMIN') && $cfg['headrc_minify'] && !$cot_rc_skip_minification && $m[1] != 'min.')
	{
		$bname = ($type == 'css') ? str_replace('/', '._.', $path) : basename($path) . '.min';
		$code = cot_rc_minify(file_get_contents($path), $type);
		$path = $cfg['cache_dir'] . '/static/' . $bname;
		file_put_contents($path, $code);
	}

	if ($cfg['headrc_consolidate'] && $cache && !defined('COT_ADMIN'))
	{
		$cot_rc_reg[$type][$scope][$order][] = $path;
	}
	else
	{
		$cot_rc_reg[$type]['files'][$scope][$order][] = $path;
	}
	return true;
}

/**
 * Registers standard resources
 */
function cot_rc_add_standard()
{
	global $cfg;

	if ($cfg['jquery'] && !$cfg['jquery_cdn'])
	{
		cot_rc_add_file('js/jquery.min.js');
	}

	if ($cfg['jquery'] && $cfg['turnajax'])
	{
		cot_rc_add_file('js/jquery.history.min.js');
	}

	if ($cfg['jquery'])
	{
		cot_rc_add_file('js/jqModal.min.js');
	}

	cot_rc_add_file('js/base.js');

	if ($cfg['jquery'] && $cfg['turnajax'])
	{
		cot_rc_add_file('js/ajax_on.js');
	}
}

/**
 * Sends registered header resources to head output
 *
 * @global array $out Output snippets
 * @global array $cot_rc_html Header HTML
 */
function cot_rc_output()
{
	global $cot_rc_html, $out, $usr, $theme;
	if (is_array($cot_rc_html) && isset($cot_rc_html[$theme]))
	{
		foreach ($cot_rc_html[$theme] as $scope => $html)
		{
			switch ($scope)
			{
				case 'global':
					$pass = true;
					break;
				case 'guest':
					$pass = $usr['id'] == 0;
					break;
				case 'user':
					$pass = $usr['id'] > 0;
					break;
				default:
					$parts = explode('_', $scope);
					$pass = count($parts) == 2 && $parts[0] == 'group' && $parts[1] == $usr['maingrp'];
			}
			if ($pass)
			{
				$out['head_head'] = $html.$out['head_head'];
			}
		}
	}
}

/**
 * A shortcut for plain output of an embedded stylesheet/javascript in the header of the page
 *
 * @global array $out Output snippets
 * @param string $code Stylesheet or javascript code
 * @param bool $prepend Prepend this file before other head outputs
 * @param string $type Resource type: 'js' or 'css'
 */
function cot_rc_embed($code, $prepend = false, $type = 'js')
{
	global $out;
	$embed = cot_rc("code_rc_{$type}_embed", array('code' => $code));
	$prepend ? $out['head_head'] = $embed . $out['head_head'] : $out['head_head'] .= $embed;
}

/**
 * A shortcut for plain output of an embedded stylesheet/javascript in the footer of the page
 *
 * @global array $out Output snippets
 * @param string $code Stylesheet or javascript code
 * @param string $type Resource type: 'js' or 'css'
 */
function cot_rc_embed_footer($code, $type = 'js')
{
	global $out;
	$out['footer_rc'] .= cot_rc("code_rc_{$type}_embed", array('code' => $code));
}

/**
 * Quick link resource pattern
 *
 * @param string $url Link href
 * @param string $text Tag contents
 * @param mixed $attrs Additional attributes as a string or an associative array
 * @return string HTML link
 */
function cot_rc_link($url, $text, $attrs = '')
{
	$link_attrs = cot_rc_attr_string($attrs);
	return '<a href="' . $url . '"' . $link_attrs . '>' . $text . '</a>';
}

/**
 * A shortcut for plain output of a link to a CSS/JS file in the header of the page
 *
 * @global array $out Output snippets
 * @param string $path Stylesheet *.css or script *.js path/url
 * @param bool $prepend Prepend this file before other header outputs
 */
function cot_rc_link_file($path, $prepend = false)
{
	global $out;
	$type = preg_match('#\.(js|css)$#i', $path, $m) ? strtolower($m[1]) : 'js';
	$embed = cot_rc("code_rc_{$type}_file", array('url' => $path));
	$prepend ? $out['head_head'] = $embed . $out['head_head'] : $out['head_head'] .= $embed;
}

/**
 * A shortcut to append a JavaScript or CSS file to {FOOTER_JS} tag
 *
 * @global array $out Output snippets
 * @param string $path JavaScript or CSS file path
 */
function cot_rc_link_footer($path)
{
	global $out;
	$type = preg_match('#\.(js|css)$#i', $path, $m) ? strtolower($m[1]) : 'js';
	$out['footer_rc'] .= cot_rc("code_rc_{$type}_file", array('url' => $path));
}

/**
 * JS/CSS minification function
 *
 * @param string $code Code to minify
 * @param string $type Type: 'js' or 'css'
 * @return string Minified code
 */
function cot_rc_minify($code, $type = 'js')
{
	if ($type == 'js')
	{
		require_once './lib/jsmin.php';
		$code = JSMin::minify($code);
	}
	elseif ($type == 'css')
	{
		require_once './lib/cssmin.php';
		$code = minify_css($code);
	}
	return $code;
}

/*
 * ========================== Security functions =================================
*/

/**
 * Generates a captcha
 *
 * @global array $cfg
 * @global array $cot_captcha
 * @return string
 */
function cot_captcha_generate()
{
	global $cfg, $cot_captcha;
	if (!$cfg['captcharandom'])
	{
		$captcha = $cfg['captchamain'] . '_generate';
	}
	else
	{
		$captcha = $cot_captcha[rand(0, count($cot_captcha) - 1)];
		$tepmcap = '<input type="hidden" name="capman" value="' . $captcha . '" />';
		$captcha .="_generate";
	}

	if (function_exists($captcha))
	{
		return $captcha() . $tepmcap;
	}
	else
	{
		return '';
	}
}

/**
 * Returns the list of currently installed captchas
 * @global array $cot_captcha Captcha registry
 * @return array
 */
function cot_captcha_list()
{
	global $cot_captcha;
	return $cot_captcha;
}

/**
 * Valides a captcha value
 * @global array $cfg
 * @param string $value Captcha input for validation
 * @return boolean
 */
function cot_captcha_validate($value)
{
	global $cfg;

	// This function can only be called once per request
	static $called = false;
	if ($called)
	{
		return true;
	}
	else
	{
		$called = true;
	}

	if (!$cfg['captcharandom'])
	{
		$captcha = $cfg['captchamain'] . "_validate";
	}
	else
	{
		$captcha = cot_import('capman', 'P', 'TXT') . "_validate";
	}
	if (function_exists($captcha))
	{
		return $captcha($value);
	}
	return true;
}

/**
 * Checks GET anti-XSS parameter
 *
 * @param bool $redirect Redirect to message on failure
 * @return bool
 */
function cot_check_xg($redirect = true)
{
	global $sys;
	$x = cot_import('x', 'G', 'ALP');
	if ($x != $sys['xk'] && (empty($sys['xk_prev']) || $x != $sys['xk_prev']))
	{
		if ($redirect)
		{
			cot_die_message(950, TRUE);
		}
		return false;
	}
	return true;
}

/**
 * Checks POST anti-XSS parameter
 *
 * @return bool
 */
function cot_check_xp()
{
	return (defined('COT_NO_ANTIXSS') || defined('COT_AUTH')) ?
		($_SERVER['REQUEST_METHOD'] == 'POST') : isset($_POST['x']);
}

/**
 * Hashes a value with given salt and specified hash algo.
 *
 * @global array  $cot_hash_func
 * @param  string $data Data to be hash-protected
 * @param  string $salt Hashing salt, usually a random value
 * @param  string $algo Hashing algo name, must be registered in $cot_hash_funcs
 * @return string       Hashed value
 */
function cot_hash($data, $salt = '', $algo = 'sha256')
{
	global $cfg, $cot_hash_funcs;
	if (isset($cfg['hashsalt']) && !empty($cfg['hashsalt']))
	{
		// Extra salt for extremely secure sites
		$salt .= $cfg['hashsalt'];
	}
	$func = (in_array($algo, $cot_hash_funcs) && function_exists('cot_hash_' . $algo)) ? 'cot_hash_' . $algo : 'cot_hash_sha256';
	return $func($data, $salt);
}

/**
 * Returns the list of available hash algos for use with configs.
 *
 * @global array $cot_hash_func
 * @return array
 */
function cot_hash_funcs()
{
	global $cot_hash_funcs;
	return $cot_hash_funcs;
}

/**
 * Simple MD5 hash wrapper. Old passwords use this func.
 *
 * @param  string $data Data to be hashed
 * @param  string $salt Hashing salt, usually a random value
 * @return string       MD5 hash of the data
 */
function cot_hash_md5($data, $salt)
{
	return md5($data . $salt);
}

/**
 * SHA1 hash func for use with cot_hash().
 *
 * @param  string $data Data to be hashed
 * @param  string $salt Hashing salt, usually a random value
 * @return string       SHA1 hash of the data
 */
function cot_hash_sha1($data, $salt)
{
	return hash('sha1', $data . $salt);
}

/**
 * SHA256 hash func for use with cot_hash(). Default since Cotonti 0.9.11.
 *
 * @param  string $data Data to be hashed
 * @param  string $salt Hashing salt, usually a random value
 * @return string       SHA256 hash of the data
 */
function cot_hash_sha256($data, $salt)
{
	return hash('sha256', $data . $salt);
}

/**
 * Clears current user action in shield
 */
function cot_shield_clearaction()
{
	$_SESSION['shield_action'] = '';
}

/**
 * Anti-hammer protection
 *
 * @param int $hammer Hammer rate
 * @param string $action Action type
 * @param int $lastseen User last seen timestamp
 * @return int
 */
function cot_shield_hammer($hammer, $action, $lastseen)
{
	global $cfg, $sys;

	if ($action == 'Hammering')
	{
		cot_shield_protect();
		cot_shield_clearaction();
		cot_plugin_active('hits') && cot_stat_inc('totalantihammer');
	}

	if (($sys['now'] - $lastseen) < 4)
	{
		$hammer++;
		if ($hammer > $cfg['shieldzhammer'])
		{
			cot_shield_update(180, 'Hammering');
			cot_log('IP banned 3 mins, was hammering', 'sec');
			$hammer = 0;
		}
	}
	else
	{
		if ($hammer > 0)
		{
			$hammer--;
		}
	}
	return $hammer;
}

/**
 * Warn user of shield protection
 *
 */
function cot_shield_protect()
{
	global $sys, $shield_limit, $shield_action, $L;

	if ($shield_limit > $sys['now'])
	{
		cot_die_message(403, true, $L['shield_title'], cot_rc('shield_protect', array(
			'sec' => $shield_limit - $sys['now'],
			'action' => $shield_action
		)));
	}
}

/**
 * Updates shield state
 *
 * @param int $shield_add Hammer
 * @param string $shield_newaction New action type
 */
function cot_shield_update($shield_add, $shield_newaction)
{
	global $cfg, $sys;

	$shield_newlimit = $sys['now'] + floor($shield_add * $cfg['shieldtadjust'] /100);
	$_SESSION['online_shield'] = $shield_newlimit;
	$_SESSION['online_action'] = $shield_newaction;
}

/**
 * Unregisters globals if globals are On
 * @see http://www.php.net/manual/en/faq.misc.php#faq.misc.registerglobals
 */
function cot_unregister_globals()
{
	if (!ini_get('register_globals'))
	{
		return;
	}

	// Might want to change this perhaps to a nicer error
	if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS']))
	{
		die('GLOBALS overwrite attempt detected');
	}

	// Variables that shouldn't be unset
	$noUnset = array('GLOBALS', '_GET',
		'_POST', '_COOKIE',
		'_REQUEST', '_SERVER',
		'_ENV', '_FILES');

	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES,
		isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());

	foreach (array_keys($input) as $k)
	{
		if (!in_array($k, $noUnset) && isset($GLOBALS[$k]))
		{
			unset($GLOBALS[$k]);
		}
	}
}

/**
 * Returns XSS protection variable for GET URLs
 *
 * @return unknown
 */
function cot_xg()
{
	global $sys;
	return ('x='.$sys['xk']);
}

/**
 * Returns XSS protection field for POST forms
 *
 * @return string
 */
function cot_xp()
{
	global $sys;
	return '<div style="display:inline;margin:0;padding:0"><input type="hidden" name="x" value="'.$sys['xk'].'" /></div>';
}

/*
 * ============================ URL and URI ===================================
*/

/**
 * Generates an URL used to confirm an action performed by target URL
 *
 * @param string $target_url Target URL which performs the action
 * @param string $ext_name Module/plugin name to peform the action
 * @param string $msg_code Language string key which contains confirmation request text
 * @return string
 */
function cot_confirm_url($target_url, $ext_name = '', $msg_key = '')
{
	global $cfg;
	if ($cfg['confirmlinks'])
	{
		return cot_url('message', array(
			'msg' => 920,
			'm' => $ext_name,
			'lng' => $msg_key,
			'redirect' => base64_encode($target_url)
		));
	}
	else
	{
		return $target_url;
	}
}

/**
 * Displays redirect page
 *
 * @param string $url Target URI
 */
function cot_redirect($url)
{
	global $cfg, $env, $error_string, $sys;

	if (cot_error_found() && $_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// Save the POST data
		if (!empty($error_string))
		{
			// Message should not be lost
			cot_error($error_string);
		}
		cot_import_buffer_save();
	}

	if (!cot_url_check($url))
	{
		// No redirects to foreign domains
		$url = $url == '/' || $url == $sys['site_uri'] ? COT_ABSOLUTE_URL : COT_ABSOLUTE_URL . $url;
	}

	if (defined('COT_AJAX') && COT_AJAX)
	{
		// Save AJAX state, some browsers loose it after redirect (e.g. FireFox 3.6)
		$sep = strpos($url, '?') === false ? '?' : '&';
		$url .= $sep . '_ajax=1';
	}

	if (isset($env['status']))
	{
		$protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
		header($protocol . ' ' . $env['status']);
	}

	if ($cfg['redirmode'])
	{
		$output = $cfg['doctype'].<<<HTM
		<html>
		<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="refresh" content="0; url=$url" />
		<title>Redirecting...</title></head>
		<body>Redirecting to <a href="$url">$url</a>
		</body>
		</html>
HTM;
		header('Refresh: 0; URL='.$url);
		echo $output;
		exit;
	}
	else
	{
		header('Location: '.$url);
		exit;
	}
}

/**
 * Splits a query string into keys and values array. In comparison with built-in
 * parse_str() function, this doesn't apply addslashes and urldecode to parameters
 * and does not support arrays and complex parameters.
 *
 * @param string $str Query string
 * @return array
 */
function cot_parse_str($str)
{
	$res = array();
	$str = str_replace('&amp;', '&', $str);
	foreach (explode('&', $str) as $item)
	{
		if (!empty($item))
		{
			list($key, $val) = explode('=', $item);
			$res[$key] = $val;
		}
	}
	return $res;
}

/**
 * Transforms parameters into URL by following user-defined rules.
 * This function can be overloaded by cot_url_custom().
 *
 * @param string $name Module or script name
 * @param mixed $params URL parameters as array or parameter string
 * @param string $tail URL postfix, e.g. anchor
 * @param bool $htmlspecialchars_bypass If TRUE, will not convert & to &amp; and so on.
 * @param bool $ignore_appendix If TRUE, $cot_url_appendix will be ignored for this URL
 * @return string Valid HTTP URL
 */
function cot_url($name, $params = '', $tail = '', $htmlspecialchars_bypass = false, $ignore_appendix = false)
{
	global $cot_url_appendix;
	// Preprocess arguments
	if (is_string($params))
	{
		$params = cot_parse_str($params);
	}
	elseif (!is_array($params))
	{
		$params = array();
	}
	if (!$ignore_appendix && count($cot_url_appendix) > 0)
	{
		$params = $params + $cot_url_appendix;
	}

	foreach ($params as $k => $param)
	{
		if (is_bool($param))
		{
			$params[$k] = (int)$param;
		}
		if (!is_array($param) && !is_object($param))
		{
			$params[$k] = strval($param);
		}
		if ($params[$k] === '')
		{
			unset($params[$k]);
		}
	}

	if (function_exists('cot_url_custom'))
	{
		return cot_url_custom($name, $params, $tail, $htmlspecialchars_bypass);
	}

	$url = in_array($name, array('admin', 'login', 'message')) ? "$name.php" : 'index.php';
	if (!in_array($name, array('admin', 'index', 'login', 'message', 'plug')))
	{
		$params = array('e' => $name) + $params;
	}
	// Append query string if needed
	if (count($params) > 0)
	{
		$sep = $htmlspecialchars_bypass ? '&' : '&amp;';
		if (version_compare(PHP_VERSION, '5.4.0', '>='))
		{
			$url .= '?' . http_build_query($params, '', $sep, PHP_QUERY_RFC3986);
		}
		else
		{
			$url .= '?' . str_replace('+', '%20', http_build_query($params, '', $sep));
		}
	}
	$url .= $tail;
	//$url = str_replace('&amp;amp;', '&amp;', $url);
	return $url;
}

/**
 * Checks if an absolute URL belongs to current site or its subdomains
 *
 * @param string $url Absolute URL
 * @return bool
 */
function cot_url_check($url)
{
	global $sys;
	return preg_match('`^'.preg_quote($sys['scheme'].'://').'([^/]+\.)?'.preg_quote($sys['domain']).'`i', $url);
}

/**
 * Transliterates a string if transliteration is available
 *
 * @param string $str Source string
 * @return string
 */

function cot_translit_encode($str)
{
	global $lang, $cot_translit;
	static $lang_loaded = false;
	if ($lang != 'en' && !$lang_loaded)
	{
		require_once cot_langfile('translit', 'core');
		$lang_loaded = true;
	}
	if ($lang != 'en' && is_array($cot_translit))
	{
		// Apply transliteration
		$str = strtr($str, $cot_translit);
	}
	return $str;
}

/**
 * Backwards transition for cot_translit_encode
 *
 * @param string $str Encoded string
 * @return string
 */
function cot_translit_decode($str)
{
	global $lang, $cot_translitb;
	if ($lang != 'en' && is_array($cot_translitb))
	{
		// Apply transliteration
		$str = strtr($str, $cot_translitb);
	}
	return $str;
}

/**
 * Store URI-redir to session
 *
 * @global $sys
 */
function cot_uriredir_store()
{
	global $sys;

	if ($_SERVER['REQUEST_METHOD'] != 'POST' // not form action/POST
		&& empty($_GET['x']) // not xg, hence not form action/GET and not command from GET
		&& !defined('COT_MESSAGE') // not message location
		&& !defined('COT_AUTH') // not login/logout location
		&&	(!defined('COT_USERS')
			|| empty($_GET['m'])
			|| !in_array($_GET['m'], array('auth', 'logout', 'register'))
	)
	)
	{
		$_SESSION['s_uri_redir'] = $sys['uri_redir'];
	}
}

/**
 * Apply URI-redir that stored in session
 *
 * @param bool $cfg_redir Configuration of redirect back
 * @global $redirect
 */
function cot_uriredir_apply($cfg_redir = true)
{
	global $redirect;

	if ($cfg_redir && empty($redirect) && !empty($_SESSION['s_uri_redir']))
	{
		$redirect = $_SESSION['s_uri_redir'];
	}
}

/**
 * Checks URI-redir for xg before redirect
 *
 * @param string $uri Target URI
 */
function cot_uriredir_redirect($uri)
{
	if (mb_strpos($uri, '&x=') !== false || mb_strpos($uri, '?x=') !== false)
	{
		$uri = cot_url('index'); // xg, not redirect to form action/GET or to command from GET
	}
	cot_redirect($uri);
}

/*
 * ========================= Internationalization (i18n) ======================
*/

$cot_languages['cn']= '中文';
$cot_languages['de']= 'Deutsch';
$cot_languages['dk']= 'Dansk';
$cot_languages['en']= 'English';
$cot_languages['es']= 'Español';
$cot_languages['fi']= 'Suomi';
$cot_languages['fr']= 'Français';
$cot_languages['gr']= 'Greek';
$cot_languages['hu']= 'Hungarian';
$cot_languages['it']= 'Italiano';
$cot_languages['jp']= '日本語';
$cot_languages['kr']= '한국어';
$cot_languages['nl']= 'Dutch';
$cot_languages['pl']= 'Polski';
$cot_languages['pt']= 'Portugese';
$cot_languages['ru']= 'Русский';
$cot_languages['se']= 'Svenska';
$cot_languages['ua'] = 'Українська';

/**
 * Makes correct plural forms of words
 *
 * @global string $lang Current language
 * @param int $digit Numeric value
 * @param mixed $expr Word or expression
 * @param bool $onlyword Return only words, without numbers
 * @param bool $canfrac - Numeric value can be Decimal Fraction
 * @return string
 */
function cot_declension($digit, $expr, $onlyword = false, $canfrac = false)
{
	global $lang, $Ls;

	$expr = is_string($expr) ? $Ls[$expr] : $expr;
	if (!is_array($expr))
	{
		return trim(($onlyword ? '' : "$digit ").$expr);
	}

	$is_frac = false;
	if ($canfrac)
	{
		if ((is_float($digit) && $digit!=floor($digit)) || mb_strpos($digit, '.') !== false)
		{
			$i = floatval($digit);
			$is_frac = true;
		}
		else
		{
			$i = intval($digit);
		}
	}
	else
	{
		$i = intval(preg_replace('#\D+#', '', $digit));
	}

	$plural = cot_get_plural($i, $lang, $is_frac);
	$cnt = count($expr);
	return trim(($onlyword ? '' : "$digit ").(($cnt > 0 && $plural < $cnt) ? $expr[$plural] : ''));
}

/**
 * Used in cot_declension to get rules for concrete languages
 *
 * @param int $plural Numeric value
 * @param string $lang Target language code
 * @param bool $is_frac true if numeric value is fraction, otherwise false
 * @return int
 */
function cot_get_plural($plural, $lang, $is_frac = false)
{
	switch ($lang)
	{
		case 'en':
		case 'de':
		case 'nl':
		case 'se':
		case 'us':
			return ($plural == 1) ? 1 : 0;

		case 'fr':
			return ($plural > 1) ? 0 : 1;

		case 'ru':
		case 'ua':
			if ($is_frac)
			{
				return 1;
			}
			$plural %= 100;
			return (5 <= $plural && $plural <= 20) ? 2 : ((1 == ($plural %= 10)) ? 0 : ((2 <= $plural && $plural <= 4) ? 1 : 2));

		default:
			return 0;
	}
}

/*
 * ============================================================================
*/

if (isset($cfg['customfuncs']) && $cfg['customfuncs'])
{
	require_once $cfg['system_dir'] . '/functions.custom.php';
}

?>
