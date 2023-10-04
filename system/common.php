<?php
/**
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/* ======== First... ======== */

if (version_compare(PHP_VERSION, '6.0.0', '<=')) {
	if (get_magic_quotes_gpc()) {
		function cot_disable_mqgpc(&$value, $key) {
			$value = stripslashes($value);
		}
		$gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		array_walk_recursive($gpc, 'cot_disable_mqgpc');
	}
}

const MQGPC = false;

if ($cfg['display_errors']) {
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors', 1);
} else {
	error_reporting(0);
	ini_set('display_errors', 0);
}

if ($cfg['debug_mode']) {
	require_once $cfg['system_dir'] . '/debug.php';
}


require_once './lib/autoload.php';

register_shutdown_function('cot_shutdown');

// Each user has his own timezone preference based on offset from GMT, so all dates are UTC/GMT by default
date_default_timezone_set('UTC');
$sys['day'] = date('Y-m-d');
$sys['now'] = time();
$site_id = 'ct' . substr(md5(empty($cfg['site_id']) ? $cfg['mainurl'] : $cfg['site_id']), 0, 16);
$sys['site_id'] = $site_id;

// Getting the server-relative path
$url = parse_url($cfg['mainurl']);

$sys['scheme'] = 'http';
if (
    !empty($cfg['force_https'])
    || mb_strpos($_SERVER['SERVER_PROTOCOL'], 'HTTPS') !== false
       // Set to a non-empty value if the script was queried through the HTTPS protocol.
    || !empty($_SERVER['HTTPS'])
    || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
    || (!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == 443)
    || (!empty($_SERVER['X_FORWARDED-PROTO']) && $_SERVER['X_FORWARDED-PROTO'] == 'https')
) {
	$sys['scheme'] = 'https';
}

$sys['secure'] = $sys['scheme'] == 'https' ? true : false;

$sys['domain'] = preg_replace('#^www\.#', '', $url['host']);
if (
    $_SERVER['HTTP_HOST'] == $url['host']
    || $cfg['multihost']
    || (
        $_SERVER['HTTP_HOST'] != 'www.' . $sys['domain']
    	&& preg_match('`^.+\.' . preg_quote($sys['domain']) . '$`i', $_SERVER['HTTP_HOST'])
    )
) {
	$sys['host'] = preg_match('#^[\w\p{L}\.\-]+(:\d+)?$#u', $_SERVER['HTTP_HOST']) ?
            preg_replace('#^([\w\p{L}\.\-]+)(:\d+)?$#u', '$1', $_SERVER['HTTP_HOST']) : $url['host'];

    if (isset($url['port'])) {
        $sys['port'] = $url['port'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PORT'])) {
        $sys['port'] = $_SERVER['HTTP_X_FORWARDED_PORT'];
    } else {
        $sys['port'] = $_SERVER['SERVER_PORT'];
    }

} else {
	$sys['host'] = $url['host'];
	$sys['port'] = isset($url['port']) ? $url['port'] : '';
}
$def_port = $sys['secure'] ? 443 : 80;
$sys['port'] = ($sys['port'] == $def_port) ? '' : $sys['port'];
if (!empty($cfg['force_https']) && empty($url['port']) && $sys['port'] == 80) {
    $sys['port'] = '';
}

$sys['domain'] = preg_replace('#^www\.#', '', $sys['host']);

$sys['site_uri'] = '/';
if (!empty($url['path'])) {
	$sys['site_uri'] = $url['path'];
	if ($sys['site_uri'][mb_strlen($sys['site_uri']) - 1] != '/') {
        $sys['site_uri'] .= '/';
    }
}

define('COT_SITE_URI', $sys['site_uri']);

// Request URI relative to $sys['abs_url']
$sys['uri'] = '/';
// Request query after the question mark '?'
$sys['query'] = '';
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] !== '/') {
    $parsedUri = cot_parse_url($_SERVER['REQUEST_URI']);
    $sys['query'] = isset($parsedUri['query']) ? $parsedUri['query'] : '';
    $sys['uri'] = $parsedUri['path'];
    if ($sys['site_uri'] !== '/') {
        $tmpRequestUri = '/' . trim($parsedUri['path'], '/') . '/';
        $tmpSiteUri = '/' . trim($sys['site_uri'], '/') . '/';

        $position = mb_strrpos($tmpRequestUri, $tmpSiteUri);
        if ($position !== false) {
            $startString = mb_substr($tmpRequestUri, 0, $position);
            $endString = mb_substr($tmpRequestUri, $position + mb_strlen($tmpSiteUri), mb_strlen($tmpRequestUri));

            $sys['uri'] = '/' . trim($startString . $endString,  '/') . '/';
            unset($startString, $endString);
        }
        unset($tmpRequestUri, $tmpSiteUri, $position);
    }
    unset($parsedUri);
}

