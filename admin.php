<?php
/**
 * Administration panel loader
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

define('COT_CODE', TRUE);
define('COT_ADMIN', TRUE);
define('COT_CORE', TRUE);
$env['location'] = 'administration';
$env['ext'] = 'admin';

require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/cotemplate.php';
require_once $cfg['system_dir'] . '/common.php';

if ($cfg['admintheme'])
{
	if (file_exists("{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/{$cfg['admintheme']}.{$usr['lang']}.lang.php"))
	{
		require_once "{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/{$cfg['admintheme']}.{$usr['lang']}.lang.php";
	}
	elseif (file_exists("{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/{$cfg['admintheme']}.en.lang.php"))
	{
		require_once "{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/{$cfg['admintheme']}.en.lang.php";
	}
	if (file_exists("{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/{$cfg['admintheme']}.php"))
	{
		require_once "{$cfg['themes_dir']}/admin/{$cfg['admintheme']}/{$cfg['admintheme']}.php";
	}
}

require_once cot_incfile('admin', 'module');

include cot_incfile('admin', 'module', 'main');

?>