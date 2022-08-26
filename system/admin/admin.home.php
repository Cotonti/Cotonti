<?php

/**
 * Administration panel - Home page for administrators
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$t = new XTemplate(cot_tplfile('admin.home', 'core'));

if (!cot::$cfg['debug_mode'] && file_exists('install.php') && is_writable('datas/config.php')) {
	cot_error('home_installable_error');
}

$adminsubtitle = ''; // Empty means just "Administration"

//Version Checking
if (cot::$cfg['check_updates'] && cot::$cache) {
	$updateInfo = cot::$cache->db->get('update_info');
	if (empty($updateInfo)) {
        $url = 'https://www.cotonti.com/?r=updatecheck';
        // $url = 'https://www.cotonti.com/update-check';
        $userAgent = 'Cotonti v.' . cot::$cfg['version'];
		if (ini_get('allow_url_fopen')) {
            $updateInfo = @file_get_contents($url, false, stream_context_create([
                    'http' => ['method'=>"GET", 'header' => 'User-Agent: ' . $userAgent]
                ])
            );
		} elseif (function_exists('curl_init')) {
			$curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            $updateInfo = curl_exec($curl);
			curl_close($curl);
		}

        if ($updateInfo) {
            $updateInfo = json_decode($updateInfo, TRUE);
        }
        if (empty($updateInfo)) {
            // Negative result should be cached too
            $updateInfo = 'a';
        }
        cot::$cache->db->store('update_info', $updateInfo, COT_DEFAULT_REALM, 86400);
	}
	if (
        !empty($updateInfo) &&
        $updateInfo != 'a' &&
        version_compare($updateInfo['update_ver'], cot::$cfg['version'], '>')
    ) {
		$t->assign(array(
			'ADMIN_HOME_UPDATE_REVISION' => sprintf(
                cot::$L['home_update_revision'],
                cot::$cfg['version'],
                htmlspecialchars($updateInfo['update_ver'])
            ),
			'ADMIN_HOME_UPDATE_MESSAGE' => cot_parse($updateInfo['update_message']),
		));
		$t->parse('MAIN.UPDATE');
	}
}

$sql = cot::$db->query("SHOW TABLES");
foreach ($sql->fetchAll(PDO::FETCH_NUM) as $row) {
	$table_name = $row[0];
	$status = cot::$db->query("SHOW TABLE STATUS LIKE '$table_name'");
	$status1 = $status->fetch();
	$status->closeCursor();
	$tables[] = $status1;
}

$total_length = 0;
$total_rows = 0;
$total_index_length = 0;
$total_data_length = 0;
foreach ($tables as $dat) {
	$table_length = $dat['Index_length'] + $dat['Data_length'];
	$total_length += $table_length;
	$total_rows += $dat['Rows'];
	$total_index_length += $dat['Index_length'];
	$total_data_length += $dat['Data_length'];
}

$totalplugins = cot::$db->query("SELECT DISTINCT(pl_code) FROM $db_plugins WHERE 1 GROUP BY pl_code")->rowCount();
$totalhooks = cot::$db->query("SELECT COUNT(*) FROM $db_plugins")->fetchColumn();

$t->assign(array(
	'ADMIN_HOME_DB_TOTAL_ROWS' => $total_rows,
	'ADMIN_HOME_DB_INDEXSIZE' => number_format(($total_index_length / 1024), 1, '.', ' '),
	'ADMIN_HOME_DB_DATASSIZE' => number_format(($total_data_length / 1024), 1, '.', ' '),
	'ADMIN_HOME_DB_TOTALSIZE' => number_format(($total_length / 1024), 1, '.', ' '),
	'ADMIN_HOME_TOTALPLUGINS' => $totalplugins,
	'ADMIN_HOME_TOTALHOOKS' => $totalhooks,
	'ADMIN_HOME_VERSION' => cot::$cfg['version'],
	'ADMIN_HOME_DB_VERSION' => htmlspecialchars(cot::$db->query("SELECT upd_value FROM $db_updates WHERE upd_param = 'revision'")->fetchColumn())
));

/* === Hook === */
foreach (cot_getextplugins('admin.home.mainpanel', 'R') as $pl) {
	$line = '';
	include $pl;
	if (!empty($line)) {
		$t->assign('ADMIN_HOME_MAINPANEL', $line);
		$t->parse('MAIN.MAINPANEL');
	}
}
/* ===== */

/* === Hook === */
foreach (cot_getextplugins('admin.home.sidepanel', 'R') as $pl) {
	$line = '';
	include $pl;
	if (!empty($line)) {
		$t->assign('ADMIN_HOME_SIDEPANEL', $line);
		$t->parse('MAIN.SIDEPANEL');
	}
}
/* ===== */

/* === Hook === */
foreach (cot_getextplugins('admin.home', 'R') as $pl) {
	include $pl;
}
/* ===== */

cot_display_messages($t);

$t->parse('MAIN');
$adminmain = $t->text('MAIN');
