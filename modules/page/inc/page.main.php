<?php
/**
 * Page display.
 *
 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('page', 'any');
cot_block(cot::$usr['auth_read']);

$id = cot_import('id', 'G', 'INT');
$al = cot::$db->prep(cot_import('al', 'G', 'TXT'));
$c = cot_import('c', 'G', 'TXT');
$pg = cot_import('pg', 'G', 'INT');

$join_columns = isset($join_columns) ? $join_columns : '';
$join_condition = isset($join_condition) ? $join_condition : '';

/* === Hook === */
foreach (cot_getextplugins('page.first') as $pl) {
	include $pl;
}
/* ===== */

if ($id > 0 || !empty($al)) {
	$where = (!empty($al)) ? "p.page_alias='".$al."'" : 'p.page_id='.$id;
	if (!empty($c)) {
        $where .= " AND p.page_cat = " . cot::$db->quote($c);
    }
	$sql_page = cot::$db->query("SELECT p.*, u.* $join_columns
		FROM $db_pages AS p $join_condition
		LEFT JOIN $db_users AS u ON u.user_id=p.page_ownerid
		WHERE $where LIMIT 1");
}

if (!$id && empty($al) || !$sql_page || $sql_page->rowCount() == 0) {
	cot_die_message(404);
}
$pag = $sql_page->fetch();

list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin'], cot::$usr['auth_download']) = cot_auth('page', $pag['page_cat'], 'RWA1');
cot_block(cot::$usr['auth_read']);

$al = empty($pag['page_alias']) ? '' : $pag['page_alias'];
$id = (int) $pag['page_id'];
$cat = cot::$structure['page'][$pag['page_cat']];

$sys['sublocation'] = $pag['page_title'];

$pag['page_begin_noformat'] = $pag['page_begin'];
$pag['page_tab'] = empty($pg) ? 0 : $pg;

$urlParams = ['c' => $pag['page_cat']];
if (!empty($al)) {
    $urlParams['al'] = $al;
} else {
    $urlParams['id'] = $id;
}
$pag['page_pageurl'] = cot_url('page', $urlParams, '', true);

if (
    (
        $pag['page_state'] == COT_PAGE_STATE_PENDING
	    || $pag['page_state'] == COT_PAGE_STATE_DRAFT
	    || $pag['page_begin'] > cot::$sys['now']
	    || ($pag['page_expire'] > 0 && cot::$sys['now'] > $pag['page_expire'])
    )
	&& (!cot::$usr['isadmin'] && cot::$usr['id'] != $pag['page_ownerid'])
) {
	cot_log("Attempt to directly access an un-validated or future/expired page", 'sec');
	cot_die_message(403, TRUE);
}
if (mb_substr($pag['page_text'], 0, 6) == 'redir:') {
	$env['status'] = '303 See Other';
	$redir = trim(str_replace('redir:', '', $pag['page_text']));
	$sql_page_update = cot::$db->query("UPDATE $db_pages SET page_filecount=page_filecount+1 WHERE page_id=$id");
	header('Location: ' . (preg_match('#^(http|ftp)s?://#', $redir) ? '' : COT_ABSOLUTE_URL) . $redir);
	exit;
} elseif (mb_substr($pag['page_text'], 0, 8) == 'include:') {
	$pag['page_text'] = cot_readraw('datas/html/'.trim(mb_substr($pag['page_text'], 8, 255)));
}
if ($pag['page_file'] && $a == 'dl' && (($pag['page_file'] == 2 && cot::$usr['auth_download']) || $pag['page_file'] == 1)) {
	/* === Hook === */
	foreach (cot_getextplugins('page.download.first') as $pl) {
		include $pl;
	}
	/* ===== */

	// Hotlinking protection
	if (
        isset($_SESSION['dl']) &&
        $_SESSION['dl'] != $id &&
        isset($_SESSION['cat']) &&
        $_SESSION['cat'] != $pag['page_cat']
    ) {
		cot_redirect($pag['page_pageurl']);
	}

	unset($_SESSION['dl']);

	if (!cot::$usr['isadmin'] || cot::$cfg['page']['count_admin']) {
		$pag['page_filecount']++;
		$sql_page_update = cot::$db->query("UPDATE $db_pages SET page_filecount=page_filecount+1 WHERE page_id=".$id);
	}
	$redir = (preg_match('#^(http|ftp)s?://#', $pag['page_url']) ? '' : COT_ABSOLUTE_URL) . $pag['page_url'];
	header('Location: ' . $redir);
	echo cot_rc('page_code_redir');
	exit;
}
if (!cot::$usr['isadmin'] || cot::$cfg['page']['count_admin']) {
	$pag['page_count']++;
	$sql_page_update =  cot::$db->query("UPDATE $db_pages SET page_count='".$pag['page_count']."' WHERE page_id=$id");
}

if ($pag['page_cat'] == 'system') {
    cot::$out['subtitle'] = empty($pag['page_metatitle']) ? $pag['page_title'] : $pag['page_metatitle'];
} else {
	$title_params = array(
		'TITLE' => empty($pag['page_metatitle']) ? $pag['page_title'] : $pag['page_metatitle'],
		'CATEGORY' => $cat['title']
	);
    cot::$out['subtitle'] = cot_title(cot::$cfg['page']['title_page'], $title_params);
}
cot::$out['desc'] = empty($pag['page_metadesc']) ? strip_tags($pag['page_desc']) : strip_tags($pag['page_metadesc']);
cot::$out['keywords'] = !empty($pag['page_keywords']) ? strip_tags($pag['page_keywords']) : '';

// Building the canonical URL
$pageurl_params = array('c' => $pag['page_cat']);
empty($al) ? $pageurl_params['id'] = $id : $pageurl_params['al'] = $al;
if ($pg > 0) {
	$pageurl_params['pg'] = $pg;
}
cot::$out['canonical_uri'] = cot_url('page', $pageurl_params);

$mskin = cot_tplfile(array('page', $cat['tpl']));

cot::$env['last_modified'] = $pag['page_updated'];

/* === Hook === */
foreach (cot_getextplugins('page.main') as $pl)
{
	include $pl;
}
/* ===== */

if ($pag['page_file'])
{
	unset($_SESSION['dl']);
	$_SESSION['dl'] = $id;
}

require_once cot::$cfg['system_dir'] . '/header.php';
require_once cot_incfile('users', 'module');
$t = new XTemplate($mskin);

$t->assign(
    cot_generate_pagetags(
        $pag,
        'PAGE_',
        0,
        cot::$usr['isadmin'],
        cot::$cfg['homebreadcrumb'],
        '',
        $pag['page_pageurl']
    )
);
$t->assign('PAGE_OWNER', cot_build_user($pag['page_ownerid'], $pag['user_name']));
$t->assign(cot_generate_usertags($pag, 'PAGE_OWNER_'));

$pag['page_file'] = intval($pag['page_file']);
if ($pag['page_file'] > 0) {
	if (cot::$sys['now'] > $pag['page_begin']) {
		if (!empty($pag['page_url'])) {
			$dotpos = mb_strrpos($pag['page_url'], ".") + 1;
			$type = mb_strtolower(mb_substr($pag['page_url'], $dotpos, 5));
			$pag['page_fileicon'] = cot_rc('page_icon_file_path');
			if (!file_exists($pag['page_fileicon']))
			{
				$pag['page_fileicon'] = cot_rc('page_icon_file_default');
			}
			$pag['page_fileicon'] = cot_rc('page_icon_file', array('icon' => $pag['page_fileicon']));
		} else {
			$pag['page_fileicon'] = '';
		}

		$t->assign(array(
			'PAGE_FILE_SIZE' => $pag['page_size'] / 1024, // in KiB; deprecated but kept for compatibility
			'PAGE_FILE_SIZE_BYTES' => $pag['page_size'],
			'PAGE_FILE_SIZE_READABLE' => cot_build_filesize($pag['page_size'], 1),
			'PAGE_FILE_COUNT' => $pag['page_filecount'],
			'PAGE_FILE_ICON' => $pag['page_fileicon'],
			'PAGE_FILE_NAME' => basename($pag['page_url']),
			'PAGE_FILE_COUNTTIMES' => cot_declension($pag['page_filecount'], $Ls['Times'])
		));

		if (($pag['page_file'] === 2 && cot::$usr['id'] == 0) || ($pag['page_file'] === 2 && !cot::$usr['auth_download']))
		{
			$t->assign(array(
				'PAGE_FILETITLE' => cot::$L['Members_download'],
				'PAGE_FILE_URL' => cot_url('users', 'm=register')
			));
		}
		else
		{
			$t->assign(array(
				'PAGE_FILETITLE' => $pag['page_title'],
				'PAGE_FILE_URL' => cot_url('page', array('c' => $pag['page_cat'], 'id' => $id, 'a' => 'dl'))
			));
		}
	}
}

// Multi tabs
$pag['page_tabs'] = explode('[newpage]', $t->vars['PAGE_TEXT'], 99);
$pag['page_totaltabs'] = count($pag['page_tabs']);

if ($pag['page_totaltabs'] > 1)
{
	if (empty($pag['page_tabs'][0]))
	{
		$remove = array_shift($pag['page_tabs']);
		$pag['page_totaltabs']--;
	}
	$max_tab = $pag['page_totaltabs'] - 1;
	$pag['page_tab'] = ($pag['page_tab'] > $max_tab) ? 0 : $pag['page_tab'];
	$pag['page_tabtitles'] = array();

	for ($i = 0; $i < $pag['page_totaltabs']; $i++)
	{
		if (mb_strpos($pag['page_tabs'][$i], '<br />') === 0)
		{
			$pag['page_tabs'][$i] = mb_substr($pag['page_tabs'][$i], 6);
		}

		$p1 = mb_strpos($pag['page_tabs'][$i], '[title]');
		$p2 = mb_strpos($pag['page_tabs'][$i], '[/title]');

		if ($p2 > $p1 && $p1 < 4)
		{
			$pag['page_tabtitle'][$i] = mb_substr($pag['page_tabs'][$i], $p1 + 7, ($p2 - $p1) - 7);
			if ($i == $pag['page_tab'])
			{
				$pag['page_tabs'][$i] = trim(str_replace('[title]'.$pag['page_tabtitle'][$i].'[/title]', '', $pag['page_tabs'][$i]));
			}
		}
		else
		{
			$pag['page_tabtitle'][$i] = $i == 0 ? $pag['page_title'] : $L['Page'] . ' ' . ($i + 1);
		}
		$tab_url = empty($al) ? cot_url('page', 'c='.$pag['page_cat'].'&id='.$id.'&pg='.$i) : cot_url('page', 'c='.$pag['page_cat'].'&al='.$al.'&pg='.$i);
		$pag['page_tabtitles'][] .= cot_rc_link($tab_url, ($i+1).'. '.$pag['page_tabtitle'][$i],
			array('class' => 'page_tabtitle'));
		$pag['page_tabs'][$i] = str_replace('[newpage]', '', $pag['page_tabs'][$i]);
		$pag['page_tabs'][$i] = preg_replace('#^(<br />)+#', '', $pag['page_tabs'][$i]);
		$pag['page_tabs'][$i] = trim($pag['page_tabs'][$i]);
	}

	$pag['page_tabtitles'] = implode('<br />', $pag['page_tabtitles']);
	$pag['page_text'] = $pag['page_tabs'][$pag['page_tab']];

	// Temporarily disable easypagenav to allow 0-based numbers
	$tmp = cot::$cfg['easypagenav'];
	cot::$cfg['easypagenav'] = false;
	$pn = cot_pagenav('page', (empty($al) ? 'id='.$id : 'al='.$al), $pag['page_tab'], $pag['page_totaltabs'], 1, 'pg');
	$pag['page_tabnav'] = $pn['main'];
	cot::$cfg['easypagenav'] = $tmp;

	$t->assign(array(
		'PAGE_MULTI_TABNAV' => $pag['page_tabnav'],
		'PAGE_MULTI_TABTITLES' => $pag['page_tabtitles'],
		'PAGE_MULTI_CURTAB' => $pag['page_tab'] + 1,
		'PAGE_MULTI_MAXTAB' => $pag['page_totaltabs'],
		'PAGE_TEXT' => $pag['page_text']
	));
	$t->parse('MAIN.PAGE_MULTI');
}

// Error and message handling
cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('page.tags') as $pl) {
	include $pl;
}
/* ===== */
if (cot::$usr['isadmin'] || cot::$usr['id'] == $pag['page_ownerid']) {
	$t->parse('MAIN.PAGE_ADMIN');
}

if (($pag['page_file'] === 2 && cot::$usr['id'] == 0) || ($pag['page_file'] === 2 && !cot::$usr['auth_download'])) {
	$t->parse('MAIN.PAGE_FILE.MEMBERSONLY');
} else {
	$t->parse('MAIN.PAGE_FILE.DOWNLOAD');
}
if (!empty($pag['page_url'])) {
	$t->parse('MAIN.PAGE_FILE');
}
$t->parse('MAIN');
$t->out('MAIN');

require_once cot::$cfg['system_dir'] . '/footer.php';

if (
    cot::$cache && cot::$usr['id'] === 0 && cot::$cfg['cache_page']
	&& (!isset(cot::$cfg['cache_page_blacklist']) || !in_array($pag['page_cat'], cot::$cfg['cache_page_blacklist']))
) {
	cot::$cache->page->write();
}
