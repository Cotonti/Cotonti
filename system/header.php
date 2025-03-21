<?php
/**
 * Global header
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

cot_uriredir_store();

/* === Hook === */
foreach (cot_getextplugins('header.first') as $pl) {
	include $pl;
}
/* ===== */

$contentTypeSent = '';
foreach (headers_list() as $header) {
    $header = mb_strtolower($header);
    if (mb_strpos($header, 'content-type') !== false) {
        $parts = explode(';', $header);
        $parts = array_map('trim', $parts);
        if (!empty($parts[0])) {
            $contentTypeParts = explode(':', $parts[0]);
            $contentTypeSent = trim($contentTypeParts[1]);
        }
        unset($parts, $contentTypeParts);
        break;
    }
}

if (empty(Cot::$out['contentType'])) {
    if ($contentTypeSent !== '') {
        Cot::$out['contentType'] = $contentTypeSent;
    } else {
        Cot::$out['contentType'] = Cot::$cfg['xmlclient'] ? 'application/xml' : 'text/html';
    }
}

if (!headers_sent()) {
    cot_sendheaders(
        Cot::$out['contentType'] !== $contentTypeSent ? Cot::$out['contentType'] : '',
        !empty(Cot::$env['status']) ? Cot::$env['status'] : '200 OK',
        !empty(Cot::$env['last_modified']) ? Cot::$env['last_modified'] : 0
    );
}