// Absolute site url
$sys['abs_url'] = $sys['scheme'] . '://' . $sys['host'] . ($sys['port'] ? ':' . $sys['port'] : '') . $sys['site_uri'];
$sys['canonical_url'] = $sys['scheme'] . '://' . $sys['host'] . ($sys['port'] ? ':' . $sys['port'] : '') .
    cot_url_sanitize($_SERVER['REQUEST_URI']);
define('COT_ABSOLUTE_URL', $sys['abs_url']);

// Reassemble mainurl if necessary
if ($cfg['multihost']) {
	$cfg['mainurl'] = mb_substr($sys['abs_url'], 0, -1);
}

session_set_cookie_params(0, $sys['site_uri'], '.' . $sys['domain']);

session_start();

cot_unregister_globals();

/* =========== Early page cache ==========*/
$cache = null;
if ($cfg['cache'] && !$cfg['debug_mode']) {
	require_once !empty($cfg['custom_cache']) ? $cfg['custom_cache'] : $cfg['system_dir'] . '/cache.php';
	$cache = new Cache();

    if (
        $_SERVER['REQUEST_METHOD'] == 'GET'
        && empty($_COOKIE[$sys['site_id']])
        && empty($_SESSION[$sys['site_id']])
        && !defined('COT_AUTH')
        && !defined('COT_ADMIN')
        && !defined('COT_INSTALL')
        && !defined('COT_MESSAGE')

        // @todo move to system controller when it will be implemented
        && !(empty($_GET['e']) && $sys['uri'] === '/' && !empty($_GET['a']) && $_GET['a'] === 'get')
    ) {
        $ext = cot_import('e', 'G', 'ALP');

        $cache_ext = empty($ext) ? 'index' : preg_replace('#\W#', '', $ext);
        if (isset($cfg['cache_' . $cache_ext]) && $cfg['cache_' . $cache_ext]) {
            $cacheRequest = $sys['uri'];
            if ($sys['query'] !== '') {
                $cacheRequest .= '?' . $sys['query'];
            }
            $cache->static->initByUri($cacheRequest, $cfg['defaulttheme']);
            unset($cacheRequest);
            $cache->static->read();
        }
        unset($ext, $cache_ext);
    }
}

