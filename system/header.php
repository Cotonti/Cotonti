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
foreach (cot_getextplugins('header.first') as $pl)
{
	include $pl;
}
/* ===== */
cot::$out['logstatus'] = (cot::$usr['id'] > 0) ? cot::$L['hea_youareloggedas'].' '.cot::$usr['name'] : cot::$L['hea_youarenotlogged'];
cot::$out['userlist'] = (cot_auth('users', 'a', 'R')) ? cot_rc_link(cot_url('users'), cot::$L['Users']) : '';

unset($title_tags, $title_data);

if(!isset(cot::$out['subtitle'])) cot::$out['subtitle'] = '';
if(!isset(cot::$out['head'])) cot::$out['head'] = '';
if(!isset(cot::$out['head_head'])) cot::$out['head_head'] = '';
$title_page_num = '';
if (!empty($pg) && is_numeric($pg) && $pg > 1) {
	// Appending page number to subtitle and meta description
	$title_page_num = htmlspecialchars(cot_rc('code_title_page_num', array('num' => $pg)));
	cot::$out['subtitle'] .= $title_page_num;
}

$title_params = array(
	'MAINTITLE' => cot::$cfg['maintitle'],
	'DESCRIPTION' => cot::$cfg['subtitle'],
	'SUBTITLE' => cot::$out['subtitle']
);
if (defined('COT_INDEX')) {
	cot::$out['fulltitle'] = cot_title('title_header_index', $title_params);

} else {
	cot::$out['fulltitle'] = cot_title('title_header', $title_params);
}

if (cot::$cfg['jquery'] && cot::$cfg['jquery_cdn']) {
	Resources::linkFile(cot::$cfg['jquery_cdn'], 'js', 30);
}
$html = Resources::render();
if($html) cot::$out['head_head'] = $html.cot::$out['head_head'];

cot::$out['meta_contenttype'] = cot::$cfg['xmlclient'] ? 'application/xml' : 'text/html';
cot::$out['basehref'] = cot::$R['code_basehref'];
cot::$out['meta_charset'] = 'UTF-8';
cot::$out['meta_desc'] = (empty(cot::$out['desc']) ? cot::$cfg['subtitle'] : htmlspecialchars(cot::$out['desc'])) . $title_page_num;
cot::$out['meta_keywords'] = empty(cot::$out['keywords']) ? cot::$cfg['metakeywords'] : htmlspecialchars(cot::$out['keywords']);
cot::$out['meta_lastmod'] = gmdate('D, d M Y H:i:s');
cot::$out['head_head'] .= cot::$out['head'];

