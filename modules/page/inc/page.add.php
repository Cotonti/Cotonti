<?php
/**
 * Add page.
 *
 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forms');

$id = cot_import('id', 'G', 'INT');
$c = cot_import('c', 'G', 'TXT');

if (!empty($c) && !isset($structure['page'][$c]))
{
	$c = '';
}

list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('page', 'any');

/* === Hook === */
foreach (cot_getextplugins('page.add.first') as $pl)
{
	include $pl;
}
/* ===== */
cot_block(cot::$usr['auth_write']);

$sys['parser'] = cot::$cfg['page']['parser'];
$parser_list = cot_get_parsers();

if ($a == 'add')
{
	cot_shield_protect();

	/* === Hook === */
	foreach (cot_getextplugins('page.add.add.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$rpage = cot_page_import('POST', array(), cot::$usr);

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('page', $rpage['page_cat']);
	cot_block($usr['auth_write']);

	/* === Hook === */
	foreach (cot_getextplugins('page.add.add.import') as $pl)
	{
		include $pl;
	}
	/* ===== */

	cot_page_validate($rpage);

	/* === Hook === */
	foreach (cot_getextplugins('page.add.add.error') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!cot_error_found())
	{
		$id = cot_page_add($rpage, $usr);

		switch ($rpage['page_state'])
		{
			case 0:
				$urlparams = empty($rpage['page_alias']) ?
					array('c' => $rpage['page_cat'], 'id' => $id) :
					array('c' => $rpage['page_cat'], 'al' => $rpage['page_alias']);
				$r_url = cot_url('page', $urlparams, '', true);
				break;
			case 1:
				$r_url = cot_url('message', 'msg=300', '', true);
				break;
			case 2:
				cot_message('page_savedasdraft');
				$r_url = cot_url('page', 'm=edit&id='.$id, '', true);
				break;
		}
		cot_redirect($r_url);
	}
	else
	{
        $urlParams = array('m' => 'add');
	    if(!empty($c)) $urlParams['c'] = $c;
		cot_redirect(cot_url('page', $urlParams, '', true));
	}
}

// Page cloning support
$clone = cot_import('clone', 'G', 'INT');
$rpage = array();
if ($clone > 0)
{
	$rpage = cot::$db->query("SELECT * FROM ".cot::$db->pages." WHERE page_id = ?", $clone)->fetch();
}

if (empty($rpage['page_cat']) && !empty($c)) $rpage['page_cat'] = $c;

if (!empty($rpage['page_cat'])) {
    list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('page', $rpage['page_cat']);
    cot_block(cot::$usr['auth_write']);

    if (!cot::$usr['isadmin'] && cot::$structure['page'][$rpage['page_cat']]['locked']) cot_die_message(602, TRUE);

    cot::$sys['sublocation'] = cot::$structure['page'][$rpage['page_cat']]['title'];
    $mskin = cot_tplfile(array('page', 'add', cot::$structure['page'][$rpage['page_cat']]['tpl']));
}
else
{
    if(!cot::$usr['isadmin']) {
        // User can add page to these categories
        $categories = array();
        if (!empty(cot::$structure['page'])) {
            foreach (cot::$structure['page'] as $i => $x) {
                $display = cot_auth('page', $i, 'W');
                if ($display && !empty($subcat) && isset(cot::$structure['page'][$subcat])) {
                    $mtch = cot::$structure['page'][$subcat]['path'] . ".";
                    $mtchlen = mb_strlen($mtch);
                    $display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i === $subcat);
                }
                if ($i != 'all' && $display) {
                    $categories[] = $i;
                }
            }
        }
        cot_block(count($categories) > 0);
    }

    cot::$sys['sublocation'] = cot::$L['page_addtitle'];
    $mskin = cot_tplfile(array('page', 'add'));
}

cot::$out['subtitle'] = cot::$L['page_addsubtitle'];
cot::$out['head'] .= cot::$R['code_noindex'];

/* === Hook === */
foreach (cot_getextplugins('page.add.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once cot::$cfg['system_dir'].'/header.php';
$t = new XTemplate($mskin);

$pageadd_array = array(
	'PAGEADD_PAGETITLE' => cot::$L['page_addtitle'],
	'PAGEADD_SUBTITLE'  => cot::$L['page_addsubtitle'],
	'PAGEADD_ADMINEMAIL' => "mailto:".cot::$cfg['adminemail'],
	'PAGEADD_FORM_SEND' => cot_url('page', 'm=add&a=add&c='.$c),
	'PAGEADD_FORM_CAT' => cot_selectbox_structure('page', $rpage['page_cat'], 'rpagecat'),
	'PAGEADD_FORM_CAT_SHORT' => cot_selectbox_structure('page', $rpage['page_cat'], 'rpagecat', $c),
	'PAGEADD_FORM_KEYWORDS' => cot_inputbox('text', 'rpagekeywords', $rpage['page_keywords'], array('size' => '32', 'maxlength' => '255')),
	'PAGEADD_FORM_METATITLE' => cot_inputbox('text', 'rpagemetatitle', $rpage['page_metatitle'], array('size' => '64', 'maxlength' => '255')),
	'PAGEADD_FORM_METADESC' => cot_textarea('rpagemetadesc', $rpage['page_metadesc'], 2, 64, array('maxlength' => '255')),
	'PAGEADD_FORM_ALIAS' => cot_inputbox('text', 'rpagealias', $rpage['page_alias'], array('size' => '32', 'maxlength' => '255')),
	'PAGEADD_FORM_TITLE' => cot_inputbox('text', 'rpagetitle', $rpage['page_title'], array('size' => '64', 'maxlength' => '255')),
	'PAGEADD_FORM_DESC' => cot_textarea('rpagedesc', $rpage['page_desc'], 2, 64, array('maxlength' => '255')),
	'PAGEADD_FORM_AUTHOR' => cot_inputbox('text', 'rpageauthor', $rpage['page_author'], array('size' => '24', 'maxlength' => '100')),
	'PAGEADD_FORM_OWNER' => cot_build_user(cot::$usr['id'], htmlspecialchars(cot::$usr['name'])),
	'PAGEADD_FORM_OWNERID' => cot::$usr['id'],
	'PAGEADD_FORM_DATE' => cot_selectbox_date($sys['now'], 'long', 'rpagedate'),
	'PAGEADD_FORM_BEGIN' => cot_selectbox_date($sys['now'], 'long', 'rpagebegin'),
	'PAGEADD_FORM_EXPIRE' => cot_selectbox_date(0, 'long', 'rpageexpire'),
	'PAGEADD_FORM_FILE' => cot_selectbox($rpage['page_file'], 'rpagefile', range(0, 2), array(cot::$L['No'], cot::$L['Yes'],
        cot::$L['Members_only']), false),
	'PAGEADD_FORM_URL' => cot_inputbox('text', 'rpageurl', $rpage['page_url'], array('size' => '56', 'maxlength' => '255')),
	'PAGEADD_FORM_SIZE' => cot_inputbox('text', 'rpagesize', $rpage['page_size'], array('size' => '56', 'maxlength' => '255')),
	'PAGEADD_FORM_TEXT' => cot_textarea('rpagetext', $rpage['page_text'], 24, 120, '', 'input_textarea_editor'),
	'PAGEADD_FORM_PARSER' => cot_selectbox(cot::$cfg['page']['parser'], 'rpageparser', $parser_list, $parser_list, false)
);

$t->assign($pageadd_array);

// Extra fields
if(!empty(cot::$extrafields[cot::$db->pages])) {
    foreach (cot::$extrafields[cot::$db->pages] as $exfld) {
        $uname = strtoupper($exfld['field_name']);
        $exfld_val = cot_build_extrafields('rpage' . $exfld['field_name'], $exfld, $rpage['page_' . $exfld['field_name']]);
        $exfld_title = cot_extrafield_title($exfld, 'page_');

        $t->assign(array(
            'PAGEADD_FORM_' . $uname => $exfld_val,
            'PAGEADD_FORM_' . $uname . '_TITLE' => $exfld_title,
            'PAGEADD_FORM_EXTRAFLD' => $exfld_val,
            'PAGEADD_FORM_EXTRAFLD_TITLE' => $exfld_title
        ));
        $t->parse('MAIN.EXTRAFLD');
    }
}

// Error and message handling
cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('page.add.tags') as $pl)
{
	include $pl;
}
/* ===== */

if (cot::$usr['isadmin'])
{
	if (cot::$cfg['page']['autovalidate']) $usr_can_publish = TRUE;
	$t->parse('MAIN.ADMIN');
}

$t->parse('MAIN');
$t->out('MAIN');

require_once cot::$cfg['system_dir'].'/footer.php';