/* ======== Connect to the SQL DB======== */
try {
	$db = new CotDB([
		'host' => $cfg['mysqlhost'],
		'port' => !empty($cfg['mysqlport']) ? $cfg['mysqlport'] : null,
		'tablePrefix' => $db_x,
		'user' => $cfg['mysqluser'],
		'password' => $cfg['mysqlpassword'],
		'dbName' => $cfg['mysqldb'],
		'charset' => !empty($cfg['mysqlcharset']) ? $cfg['mysqlcharset'] : null,
		'collate' => !empty($cfg['mysqlcollate']) ? $cfg['mysqlcollate'] : null,
	]);
} catch (PDOException $e) {
	cot_diefatal('Could not connect to database !<br />
		Please check your settings in the file datas/config.php<br />
		MySQL error : '.$e->getMessage());
}
unset($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword'], $dbc_port);

// Here we can init our globals facade
Cot::init();

$cache && $cache->init();

/* ======== Configuration settings ======== */
if (!isset($cot_cfg)) {
    $cot_cfg = null;
}
if ($cache && $cot_cfg) {
	$cfg = cot_arrayMergeRecursive($cot_cfg, $cfg);
} else {
	// Part 1: Load main configuration
	$sql_config = $db->query("SELECT * FROM $db_config");
	while ($row = $sql_config->fetch()) {
		if ($row['config_owner'] == 'core') {
			$cfg[$row['config_name']] = $row['config_value'];
		} elseif ($row['config_owner'] == 'module') {
			if (empty($row['config_subcat'])) {
				$cfg[$row['config_cat']][$row['config_name']] = $row['config_value'];
			} else {
				$cfg[$row['config_cat']]['cat_' . $row['config_subcat']][$row['config_name']] = $row['config_value'];
			}
		} else {
			$cfg['plugin'][$row['config_cat']][$row['config_name']] = $row['config_value'];
		}
	}
	$sql_config->closeCursor();
}
// Mbstring options
mb_internal_encoding('UTF-8');

/* ======== Extra settings (the other presets are in functions.php) ======== */
if (
    isset($_SERVER['HTTP_CLIENT_IP'])
    && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)
) {
    $usr['ip'] = $_SERVER['HTTP_CLIENT_IP'];
} elseif (
    isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])
    && filter_var($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'], FILTER_VALIDATE_IP)
) {
    $usr['ip'] = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
} elseif (
    isset($_SERVER['HTTP_X_REAL_IP'])
    && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)
) {
    $usr['ip'] = $_SERVER['HTTP_X_REAL_IP'];
} elseif (
    isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)
) {
    $usr['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $usr['ip'] = $_SERVER['REMOTE_ADDR'];
}
/**
 * We are trying to use filter_var('...', FILTER_VALIDATE_IP) insted. If it will be ok, code below will be removed
 * @deprecated
 */
//if (
//    !preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $usr['ip'])
//    && !preg_match(
/*        '#^(((?=(?>.*?(::))(?!.+\3)))\3?|([\dA-F]{1,4}(\3|:(?!$)|$)|\2))(?4){5}((?4){2}|(25[0-5]|(2[0-4]|1\d|[1-9])?\d)(\.(?7)){3})\z#i',*/
//        $usr['ip']
//    )
//) {
//	$usr['ip'] = '0.0.0.0';
//}

$sys['unique'] = cot_unique(16);

if (empty($cfg['cookiedomain'])) {
    $cfg['cookiedomain'] = $sys['domain'];
}
if (empty($cfg['cookiepath'])) {
    $cfg['cookiepath'] = $sys['site_uri'];
}

// URI redirect appliance
$sys['uri_curr'] = (mb_stripos($_SERVER['REQUEST_URI'], $sys['site_uri']) === 0) ?
	mb_substr($_SERVER['REQUEST_URI'], mb_strlen($sys['site_uri'])) : ltrim($_SERVER['REQUEST_URI'], '/');
$sys['uri_redir'] = base64_encode($sys['uri_curr']);
$sys['url_redirect'] = 'redirect='.$sys['uri_redir'];
$redirect = cot_import('redirect','G','TXT');
if (!empty($redirect)) $redirect = preg_replace('/[^a-zA-Z0-9_=\/]/', '', $redirect);
$out['uri'] = str_replace('&', '&amp;', $sys['uri_curr']);

define(
    'COT_AJAX',
    (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    || (!empty($_SERVER['X-Requested-With']) && strtolower($_SERVER['X-Requested-With']) == 'xmlhttprequest')
    || cot_import('_ajax', 'G', 'INT') == 1
);

// Other system variables
$sys['parser'] = $cfg['parser'];

/* ======== Modules and plugins ======== */
$extensions = [];
if (
    (empty($cot_plugins) && !defined('COT_INSTALL'))
    || empty($cot_modules)
) {
    $extensions = Cot::$db->query("SELECT * FROM " . Cot::$db->core . " WHERE ct_state = 1 AND ct_lock = 0")->fetchAll();
}
if (empty($cot_plugins) && !defined('COT_INSTALL')) {
	$sql = Cot::$db->query("SELECT pl_code, pl_file, pl_hook, pl_module, pl_title FROM " . Cot::$db->plugins .
	" WHERE pl_active = 1 ORDER BY pl_hook ASC, pl_order ASC");

	$cot_plugins = [];

	/**
	* @var array<string, bool> $cot_plugins_active
	* @deprecated use $cot_plugins_enabled instead
	*/
	$cot_plugins_active = [];

	if ($sql->rowCount() > 0) {
		while ($row = $sql->fetch()) {
			$cot_plugins[$row['pl_hook']][] = $row;
		}
	}
	$sql->closeCursor();

	if (!empty($extensions)) {
		foreach ($extensions as $row) {
			if ($row['ct_plug']) {
				$cot_plugins_active[$row['ct_code']] = true;
			}
		}
	}

	if (!empty(Cot::$cache)) {
		Cot::$cache->db->store('cot_plugins', $cot_plugins, 'system');
		Cot::$cache->db->store('cot_plugins_active', $cot_plugins_active, 'system');
	}
}

if (empty($cot_modules)) {
	$cot_plugins_enabled = [];
	$cot_modules = [];
	if (!empty($extensions)) {
		foreach ($extensions as $row) {
			if ($row['ct_plug']) {
				$cot_plugins_enabled[$row['ct_code']] = array(
					'code' => $row['ct_code'],
					'title' => $row['ct_title'],
					'version' => $row['ct_version']
				);
			} else {
				$cot_modules[$row['ct_code']] = array(
					'code' => $row['ct_code'],
					'title' => $row['ct_title'],
					'version' => $row['ct_version']
				);
			}
		}
	}

	if (!empty(Cot::$cache)) {
		Cot::$cache->db->store('cot_modules', $cot_modules, 'system');
		Cot::$cache->db->store('cot_plugins_enabled', $cot_plugins_enabled, 'system');
	}
}
unset($extensions);

/* ======== Gzip and output filtering ======== */

if ($cfg['gzip']) {
	@ob_start('ob_gzhandler');
} else {
	ob_start();
}

ob_start('cot_outputfilters');

/* ======== Groups ======== */

if (empty($cot_groups )) {
	$sql = $db->query("SELECT * FROM $db_groups WHERE grp_disabled=0 ORDER BY grp_level DESC");

	if ($sql->rowCount() > 0) {
		while ($row = $sql->fetch()) {
			$cot_groups[$row['grp_id']] = array(
				'id' => $row['grp_id'],
				'alias' => $row['grp_alias'],
				'level' => $row['grp_level'],
				'disabled' => $row['grp_disabled'],
				'hidden' => !empty($row['grp_hidden']),
				'state' => isset($row['grp_state']) ? $row['grp_state'] : null,
				'name' => htmlspecialchars($row['grp_name']),
				'title' => htmlspecialchars($row['grp_title']),
				'desc' => htmlspecialchars($row['grp_desc']),
				'icon' => $row['grp_icon'],
				'pfs_maxfile' => isset($row['grp_pfs_maxfile']) ? $row['grp_pfs_maxfile'] : null,
				'pfs_maxtotal' => isset($row['grp_pfs_maxtotal']) ? $row['grp_pfs_maxtotal'] : null,
				'ownerid' => $row['grp_ownerid'],
				'skiprights' => isset($row['grp_skiprights']) ? $row['grp_skiprights'] : false
			);
		}
		$sql->closeCursor();
	} else {
		cot_diefatal('No groups found.'); // TODO: Need translate
	}

	$cache && $cache->db->store('cot_groups', $cot_groups, 'system');
}

/* ======== User/Guest ======== */

$usr['id'] = 0;
$usr['sessionid'] = '';
$usr['name'] = '';
$usr['level'] = 0;
$usr['lastvisit'] = 30000000000;
$usr['lastlog'] = 0;
$usr['timezone'] = cot_timezone_offset($cfg['defaulttimezone'], true);
$usr['timezonename'] = $cfg['defaulttimezone'];
$usr['newpm'] = 0;
$usr['messages'] = 0;

$csid = cot_import($sys['site_id'], 'COOKIE', 'TXT');
if (!empty($csid) || !empty($_SESSION[$sys['site_id']])) {
	$u = empty($_SESSION[$sys['site_id']]) ?
        explode(':', base64_decode($csid)) : explode(':', base64_decode($_SESSION[$sys['site_id']]));
	$u_id = (int) cot_import($u[0], 'D', 'INT');
	$u_sid = $u[1];
	if ($u_id > 0) {
		$sql = $db->query("SELECT * FROM $db_users WHERE user_id = $u_id");
		if ($row = $sql->fetch()) {
			if (
                $u_sid == hash_hmac('sha1', $row['user_sid'], $cfg['secret_key'])
				&& (
				    $row['user_maingrp'] > 3
                    || (
					    $row['user_maingrp'] == COT_GROUP_INACTIVE
                        && isset($cfg['users']['inactive_login'])
                        && $cfg['users']['inactive_login']
				    )
				)
                && ($cfg['ipcheck'] == FALSE || $row['user_lastip'] == $usr['ip'])
                && ($row['user_sidtime'] + $cfg['cookielifetime'] > $sys['now'])
            ) {
				$usr['id'] = (int) $row['user_id'];
				$usr['name'] = $row['user_name'];
				$usr['maingrp'] = $row['user_maingrp'];
				$usr['lastvisit'] = $row['user_lastvisit'];
				$usr['lastlog'] = $row['user_lastlog'];
				$usr['timezone'] = cot_timezone_offset($row['user_timezone'], true);
				$usr['timezonename'] = $row['user_timezone'];
				$usr['theme'] = $cfg['forcedefaulttheme'] ? $cfg['defaulttheme'] : $row['user_theme'];
				$usr['scheme'] = $cfg['forcedefaulttheme'] ? $cfg['defaultscheme'] : $row['user_scheme'];
				$usr['lang'] = $cfg['forcedefaultlang'] ? $cfg['defaultlang'] : $row['user_lang'];
				$usr['newpm'] = !empty($row['user_newpm']) ? $row['user_newpm'] : false;
				$usr['auth'] = isset($row['user_auth']) ? unserialize($row['user_auth']) : null;
				$usr['adminaccess'] = cot_auth('admin', 'any', 'R');
				$usr['level'] = $cot_groups[$usr['maingrp']]['level'];
				$usr['profile'] = $row;

				$sys['xk'] = $row['user_token'];

				if (!isset($_SESSION['cot_user_id'])) {
					$_SESSION['cot_user_id'] = $usr['id'];
				}

				if ($usr['lastlog'] + $cfg['timedout'] < $sys['now']) {
					$sys['comingback'] = TRUE;
					if ($usr['lastlog'] > $usr['lastvisit']) {
						$usr['lastvisit'] = $usr['lastlog'];
						$user_log['user_lastvisit'] = $usr['lastvisit'];
					}

					// Generate new security token
					$token = cot_unique(16);
					$sys['xk_prev'] = $sys['xk'];
					$sys['xk'] = $token;
					$user_log['user_token'] = $token;
				}

				if (!$cfg['authcache'] || empty($row['user_auth'])) {
					$usr['auth'] = cot_auth_build($usr['id'], $usr['maingrp']);
					$cfg['authcache'] && $user_log['user_auth'] = serialize($usr['auth']);
				}

				$user_log['user_lastlog'] = $sys['now'];

				$db->update($db_users, $user_log, "user_id={$usr['id']}");
				unset($u, $passhash, $oldhash, $hashsalt, $hashsaltprev, $user_log);
			}
		}
	}

	// User can't log in, destroy authorization cookie and session data
	if ($usr['id'] == 0) {
        if (!empty($csid)) {
			cot_setcookie(
				$sys['site_id'],
				'',
				time() - 63072000,
				$cfg['cookiepath'],
				$cfg['cookiedomain'],
				$sys['secure'],
				true
			);
        }

        if (isset($_SESSION[$sys['site_id']])) {
            unset($_SESSION[$sys['site_id']]);
        }
    }
}

if ($usr['id'] == 0) {
	if (empty($cot_guest_auth)) {
		$cot_guest_auth = cot_auth_build(0);
		$cache && $cache->db->store('cot_guest_auth', $cot_guest_auth, 'system');
	}
	$usr['auth'] = $cot_guest_auth;
	unset($cot_guest_auth);
	$usr['theme'] = $cfg['defaulttheme'];
	$usr['scheme'] = $cfg['defaultscheme'];
	$usr['lang'] = $cfg['defaultlang'];
	$usr['maingrp'] = COT_GROUP_GUESTS;
	$sys['xk'] = mb_strtoupper(dechex(crc32($sys['site_id']))); // Site related key for guests
}

$lang = $usr['lang'];

if (
    defined('COT_MESSAGE')
    && isset($_SESSION['s_run_admin'])
    && $_SESSION['s_run_admin']
    && cot_auth('admin', 'any', 'R')
) {
	define('COT_ADMIN', TRUE);
} else {
	$_SESSION['s_run_admin'] = defined('COT_ADMIN');
}

/* ======== Category Structure ======== */
if (empty($structure)) {
	require_once cot_incfile('extrafields');
	cot_load_structure();
	$cache && $cache->db->store('structure', $structure, 'system');
}

if (!$cache || !$cot_cfg) {
	// Fill missing options with default values
	foreach ($structure as $module => $mod_struct) {
		if (
		    isset($cfg[$module]['cat___default'])
		    && isset($mod_struct)
		    && is_array($cfg[$module]['cat___default'])
		    && is_array($mod_struct)
        ) {
			foreach ($mod_struct as $cat => $row) {
				foreach ($cfg[$module]['cat___default'] as $key => $val) {
					if (!isset($cfg[$module]['cat_' . $cat][$key])) {
						$cfg[$module]['cat_' . $cat][$key] = $val;
					}
				}
			}
		}
	}

	// Save configuration at this point
	$cache && $cache->db->store('cot_cfg', $cfg, 'system');
}
unset($cot_cfg);

/* === Hook === */
foreach (cot_getextplugins('input') as $pl) {
	include $pl;
}
/* ======================== */


/* ======== Maintenance mode ======== */

if ($cfg['maintenance'] && !defined('COT_INSTALL')) {
	$sqll = $db->query("SELECT grp_maintenance FROM $db_groups WHERE grp_id='" . $usr['maingrp'] . "' ");
	$roow = $sqll->fetch();

	if (!$roow['grp_maintenance'] && !defined('COT_AUTH')) {
		cot_redirect(cot_url('login'));
	}
}

/* ======== Anti-hammering =========*/

if ($cfg['shieldenabled'] && ($usr['id'] == 0 || !cot_auth('admin', 'a', 'A') || $cfg['shield_force'])) {
	$shield_limit = isset($_SESSION['online_shield']) ? $_SESSION['online_shield'] : 0;
	$shield_action = isset($_SESSION['online_action']) ? $_SESSION['online_action'] : '';
	$shield_hammer = cot_shield_hammer(
		isset($_SESSION['online_hammer']) ? $_SESSION['online_hammer'] : 0,
		$shield_action,
		isset($_SESSION['online_lastseen']) ? $_SESSION['online_lastseen'] : 0
	);
	$_SESSION['online_hammer'] = $sys['online_hammer'] = $shield_hammer;
	$_SESSION['online_lastseen'] = (int)$sys['now'];
}

/* ======== Zone variables ======== */

$m = cot_import('m', 'G', 'ALP', 24);
$n = cot_import('n', 'G', 'ALP', 24);
$a = cot_import('a', 'G', 'ALP', 24);
$b = cot_import('b', 'G', 'ALP', 24);

/* ======== Language ======== */

require_once cot_langfile('main', 'core');
require_once cot_langfile('users', 'core');

if (defined('COT_ADMIN')) {
	require_once cot_langfile('admin', 'core');
}

/* ======== Theme / color scheme ======== */
if (empty($cfg['themes_dir'])) {
	$cfg['themes_dir'] = 'themes';
}

$mtheme = "{$cfg['themes_dir']}/{$usr['theme']}/header.tpl";
if (!file_exists($mtheme)) {
	$out['notices_array'][] = $L['com_themefail'];
	$usr['theme'] = $cfg['defaulttheme'];
	$mtheme = "{$cfg['themes_dir']}/{$usr['theme']}/header.tpl";
	if (!file_exists($mtheme)) {
		cot_diefatal($L['com_defthemefail']);
	}
}

$cfg['admintheme'] = !empty($cfg['admintheme']) ? $cfg['admintheme'] : '';

$usr['def_theme_lang'] = "{$cfg['themes_dir']}/{$usr['theme']}/{$usr['theme']}.en.lang.php";
if (defined('COT_ADMIN')) {
    $usr['def_theme_lang'] = '';
    if (!empty($cfg['admintheme'])) {
        $usr['def_theme_lang'] = "{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/{$cfg['admintheme']}.en.lang.php";
    }
}

$usr['theme_lang'] = "{$cfg['themes_dir']}/{$usr['theme']}/{$usr['theme']}.{$usr['lang']}.lang.php";
if (defined('COT_ADMIN')) {
    $usr['theme_lang'] = '';
    if (!empty($cfg['admintheme'])) {
        $usr['theme_lang'] = "{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/{$cfg['admintheme']}.{$usr['lang']}.lang.php";
    }
}

if (!empty($usr['theme_lang']) && $usr['theme_lang'] != $usr['def_theme_lang'] && @file_exists($usr['theme_lang'])) {
	require_once $usr['theme_lang'];
} elseif (!empty($usr['def_theme_lang']) && @file_exists($usr['def_theme_lang'])) {
	require_once $usr['def_theme_lang'];
}

$theme = $usr['theme'];
$scheme = $usr['scheme'];

// Resource strings
require_once $cfg['system_dir'] . '/resources.rc.php';

if (defined('COT_ADMIN')) {
	require_once cot_incfile('admin', 'module', 'resources');
}

// Theme resources
$sys['theme_resources'] = "{$cfg['themes_dir']}/{$usr['theme']}/{$usr['theme']}.php";
if (defined('COT_ADMIN')) {
	$sys['theme_resources'] = '';
	if (!empty($cfg['admintheme'])) {
		$sys['theme_resources'] = "{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/{$cfg['admintheme']}.php";
	}
}

if (!empty($sys['theme_resources']) && file_exists($sys['theme_resources'])) {
	$L_tmp = $L;
	$R_tmp = $R;
	include $sys['theme_resources'];
	// Save overridden strings in $theme_reload global
	// Todo more right way
	$theme_reload['L'] = @array_diff_assoc($L,$L_tmp);
	$theme_reload['R'] = @array_diff_assoc($R,$R_tmp);
	unset($L_tmp, $R_tmp);
}

// Iconpack
if (empty($cfg['defaulticons'])) {
	$cfg['defaulticons'] = 'default';
}
if (empty($usr['icons'])) {
	$usr['icons'] = $cfg['defaulticons'];
}

if (file_exists($cfg['icons_dir'] . '/' . $usr['icons'] . '/resources.php')) {
	require_once $cfg['icons_dir'] . '/' . $usr['icons'] . '/resources.php';
} else {
	require_once './images/icons/' . $cfg['defaulticons'] . '/resources.php';
}

$out['copyright'] = "<a href=\"https://www.cotonti.com\" target=\"_blank\">" . $L['foo_poweredby'] . " Cotonti</a>";

/* ======== Various ======== */

$cot_yesno[0] = $L['No'];
$cot_yesno[1] = $L['Yes'];

/* ======== Local/GMT time ======== */

$usr['timetext'] = cot_build_timezone($usr['timezone']);
$usr['gmttime'] = cot_date('datetime_medium', $sys['now'], false).' GMT';
$usr['localtime'] = cot_date('datetime_medium', $sys['now']);

/* ======== Anti-XSS protection ======== */

$x = cot_import('x', 'P', 'ALP');
if (empty($x) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	$x = cot_import('x', 'G', 'ALP');
}

$referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
if (
    $_SERVER['REQUEST_METHOD'] == 'POST'
    && !defined('COT_NO_ANTIXSS')
    && (
        !defined('COT_AUTH')
	    && $x != $sys['xk']
        && (empty($sys['xk_prev']) || $x != $sys['xk_prev'])
	    || ($cfg['referercheck'] && !preg_match('`https?://([^/]+\.)?'.preg_quote($sys['domain']).'(/|:|$)`i', $referer))
    )
) {
	$cot_error = true;
	cot_die_message(950, TRUE, '', '', $referer);
}

/* ============ Head Resources ===========*/
if (!COT_AJAX) {
	// May Be move it to header.php?
	if (!isset($cot_rc_html[$theme]) || !$cache || !$cfg['headrc_consolidate'] || defined('COT_ADMIN')) {
		// Load standard resources
		cot_rc_add_standard();

		// Invoke rc handlers
		foreach (cot_getextplugins('rc') as $pl) {
			include $pl;
		}
	}
	if (!defined('COT_ADMIN')) {
		if (file_exists("{$cfg['themes_dir']}/{$usr['theme']}/{$usr['theme']}.rc.php")) {
			include "{$cfg['themes_dir']}/{$usr['theme']}/{$usr['theme']}.rc.php";
		}
	}
}
/* ============ /Head Resources ===========*/

// Cotonti-specific XTemplate initialization
if (class_exists('XTemplate')) {
	XTemplate::init(array(
		'cache'        => $cfg['xtpl_cache'],
		'cache_dir'    => $cfg['cache_dir'],
		'cleanup'      => $cfg['html_cleanup'],
		'debug'        => $cfg['debug_mode'],
		'debug_output' => isset($_GET['tpl_debug']) ? (bool) $_GET['tpl_debug'] : false
	));
}

/* ======== Global hook ======== */

foreach (cot_getextplugins('global') as $pl) {
	include $pl;
}