if (Cot::$sys['displayHeader']) {
    Cot::$out['logstatus'] = (Cot::$usr['id'] > 0)
        ? Cot::$L['hea_youareloggedas'] . ' ' . Cot::$usr['name']
        : Cot::$L['hea_youarenotlogged'];
    Cot::$out['userlist'] = (cot_auth('users', 'a', 'R'))
        ? cot_rc_link(cot_url('users'), Cot::$L['Users'])
        : '';

    unset($title_tags, $title_data);

    Cot::$out['subtitle'] = isset(Cot::$out['subtitle']) ? Cot::$out['subtitle'] : '';
    Cot::$out['head'] = isset(Cot::$out['head']) ? Cot::$out['head'] : '';
    Cot::$out['head_head'] = isset(Cot::$out['head_head']) ? Cot::$out['head_head'] : '';

    $title_page_num = '';
    if (!empty($pg) && is_numeric($pg) && $pg > 1) {
        // Appending page number to subtitle and meta description
        $title_page_num = htmlspecialchars(cot_rc('code_title_page_num', ['num' => $pg]));
        Cot::$out['subtitle'] .= $title_page_num;
    }

    $title_params = [
        'MAINTITLE' => Cot::$cfg['maintitle'],
        'DESCRIPTION' => Cot::$cfg['subtitle'],
        'SUBTITLE' => Cot::$out['subtitle'],
    ];
    if (defined('COT_INDEX')) {
        Cot::$out['fulltitle'] = cot_title('title_header_index', $title_params);

    } else {
        Cot::$out['fulltitle'] = cot_title('title_header', $title_params);
    }

    if (Cot::$cfg['jquery'] && Cot::$cfg['jquery_cdn']) {
        Resources::linkFile(Cot::$cfg['jquery_cdn'], 'js', 30);
    }

    $html = Resources::render();
    if ($html) {
        Cot::$out['head_head'] = $html . Cot::$out['head_head'];
    }

    Cot::$out['basehref'] = Cot::$R['code_basehref'];
    Cot::$out['meta_charset'] = 'UTF-8';
    Cot::$out['meta_desc'] = (empty(Cot::$out['desc'])
            ? Cot::$cfg['subtitle']
            : htmlspecialchars(Cot::$out['desc'])) . $title_page_num;
    Cot::$out['meta_keywords'] = empty(Cot::$out['keywords'])
        ? Cot::$cfg['metakeywords']
        : htmlspecialchars(Cot::$out['keywords']);
    Cot::$out['meta_lastmod'] = gmdate('D, d M Y H:i:s');
    Cot::$out['head_head'] .= Cot::$out['head'];

    Cot::$out['canonical_uri'] = !empty(Cot::$out['canonical_uri']) ? Cot::$out['canonical_uri'] : '';

    if (!empty(Cot::$out['canonical_uri']) && !preg_match("#^https?://.+#", Cot::$out['canonical_uri'])) {
        Cot::$out['canonical_uri'] = rtrim(COT_ABSOLUTE_URL, '/') . '/'
            . (
                (Cot::$out['canonical_uri'] !== '/') ? ltrim(Cot::$out['canonical_uri'], '/') : ''
            );
    }

    if (Cot::$out['canonical_uri'] !== '') {
        header('Link: <' . str_replace('&amp;', '&', Cot::$out['canonical_uri'])
            . '>; rel="canonical"');
    }

    if (!empty(Cot::$sys['noindex'])) {
        Cot::$out['head_head'] .= Cot::$R['code_noindex'];
    }

    $mtpl_type = defined('COT_ADMIN') || defined('COT_MESSAGE') && $_SESSION['s_run_admin'] && cot_auth('admin', 'any', 'R') ? 'core' : 'module';
	if (Cot::$cfg['enablecustomhf']) {
		$mtpl_base = (defined('COT_PLUG') && !empty($e)) ? ['header', $e] : ['header', Cot::$env['location']];
	} else {
		$mtpl_base = 'header';
	}

	$t = new XTemplate(cot_tplfile($mtpl_base, $mtpl_type));

	/* === Hook === */
	foreach (cot_getextplugins('header.main') as $pl) {
		include $pl;
	}
	/* ===== */

	if (empty(Cot::$out['notices'])) {
        Cot::$out['notices'] = '';
    }
	if (!empty(Cot::$out['notices_array']) && is_array(Cot::$out['notices_array'])) {
		$notices = '';
		foreach (Cot::$out['notices_array'] as $noticeRow) {
			$notice = (is_array($noticeRow)) ? cot_rc('notices_link', ['url' => $noticeRow[0], 'title' => $noticeRow[1]]) :
				cot_rc('notices_plain', ['title' => $noticeRow]);
			$notices .= cot_rc('notices_notice', ['notice' => $notice]);

		}
		Cot::$out['notices'] .= cot_rc('notices_container', ['notices' => $notices]);
	}

	$t->assign([
		'HEADER_TITLE' => Cot::$out['fulltitle'],
		'HEADER_COMPOPUP' => !empty(Cot::$out['compopup']) ? Cot::$out['compopup'] : '',
		'HEADER_LOGSTATUS' => Cot::$out['logstatus'],
		'HEADER_TOPLINE' => Cot::$cfg['topline'],
		'HEADER_BANNER' => Cot::$cfg['banner'],
		'HEADER_GMTTIME' => Cot::$usr['gmttime'],
		'HEADER_USERLIST' => Cot::$out['userlist'],
		'HEADER_NOTICES' => Cot::$out['notices'],
		'HEADER_NOTICES_ARRAY' => !empty(Cot::$out['notices_array']) ? Cot::$out['notices_array'] : [],
		'HEADER_BASEHREF' => Cot::$out['basehref'],
		'HEADER_META_CONTENTTYPE' => Cot::$out['contentType'],
		'HEADER_META_CHARSET' => Cot::$out['meta_charset'],
		'HEADER_META_DESCRIPTION' => Cot::$out['meta_desc'],
		'HEADER_META_KEYWORDS' => Cot::$out['meta_keywords'],
		'HEADER_META_LASTMODIFIED' => Cot::$out['meta_lastmod'],
		'HEADER_HEAD' => Cot::$out['head_head'],
		'HEADER_CANONICAL_URL' => !empty(Cot::$out['canonical_uri']) ? Cot::$out['canonical_uri'] : '',
		'HEADER_PREV_URL' => !empty(Cot::$out['prev_uri']) ? Cot::$out['prev_uri'] : '',
		'HEADER_NEXT_URL' => !empty(Cot::$out['next_uri']) ? Cot::$out['next_uri'] : '',
		'HEADER_COLOR_SCHEME' => cot_schemeFile(),
	]);

	/* === Hook === */
	foreach (cot_getextplugins('header.body') as $pl) {
		include $pl;
	}
	/* ===== */

	if (Cot::$usr['id'] > 0) {
		Cot::$out['adminpanel'] = (cot_auth('admin', 'any', 'R')) ?
            cot_rc_link(cot_url('admin'), Cot::$L['Administration']) : '';
		Cot::$out['loginout_url'] = cot_url('login', 'out=1&' . cot_xg());
		Cot::$out['loginout'] = cot_rc_link(Cot::$out['loginout_url'], Cot::$L['Logout']);
		Cot::$out['profile'] = cot_module_active('users')
            ? cot_rc_link(cot_url('users', ['m' => 'profile']), Cot::$L['users_profileSettings'])
            : null;

		$t->assign([
			'HEADER_USER_NAME' => Cot::$usr['name'],
			'HEADER_USER_ADMINPANEL' => Cot::$out['adminpanel'],
			'HEADER_USER_ADMINPANEL_URL' => cot_url('admin'),
			'HEADER_USER_LOGINOUT' => Cot::$out['loginout'],
			'HEADER_USER_LOGINOUT_URL' => Cot::$out['loginout_url'],
			'HEADER_USER_PROFILE' => Cot::$out['profile'],
			'HEADER_USER_PROFILE_URL' => cot_url('users', 'm=profile'),
			'HEADER_USER_MESSAGES' => Cot::$usr['messages'],
		]);

		/* === Hook === */
		foreach (cot_getextplugins('header.user.tags') as $pl) {
			include $pl;
		}
		/* ===== */

		$t->parse('HEADER.USER');
	} else {
		Cot::$out['guest_username'] = Cot::$R['form_guest_username'];
		Cot::$out['guest_password'] = Cot::$R['form_guest_password'];
		Cot::$out['guest_register'] = cot_rc_link(cot_url('users', 'm=register'), Cot::$L['Register']);
		Cot::$out['guest_cookiettl'] = Cot::$cfg['forcerememberme'] ? Cot::$R['form_guest_remember_forced']
			: Cot::$R['form_guest_remember'];

		$t->assign([
			'HEADER_GUEST_SEND' => cot_url('login', 'a=check&' . Cot::$sys['url_redirect']),
			'HEADER_GUEST_USERNAME' => Cot::$out['guest_username'],
			'HEADER_GUEST_PASSWORD' => Cot::$out['guest_password'],
			'HEADER_GUEST_REGISTER' => Cot::$out['guest_register'],
			'HEADER_GUEST_REGISTER_URL' => cot_url('users', 'm=register'),
			'HEADER_GUEST_COOKIETTL' => Cot::$out['guest_cookiettl'],
		]);

		/* === Hook === */
		foreach (cot_getextplugins('header.guest.tags') as $pl) {
			include $pl;
		}
		/* ===== */

		$t->parse('HEADER.GUEST');
	}

	/* === Hook === */
	foreach (cot_getextplugins('header.tags') as $pl) {
		include $pl;
	}
	/* ===== */

	$t->parse('HEADER');
	$t->out('HEADER');
}

/* === Hook === */
foreach (cot_getextplugins('header.last') as $pl) {
    include $pl;
}
/* ===== */

define('COT_HEADER_COMPLETE', TRUE);
