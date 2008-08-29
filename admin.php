<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=Administration panel loader
[END_SED]
==================== */

define('SED_CODE', TRUE);
define('SED_ADMIN', TRUE);
$location = 'Administration';
$z = 'admin';

require_once('./datas/config.php');
require_once($cfg['system_dir'].'/functions.php');
require_once($cfg['system_dir'].'/functions.admin.php');
require_once($cfg['system_dir'].'/common.php');
require_once($cfg['system_dir'].'/lang/'.$usr['lang'].'/admin.lang.php');
require_once($cfg['system_dir'].'/core/admin/admin.inc.php');

?>
