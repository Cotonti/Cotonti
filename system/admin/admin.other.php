<?php
/**
 * Administration panel - Other Admin parts listing
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$t = new XTemplate(cot_tplfile('admin.other', 'core'));

$p = cot_import('p', 'G', 'ALP');

/* === Hook === */
foreach (cot_getextplugins('admin.other.first') as $pl) {
	include $pl;
}
/* ===== */

if (!empty($p)) {
    $extp = [];
    $hook = 'tools';
    if (!empty($cot_plugins[$hook]) && is_array($cot_plugins[$hook])) {
        if (Cot::$cfg['debug_mode']) {
            $cotHooksFired[] = $hook;
        }
        foreach ($cot_plugins[$hook] as $extensionRow) {
            if ($extensionRow['pl_code'] === $p) {
                $extp[] = $extensionRow;
            }
        }
    }

    if (count($extp) == 0) {
        cot_die_message(907, TRUE);
    }

	list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('plug', $p);
	cot_block(Cot::$usr['isadmin']);

    Cot::$env['ext'] = $p;

    if (file_exists(cot_langfile($p, 'plug'))) {
        require_once cot_langfile($p, 'plug');
    }

    $extInfo = cot_get_extensionparams($p, false);
    $adminTitle = $extInfo['name'];

    $adminPath = [
        [cot_url('admin', ['m' => 'extensions']), Cot::$L['Extensions']],
        [cot_url('admin', ['m' => 'extensions', 'a' => 'details', 'pl' => $p]), $adminTitle],
        [cot_url('admin', ['m' => 'other', 'p' => $p]), Cot::$L['Administration']],
    ];

	// $adminHelp = Cot::$L['Description'].' : '.$info['Description'].'<br />'.Cot::$L['Version'].' : '.$info['Version'].'<br />'.Cot::$L['Date'].' : '.$info['Date'].'<br />'.Cot::$L['Author'].' : '.$info['Author'].'<br />'.Cot::$L['Copyright'].' : '.$info['Copyright'].'<br />'.Cot::$L['Notes'].' : '.$info['Notes'];

    $adminMain = '';
    $legacyMode = isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode'];
    foreach ($extp as $k => $pl) {
        if ($legacyMode) {
            /** @deprecated in 0.9.25 */
            $plugin_body = '';
        }

        $pluginBody = '';
        include_once Cot::$cfg['plugins_dir'] . '/' . $pl['pl_file'];
        $adminMain .= $pluginBody;

        if ($legacyMode) {
            // @deprecated in 0.9.25
            $adminMain .= $plugin_body;
        }
    }
} else {
	$adminPath[] = [cot_url('admin', ['m' => 'other']), Cot::$L['Other']];
	$adminTitle = Cot::$L['Other'];
	list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('admin', 'a');
	cot_block(Cot::$usr['auth_read']);

	$target = [];

	function cot_admin_other_cmp($pl_a, $pl_b) {
		if($pl_a['pl_code'] == $pl_b['pl_code']) {
			return 0;
		}
		return ($pl_a['pl_code'] < $pl_b['pl_code']) ? -1 : 1;
	}

	foreach (['module', 'plug'] as $type) {
		if ($type === 'module') {
			$target = $cot_plugins['admin'];
			$title = Cot::$L['Modules'];
		} else {
			$target = $cot_plugins['tools'];
			$title = Cot::$L['Plugins'];
		}

		if (is_array($target)) {
			usort($target, 'cot_admin_other_cmp');
			foreach ($target as $pl) {
				$ext_info = cot_get_extensionparams($pl['pl_code'], $type == COT_EXT_TYPE_MODULE);
				$t->assign([
					'ADMIN_OTHER_EXT_URL' => $type == 'plug'
                        ? cot_url('admin', 'm=other&p=' . $pl['pl_code'])
                        : cot_url('admin', 'm=' . $pl['pl_code']),
					'ADMIN_OTHER_EXT_ICON' => $ext_info['icon'],
					'ADMIN_OTHER_EXT_NAME' => $ext_info['name'],
					'ADMIN_OTHER_EXT_DESC' => $ext_info['desc'],
				]);
                if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
                    $t->assign([
                        // @deprecated For backward compatibility. Will be removed in future releases
                        'ADMIN_OTHER_EXT_ICO' => $ext_info['legacyIcon'],
                    ]);
                }

				$t->parse('MAIN.SECTION.ROW');
			}
		} else {
			$t->parse('MAIN.SECTION.EMPTY');
		}
		$t->assign('ADMIN_OTHER_SECTION', $title);
		$t->parse('MAIN.SECTION');
	}

	$t->assign([
		'ADMIN_OTHER_URL_CACHE' => cot_url('admin', 'm=cache'),
		'ADMIN_OTHER_URL_DISKCACHE' => cot_url('admin', 'm=cache&s=disk'),
		'ADMIN_OTHER_URL_EXFLDS' => cot_url('admin', 'm=extrafields'),
		'ADMIN_OTHER_URL_STRUCTURE' => cot_url('admin', 'm=structure'),
		'ADMIN_OTHER_URL_LOG' => cot_url('admin', 'm=log'),
		'ADMIN_OTHER_URL_INFOS' => cot_url('admin', 'm=infos'),
        'ADMIN_OTHER_URL_PHPINFO' => cot_url('admin', 'm=phpinfo'),
	]);

	/* === Hook === */
	foreach (cot_getextplugins('admin.other.tags') as $pl) {
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN');
	$adminMain = $t->text('MAIN');
}
