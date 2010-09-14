<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin
[END_COT_EXT]
==================== */

/**
 * Forums administration part
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

// Requirements
cot_require_api('auth');
cot_require_api('forms');
cot_require('forums');

$s = cot_import('s', 'G', 'ALP');
$id = cot_import('id', 'G', 'INT');
$d = cot_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

if ($s == 'structure')
{
	require_once cot_incfile('forums', 'admin.structure');
}
else
{
	$t = new XTemplate(cot_skinfile('forums.admin', 'module'));

	$adminpath[] = array(cot_url('admin', 'm=forums'), $L['Forums']);

	/* === Hook === */
	foreach (cot_getextplugins('admin.forums.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($n == 'edit')
	{
		if ($a == 'update')
		{
			$rstate = cot_import('rstate', 'P', 'BOL');
			$rtitle = cot_import('rtitle', 'P', 'TXT');
			$rdesc = cot_import('rdesc', 'P', 'TXT');
			$ricon = cot_import('ricon', 'P', 'TXT');
			$rautoprune = cot_import('rautoprune', 'P', 'INT');
			$rcat = cot_import('rcat', 'P', 'TXT');
			$rallowusertext = cot_import('rallowusertext', 'P', 'BOL');
			$rallowbbcodes = cot_import('rallowbbcodes', 'P', 'BOL');
			$rallowsmilies = cot_import('rallowsmilies', 'P', 'BOL');
			$rallowprvtopics = cot_import('rallowprvtopics', 'P', 'BOL');
			$rallowviewers = cot_import('rallowviewers', 'P', 'BOL');
			$rallowpolls = cot_import('rallowpolls', 'P', 'BOL');
			$rcountposts = cot_import('rcountposts', 'P', 'BOL');
			$rtitle = cot_db_prep($rtitle);
			$rdesc = cot_db_prep($rdesc);
			$rcat = cot_db_prep($rcat);
			$rmaster = cot_import('rmaster', 'P', 'INT');
			$mastername = $rtitle;

			/* === Hook === */
			foreach (cot_getextplugins('admin.forums.update') as $pl)
			{
				include $pl;
			}
			/* ===== */

			$sql = cot_db_query("SELECT fs_id, fs_masterid, fs_order, fs_category FROM $db_forum_sections WHERE fs_id=$id ");
			cot_die(cot_db_numrows($sql) == 0);
			$row_cur = cot_db_fetcharray($sql);

			if ($rmaster != '' && $row_cur['fs_masterid'] != $rmaster || empty($row_cur['fs_mastername']))
			{
				$sql1 = cot_db_query("SELECT fs_title FROM $db_forum_sections WHERE fs_id='$rmaster' ");
				$row1 = cot_db_fetcharray($sql1);

				$master = cot_db_prep($row1['fs_title']);

				$sql = cot_db_query("UPDATE $db_forum_sections SET fs_masterid='" . $rmaster . "', fs_mastername='" . $master . "' WHERE fs_id='" . $id . "' ");
			}

			if ($row_cur['fs_category'] != $rcat)
			{
				$sql = cot_db_query("SELECT fs_order FROM $db_forum_sections WHERE fs_category='" . $rcat . "' ORDER BY fs_order DESC LIMIT 1");

				if (cot_db_numrows($sql) > 0)
				{
					$row_oth = cot_db_fetcharray($sql);
					$rorder = $row_oth['fs_order'] + 1;
				}
				else
				{
					$rorder = 100;
				}

				$sql = cot_db_query("UPDATE $db_forum_sections SET fs_order=fs_order-1 WHERE fs_category='" . $row_cur['fs_category'] . "' AND fs_order>" . $row_cur['fs_order']);
				$sql = cot_db_query("UPDATE $db_forum_sections SET fs_order='$rorder' WHERE fs_id='$id'");
			}

			if (!empty($rtitle))
			{
				$sql = cot_db_query("UPDATE $db_forum_sections SET fs_state='$rstate', fs_title='$rtitle', fs_desc='$rdesc', fs_category='$rcat' , fs_icon='$ricon', fs_autoprune='$rautoprune', fs_allowusertext='$rallowusertext', fs_allowbbcodes='$rallowbbcodes', fs_allowsmilies='$rallowsmilies', fs_allowprvtopics='$rallowprvtopics', fs_allowviewers='$rallowviewers', fs_allowpolls='$rallowpolls', fs_countposts='$rcountposts' WHERE fs_id='$id'");
				$sql = cot_db_query("UPDATE $db_forum_sections SET fs_mastername='" . $mastername . "' WHERE fs_masterid='$id' ");
			}

			if ($cot_cache && $cfg['cache_forums'])
			{
				$cot_cache->page->clear('forums');
			}

			cot_redirect(cot_url('admin', 'm=forums&d=' . $d . $additionsforurl, '', true));
		}
		elseif ($a == 'resync')
		{
			cot_check_xg();
			cot_forum_resync($id);

			if ($cot_cache && $cfg['cache_forums'])
			{
				$cot_cache->page->clear('forums');
			}

			cot_message('Resynced');
		}

		$sql = cot_db_query("SELECT * FROM $db_forum_sections WHERE fs_id='$id'");
		cot_die(cot_db_numrows($sql) == 0);
		$row = cot_db_fetcharray($sql);

		extract($row);

		$adminpath[] = array(cot_url('admin', 'm=forums&n=edit&id=' . $id), htmlspecialchars($fs_title));

		$sqlc = cot_db_query("SELECT fs_id FROM $db_forum_sections WHERE fs_masterid='" . $id . "' ");
		if (!cot_db_numrows($sqlc))
		{
			$sqla = cot_db_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category WHERE fs_id<>$id AND fs_masterid<1 AND fs_category='" . $fs_category . "' ORDER by fn_path ASC, fs_order ASC");
			while ($rowa = cot_db_fetchassoc($sqla))
			{
				$forumslist[$rowa['fs_id']] = cot_build_forums($rowa['fs_id'], $rowa['fs_title'], $rowa['fs_category'], FALSE);
			}
		}

		$t->assign(array(
			'ADMIN_FORUMS_EDIT_FORM_URL' => cot_url('admin', 'm=forums&n=edit&a=update&id=' . $fs_id),
			'ADMIN_FORUMS_EDIT_FS_ID' => $fs_id,
			'ADMIN_FORUMS_EDIT_SELECTBOX_FORUMCAT' => cot_selectbox_forumcat($fs_category, 'rcat'),
			'ADMIN_FORUMS_EDIT_FS_TITLE' => cot_inputbox('text', 'rtitle', htmlspecialchars($fs_title), 'size="56" maxlength="128"'),
			'ADMIN_FORUMS_EDIT_FS_DESC' => cot_inputbox('text', 'rdesc', htmlspecialchars($fs_desc), 'size="56"'),
			'ADMIN_FORUMS_EDIT_FS_ICON' => cot_inputbox('text', 'ricon', htmlspecialchars($fs_icon), 'size="56"'),
			'ADMIN_FORUMS_EDIT_FS_ALLOWUSERTEXT' => cot_radiobox($fs_allowusertext, 'rallowusertext', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_FORUMS_EDIT_FS_ALLOWBBCODES' => cot_radiobox($fs_allowbbcodes, 'rallowbbcodes', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_FORUMS_EDIT_FS_ALLOWSMILES' => cot_radiobox($fs_allowsmilies, 'rallowsmilies', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_FORUMS_EDIT_FS_ALLOWPRVTOPICS' => cot_radiobox($fs_allowprvtopics, 'rallowprvtopics', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_FORUMS_EDIT_FS_ALLOWVIEWERS' => cot_radiobox($fs_allowviewers, 'rallowviewers', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_FORUMS_EDIT_FS_ALLOWPOLLS' => cot_radiobox($fs_allowpolls, 'rallowpolls', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_FORUMS_EDIT_FS_COUNTPOSTS' => cot_radiobox($fs_countposts, 'rcountposts', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_FORUMS_EDIT_FS_STATE' => cot_radiobox($fs_state, 'rstate', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_FORUMS_EDIT_FS_MASTER' => cot_selectbox($fs_masterid, 'rmaster', array_keys($forumslist), array_values($forumslist)),
			'ADMIN_FORUMS_EDIT_FS_AUTOPRUNE' => cot_inputbox('text', 'rautoprune', $fs_autoprune, 'size="3" maxlength="7"'),
			'ADMIN_FORUMS_EDIT_RESYNC_URL' => cot_url('admin', 'm=forums&n=edit&a=resync&id=' . $fs_id . '&' . cot_xg())
		));
		/* === Hook === */
		foreach (cot_getextplugins('admin.forums.edit') as $pl)
		{
			include $pl;
		}
		/* ===== */
		$t->parse('MAIN.EDIT');
	}
	else
	{
		if ($a == 'order')
		{
			$w = cot_import('w', 'G', 'ALP', 4);

			$sql = cot_db_query("SELECT fs_order, fs_category FROM $db_forum_sections WHERE fs_id='" . $id . "'");
			cot_die(cot_db_numrows($sql) == 0);
			$row_cur = cot_db_fetcharray($sql);

			/* === Hook === */
			foreach (cot_getextplugins('admin.forums.order') as $pl)
			{
				include $pl;
			}
			/* ===== */

			if ($w == 'up')
			{
				$sql = cot_db_query("SELECT fs_id, fs_order FROM $db_forum_sections WHERE fs_category='" . $row_cur['fs_category'] . "' AND fs_order<'" . $row_cur['fs_order'] . "' ORDER BY fs_order DESC LIMIT 1");
			}
			else
			{
				$sql = cot_db_query("SELECT fs_id, fs_order FROM $db_forum_sections WHERE fs_category='" . $row_cur['fs_category'] . "' AND fs_order>'" . $row_cur['fs_order'] . "' ORDER BY fs_order ASC LIMIT 1");
			}
			if (cot_db_numrows($sql) > 0)
			{
				$row_oth = cot_db_fetcharray($sql);
				$sql = cot_db_query("UPDATE $db_forum_sections SET fs_order='" . $row_oth['fs_order'] . "' WHERE fs_id='" . $id . "'");
				$sql = cot_db_query("UPDATE $db_forum_sections SET fs_order='" . $row_cur['fs_order'] . "' WHERE fs_id='" . $row_oth['fs_id'] . "'");

				if ($cot_cache && $cfg['cache_forums'])
				{
					$cot_cache->page->clear('forums');
				}
			}

			cot_message('Ordered');
		}
		elseif ($a == 'add')
		{
			$nmaster = cot_import('nmaster', 'P', 'INT');
			$ntitle = cot_import('ntitle', 'P', 'TXT');
			$ndesc = cot_import('ndesc', 'P', 'TXT');
			$ncat = cot_import('ncat', 'P', 'TXT');

			if (!empty($ntitle))
			{
				$sql1 = cot_db_query("SELECT fs_order FROM $db_forum_sections WHERE fs_category='" . cot_db_prep($ncat) . "' ORDER BY fs_order DESC LIMIT 1");
				if ($row1 = cot_db_fetcharray($sql1))
				{
					$nextorder = $row1['fs_order'] + 1;
				}
				else
				{
					$nextorder = 100;
				}

				if (!empty($nmaster))
				{
					$sql2 = cot_db_query("SELECT fs_title FROM $db_forum_sections WHERE fs_id='" . $nmaster . "' ");
					$row2 = cot_db_fetcharray($sql2);

					$mastername = cot_db_prep($row2['fs_title']);
				}

				$sql = cot_db_query("INSERT INTO $db_forum_sections (fs_masterid, fs_mastername, fs_order, fs_title, fs_desc, fs_category, fs_icon, fs_autoprune, fs_allowusertext, fs_allowbbcodes, fs_allowsmilies, fs_allowprvtopics, fs_countposts) VALUES ('" . (int) $nmaster . "', '" . $mastername . "', '" . (int) $nextorder . "', '" . cot_db_prep($ntitle) . "', '" . cot_db_prep($ndesc) . "', '" . cot_db_prep($ncat) . "', 'images/admin/forums.gif', 0, 1, 1, 1, 0, 1)");

				$forumid = cot_db_insertid();

				/* === Hook === */
				foreach (cot_getextplugins('admin.forums.add') as $pl)
				{
					include $pl;
				}
				/* ===== */

				// The permissions are actually the default
				// Some records are left for example
				$auth_permit = array(
					COT_GROUP_DEFAULT => 'RW'
				);

				$auth_lock = array(
					COT_GROUP_DEFAULT => '0',
					COT_GROUP_MEMBERS => 'A'
				);

				cot_auth_add_item('forums', $forumid, $auth_permit, $auth_lock);

				if ($cot_cache && $cfg['cache_forums'])
				{
					$cot_cache->page->clear('forums');
				}

				cot_message('Added');
			}
			else
			{
				cot_error('adm_forum_emptytitle', 'ntitle');
			}
		}
		elseif ($a == 'delete')
		{
			cot_check_xg();
			cot_auth_clear('all');
			$num = cot_forum_deletesection($id);
			$sql1 = cot_db_query("UPDATE $db_forum_sections SET fs_masterid='0', fs_mastername='' WHERE fs_masterid='" . $id . "' ");
			//$num = cot_db_numrows($sql1);

			if ($cot_cache && $cfg['cache_forums'])
			{
				$cot_cache->page->clear('forums');
			}

			/* === Hook === */
			foreach (cot_getextplugins('admin.forums.delete') as $pl)
			{
				include $pl;
			}
			/* ===== */

			cot_message('Deleted');
		}
		/*
		  $totalitems = cot_db_rowcount($db_forum_sections)+cot_db_rowcount($db_forum_structure);
		  $pagenav = cot_pagenav('admin','m=forums', $d, $totalitems, $cfg['maxrowsperpage']);
		 */
		$sql = cot_db_query("SELECT s.*, n.*
		FROM $db_forum_sections AS s LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category
		ORDER by fs_masterid DESC, fn_path ASC, fs_order ASC, fs_title ASC");

		$prev_cat = '';
		$line = 1;
		$fcache = array();

		$ii = 0;
		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('admin.forums.loop');
		/* ===== */
		while ($row = cot_db_fetcharray($sql))
		{
			if ($row['fs_masterid'] > 0)
			{
				$fcache[$row['fs_masterid']][$row['fs_id']] = array($row['fs_title'], $row['fs_topiccount'], $row['fs_postcount'], $row['fs_viewcount'], $row['fs_allowprvtopics']);
			}
			else
			{
				$fs_id = $row['fs_id'];
				$fs_state = $row['fs_state'];
				$fs_order = $row['fs_order'];
				$fs_title = htmlspecialchars($row['fs_title']);
				$fs_desc = htmlspecialchars($row['fs_desc']);
				$fs_category = $row['fs_category'];
				$show_fn = ($fs_category != $prev_cat) ? true : false;

				if ($fs_category != $prev_cat)
				{
					$prev_cat = $fs_category;
					$line = 1;

					$ii++;
				}

				$line++;
				$ii++;

				if ($fcache[$fs_id])
				{
					foreach ($fcache[$fs_id] as $key => $value)
					{
						$t->assign(array(
							'ADMIN_FORUMS_DEFAULT_ROW_DELETE_URL' => cot_url('admin', 'm=forums&a=delete&id=' . $key . '&' . cot_xg()),
							'ADMIN_FORUMS_DEFAULT_ROW_FS_EDIT_URL' => cot_url('admin', 'm=forums&n=edit&id=' . $key),
							'ADMIN_FORUMS_DEFAULT_ROW_FS_TITLE' => htmlspecialchars($value[0]),
							'ADMIN_FORUMS_DEFAULT_ROW_FS_ORDER_UP_URL' => cot_url('admin', 'm=forums&id=' . $key . '&a=order&w=up&d=' . $d),
							'ADMIN_FORUMS_DEFAULT_ROW_FS_ORDER_DOWN_URL' => cot_url('admin', 'm=forums&id=' . $key . '&a=order&w=down&d=' . $d),
							'ADMIN_FORUMS_DEFAULT_ROW_FS_ALLOWPRVTOPICS' => $cot_yesno[$value[4]],
							'ADMIN_FORUMS_DEFAULT_ROW_FS_TOPICCOUNT' => $value[1],
							'ADMIN_FORUMS_DEFAULT_ROW_FS_POSTCOUNT' => $value[2],
							'ADMIN_FORUMS_DEFAULT_ROW_FS_VIEWCOUNT' => $value[3],
							'ADMIN_FORUMS_DEFAULT_ROW_FS_RIGHTS_URL' => cot_url('admin', 'm=rightsbyitem&ic=forums&io=' . $key),
							'ADMIN_FORUMS_DEFAULT_ROW_FS_TOPICS_URL' => cot_url('forums', 'm=topics&s=' . $key)
						));

						/* === Hook - Part2 : Include === */
						foreach ($extp as $pl)
						{
							include $pl;
						}
						/* ===== */

						$t->parse('MAIN.DEFULT.ROW.FCACHE');

						$ii++;
					}
				}

				$t->assign(array(
					'ADMIN_FORUMS_DEFAULT_ROW_FN_URL' => cot_url('admin', 'm=forums&s=structure&n=options&id=' . $row['fn_id']),
					'ADMIN_FORUMS_DEFAULT_ROW_FN_TITLE' => htmlspecialchars($row['fn_title']),
					'ADMIN_FORUMS_DEFAULT_ROW_FN_PATH' => $row['fn_path'],
					'ADMIN_FORUMS_DEFAULT_ROW_DELETE_URL' => cot_url('admin', 'm=forums&a=delete&id=' . $fs_id . '&' . cot_xg()),
					'ADMIN_FORUMS_DEFAULT_ROW_FS_EDIT_URL' => cot_url('admin', 'm=forums&n=edit&id=' . $fs_id),
					'ADMIN_FORUMS_DEFAULT_ROW_FS_TITLE' => htmlspecialchars($fs_title),
					'ADMIN_FORUMS_DEFAULT_ROW_FS_ORDER_UP_URL' => cot_url('admin', 'm=forums&id=' . $fs_id . '&a=order&w=up&d=' . $d),
					'ADMIN_FORUMS_DEFAULT_ROW_FS_ORDER_DOWN_URL' => cot_url('admin', 'm=forums&id=' . $fs_id . '&a=order&w=down&d=' . $d),
					'ADMIN_FORUMS_DEFAULT_ROW_FS_ALLOWPRVTOPICS' => $cot_yesno[$row['fs_allowprvtopics']],
					'ADMIN_FORUMS_DEFAULT_ROW_FS_TOPICCOUNT' => $row['fs_topiccount'],
					'ADMIN_FORUMS_DEFAULT_ROW_FS_POSTCOUNT' => $row['fs_postcount'],
					'ADMIN_FORUMS_DEFAULT_ROW_FS_VIEWCOUNT' => $row['fs_viewcount'],
					'ADMIN_FORUMS_DEFAULT_ROW_FS_RIGHTS_URL' => cot_url('admin', 'm=rightsbyitem&ic=forums&io=' . $fs_id),
					'ADMIN_FORUMS_DEFAULT_ROW_FS_TOPICS_URL' => cot_url('forums', 'm=topics&s=' . $fs_id)
				));

				/* === Hook - Part2 : Include === */
				foreach ($extp as $pl)
				{
					include $pl;
				}
				/* ===== */

				$t->parse('MAIN.DEFULT.ROW');
			}
		}

		$sqla = cot_db_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category WHERE fs_masterid<1 ORDER by fn_path ASC, fs_order ASC");

		while ($rowa = cot_db_fetchassoc($sqla))
		{
			$forumslist[$rowa['fs_id']] = cot_build_forums($rowa['fs_id'], $rowa['fs_title'], $rowa['fs_category'], FALSE);
		}

		$t->assign(array(
			'ADMIN_FORUMS_DEFAULT_FORM_UPDATEORDER_URL' => cot_url('admin', 'm=forums&a=update&d=' . $d),
			//'ADMIN_FORUMS_PAGINATION_PREV' => $pagenav['prev'],
			//'ADMIN_FORUMS_PAGNAV' => $pagenav['main'],
			//'ADMIN_FORUMS_PAGINATION_NEXT' => $pagenav['next'],
			'ADMIN_FORUMS_TOTALITEMS' => $totalitems,
			'ADMIN_FORUMS_COUNTER_ROW' => $ii,
			'ADMIN_FORUMS_DEFAULT_FORM_ADD_URL' => cot_url('admin', 'm=forums&a=add'),
			'ADMIN_FORUMS_DEFAULT_FORM_ADD_SELECTBOX_FORUMCAT' => cot_selectbox_forumcat('', 'ncat'),
			'ADMIN_FORUMS_DEFAULT_FORM_ADD_TITLE' => cot_inputbox('text', 'ntitle', htmlspecialchars($fs_title), 'size="56" maxlength="128"'),
			'ADMIN_FORUMS_DEFAULT_FORM_ADD_DESC' => cot_inputbox('text', 'ndesc', htmlspecialchars($fs_desc), 'size="56"'),
			'ADMIN_FORUMS_DEFAULT_FORM_ADD_MASTER' => cot_selectbox(0, 'nmaster', array_keys($forumslist), array_values($forumslist))
		));
		$t->parse('MAIN.DEFULT');
	}

	$lincif_conf = cot_auth('admin', 'a', 'A');

	$t->assign(array(
		'ADMIN_FORUMS_CONF_URL' => cot_url('admin', 'm=config&n=edit&o=core&p=forums'),
		'ADMIN_FORUMS_CONF_STRUCTURE_URL' => cot_url('admin', 'm=forums&s=structure'),
	));

	/* === Hook === */
	foreach (cot_getextplugins('admin.forums.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */
}

cot_display_messages($t);

$t->parse('MAIN');
if (COT_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}
?>