if (!empty(cot::$sys['noindex'])) {
	cot::$out['head_head'] .= cot::$R['code_noindex'];
}
if(!headers_sent()) {
    $lastModified = !empty(cot::$env['last_modified']) ? cot::$env['last_modified'] : 0;
	cot_sendheaders(cot::$out['meta_contenttype'], isset(cot::$env['status']) ? cot::$env['status'] : '200 OK', $lastModified);
}
if (!COT_AJAX) {
	$mtpl_type = defined('COT_ADMIN') || defined('COT_MESSAGE') && $_SESSION['s_run_admin'] && cot_auth('admin', 'any', 'R') ? 'core' : 'module';
	if (cot::$cfg['enablecustomhf']) {
		$mtpl_base = (defined('COT_PLUG') && !empty($e)) ? array('header', $e) : array('header', cot::$env['location']);

	} else {
		$mtpl_base = 'header';
	}
	$t = new XTemplate(cot_tplfile($mtpl_base, $mtpl_type));

	/* === Hook === */
	foreach (cot_getextplugins('header.main') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if(empty(cot::$out['notices'])) cot::$out['notices'] = '';
	if(!empty(cot::$out['notices_array']) && is_array(cot::$out['notices_array'])) {
		$notices = '';
		foreach (cot::$out['notices_array'] as $noticeRow) {
			$notice = (is_array($noticeRow)) ? cot_rc('notices_link', array('url' => $noticeRow[0], 'title' => $noticeRow[1])) :
				cot_rc('notices_plain', array('title' => $noticeRow));
			$notices .= cot_rc('notices_notice', array('notice' => $notice));

		}
		cot::$out['notices'] .= cot_rc('notices_container', array('notices' => $notices));
	}
	cot::$out['canonical_uri'] = empty(cot::$out['canonical_uri']) ? str_replace('&', '&amp;', cot::$sys['canonical_url']) : cot::$out['canonical_uri'];
	if(!preg_match("#^https?://.+#", cot::$out['canonical_uri']))
	{
		cot::$out['canonical_uri'] = COT_ABSOLUTE_URL . cot::$out['canonical_uri'];
	}

	$t->assign(array(
		'HEADER_TITLE' => cot::$out['fulltitle'],
		'HEADER_COMPOPUP' => !empty(cot::$out['compopup']) ? cot::$out['compopup'] : '',
		'HEADER_LOGSTATUS' => cot::$out['logstatus'],
		'HEADER_TOPLINE' => cot::$cfg['topline'],
		'HEADER_BANNER' => cot::$cfg['banner'],
		'HEADER_GMTTIME' => cot::$usr['gmttime'],
		'HEADER_USERLIST' => cot::$out['userlist'],
		'HEADER_NOTICES' => cot::$out['notices'],
		'HEADER_NOTICES_ARRAY' => !empty(cot::$out['notices_array']) ? cot::$out['notices_array'] : array(),
		'HEADER_BASEHREF' => cot::$out['basehref'],
		'HEADER_META_CONTENTTYPE' => cot::$out['meta_contenttype'],
		'HEADER_META_CHARSET' => cot::$out['meta_charset'],
		'HEADER_META_DESCRIPTION' => cot::$out['meta_desc'],
		'HEADER_META_KEYWORDS' => cot::$out['meta_keywords'],
		'HEADER_META_LASTMODIFIED' => cot::$out['meta_lastmod'],
		'HEADER_HEAD' => cot::$out['head_head'],
		'HEADER_CANONICAL_URL' => cot::$out['canonical_uri'],
		'HEADER_PREV_URL' => !empty(cot::$out['prev_uri']) ? cot::$out['prev_uri'] : '',
		'HEADER_NEXT_URL' => !empty(cot::$out['next_uri']) ? cot::$out['next_uri'] : '',
		'HEADER_COLOR_SCHEME' => cot_schemefile()
	));

	/* === Hook === */
	foreach (cot_getextplugins('header.body') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (cot::$usr['id'] > 0)
	{
		cot::$out['adminpanel'] = (cot_auth('admin', 'any', 'R')) ? cot_rc_link(cot_url('admin'), cot::$L['Administration']) : '';
		cot::$out['loginout_url'] = cot_url('login', 'out=1&' . cot_xg());
		cot::$out['loginout'] = cot_rc_link(cot::$out['loginout_url'], cot::$L['Logout']);
		cot::$out['profile'] = cot_rc_link(cot_url('users', 'm=profile'), cot::$L['Profile']);

		$t->assign(array(
			'HEADER_USER_NAME' => cot::$usr['name'],
			'HEADER_USER_ADMINPANEL' => cot::$out['adminpanel'],
			'HEADER_USER_ADMINPANEL_URL' => cot_url('admin'),
			'HEADER_USER_LOGINOUT' => cot::$out['loginout'],
			'HEADER_USER_LOGINOUT_URL' => cot::$out['loginout_url'],
			'HEADER_USER_PROFILE' => cot::$out['profile'],
			'HEADER_USER_PROFILE_URL' => cot_url('users', 'm=profile'),
			'HEADER_USER_MESSAGES' => cot::$usr['messages']
		));

		/* === Hook === */
		foreach (cot_getextplugins('header.user.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse('HEADER.USER');
	}
	else
	{
		cot::$out['guest_username'] = cot::$R['form_guest_username'];
		cot::$out['guest_password'] = cot::$R['form_guest_password'];
		cot::$out['guest_register'] = cot_rc_link(cot_url('users', 'm=register'), cot::$L['Register']);
		cot::$out['guest_cookiettl'] = cot::$cfg['forcerememberme'] ? cot::$R['form_guest_remember_forced']
			: cot::$R['form_guest_remember'];

		$t->assign(array (
			'HEADER_GUEST_SEND' => cot_url('login', 'a=check&' . cot::$sys['url_redirect']),
			'HEADER_GUEST_USERNAME' => cot::$out['guest_username'],
			'HEADER_GUEST_PASSWORD' => cot::$out['guest_password'],
			'HEADER_GUEST_REGISTER' => cot::$out['guest_register'],
			'HEADER_GUEST_REGISTER_URL' => cot_url('users', 'm=register'),
			'HEADER_GUEST_COOKIETTL' => cot::$out['guest_cookiettl']
		));

		/* === Hook === */
		foreach (cot_getextplugins('header.guest.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse('HEADER.GUEST');
	}

	/* === Hook === */
	foreach (cot_getextplugins('header.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('HEADER');
	$t->out('HEADER');
}
define('COT_HEADER_COMPLETE', TRUE);
