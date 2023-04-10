<?php

/**
 * Polls functions
 *
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

// Requirements
require_once cot_incfile('forms');
require_once cot_langfile('polls', 'module');

/*
 * Poll status
 * 0 - active
 * 1 - locked
*/
const COT_POLL_ACTIVE = 0;
const COT_POLL_LOCKED = 1;

Cot::$db->registerTable('polls');
Cot::$db->registerTable('polls_options');
Cot::$db->registerTable('polls_voters');

/**
 * Adds form for create/edit Poll
 *
 * @param int $id Poll ID or Poll Code if $type is not empty or new for new Poll
 * @param XTemplate $t Template
 * @param string $block Poll block in Template
 * @param string $type Poll type
 * @return bool
 * @global CotDB $db
 */
function cot_poll_edit_form($id, $t = '', $block = 'MAIN', $type = '')
{
	$id = (int) $id;
	global $db, $cfg, $db_polls, $db_polls_options, $poll_id, $R, $L, $poll_options, $poll_multiple, $poll_state, $poll_text;

    $poll_full_template = false;
	if (gettype($t) != 'object')
	{
		$t = new XTemplate(cot_tplfile('polls'));
		$block = 'EDIT_POLL_FORM';
		$poll_full_template = true;
	}
	$counter = 0;
	$multiple = !empty($poll_multiple) ? true : false;
	if (cot_error_found() && !empty($poll_options))
	{
		$id = (int) $poll_id;
		foreach ($poll_options as $key => $val)
		{
			if ($val != '')
			{
				$counter++;
				$t->assign('EDIT_POLL_OPTION_TEXT', cot_inputbox('text', 'poll_option[]', htmlspecialchars($val), 'size="40" maxlength="128"'));
				$t->parse($block . ".OPTIONS");
			}
		}

	} elseif (!empty($id)) {
        if ($type) {
            $where = "poll_type = :type AND poll_code = :code";
            $params = ['type' => $type, 'code' => $id];
        } else {
            $where = "poll_id = ?";
            $params = [$id];
        }

		$sql = Cot::$db->query('SELECT * FROM ' . Cot::$db->polls . " WHERE $where LIMIT 1", $params);
		if ($row = $sql->fetch()) {
			$id = $row["poll_id"];
			$poll_text = htmlspecialchars($row["poll_text"]);
			$multiple = (bool)$row['poll_multiple'];

			$sql1 = $db->query("SELECT * FROM $db_polls_options WHERE po_pollid = $id ORDER by po_id ASC");
			while ($row1 = $sql1->fetch())
			{
				$counter++;
				$t->assign('EDIT_POLL_OPTION_TEXT', cot_inputbox('text', 'poll_option[id' . $row1['po_id'] . ']', $row1['po_text'],
                    'size="40" maxlength="128"'));
				$t->parse($block . ".OPTIONS");
			}
			$sql1->closeCursor();
		} else {
            $id = 0;
        }
	}

	while ($counter < 2) {
		$counter++;
		$t->assign('EDIT_POLL_OPTION_TEXT', cot_inputbox('text', 'poll_option[]', '', 'size="40" maxlength="128"'));
		$t->parse($block . ".OPTIONS");
	}

	if ($counter < $cfg['polls']['max_options_polls']) {
		$counter++;
		$t->assign('EDIT_POLL_OPTION_TEXT', cot_inputbox('text', 'poll_option[]', '', 'size="40" maxlength="128"'));
		$t->parse($block . ".OPTIONS");
	}

	if ((int) $id > 0) {
		$t->assign(array(
			'EDIT_POLL_LOCKED' => cot_checkbox($poll_state, 'poll_state', $L['Locked']),
			'EDIT_POLL_RESET' => cot_checkbox(0, 'poll_reset', $L['Reset']),
			'EDIT_POLL_DELETE' => cot_checkbox(0, 'poll_delete', $L['Delete']),
			'EDIT_POLL_EDIT' => true,
		));
		$t->parse($block . ".EDIT");
	}

	$t->assign(array(
		'EDIT_POLL_TEXT' => cot_inputbox('text', 'poll_text', $poll_text, 'size="64" maxlength="255"'),
		'EDIT_POLL_IDFIELD' => cot_inputbox('hidden', 'poll_id', $id),
		'EDIT_POLL_OPTIONSCOUNT' => $counter,
		'EDIT_POLL_ID' => $id,
		'EDIT_POLL_MULTIPLE' => cot_checkbox($multiple, 'poll_multiple', $L['polls_multiple']),
	));

	if ($poll_full_template) {
		$t->parse($block);
		return ($t->text($block));
	}

	return true;
}

/**
 * Check Poll form
 */
function cot_poll_check()
{
	global $poll_id, $poll_text, $poll_multiple, $poll_state, $poll_options;

    $poll_id = (int) cot_import('poll_id', 'P', 'INT');

	$poll_delete = cot_import('poll_delete', 'P', 'BOL');
	$poll_reset = cot_import('poll_reset', 'P', 'BOL');

    $poll_text = cot_import('poll_text', 'P', 'HTM');
	$poll_text = !empty($poll_text) ? trim($poll_text) : '';
	$poll_multiple = cot_import('poll_multiple', 'P', 'BOL');
	$poll_state = cot_import('poll_state', 'P', 'BOL');
	$poll_options = cot_import('poll_option', 'P', 'ARR');

	if ($poll_id > 0) {
        if ($poll_delete) {
            cot_poll_delete($poll_id);
            $poll_id = 0;
            $poll_text = null;
            $poll_options = null;
            return;
        }

		if ($poll_reset) {
			cot_poll_reset($poll_id);
		}
	}

    $poll_options_temp = [];
    foreach ($poll_options as $key => $val) {
        $val = trim(cot_import($val, 'D', 'TXT'));
        if (!empty($val)) {
            $poll_options_temp[$key] = $val;
        }
    }

    $poll_options = $poll_options_temp;
    if (Cot::$cfg['polls']['del_dup_options']) {
        $poll_options = array_unique($poll_options);
    }

    if (mb_strlen($poll_text) < 4) {
        cot_error('polls_error_title', 'poll_text');
    }

    if (count($poll_options) < 2) {
        cot_error('polls_error_count', 'poll_option');
    }
}

/**
 * Save Poll form
 *
 * @param string $type Poll type
 * @param int $code Poll Code
 * @return int|null Saved poll id or null if it was not saved
 * @global CotDB $db
 */
function cot_poll_save($type = 'index', $code = '')
{
	global $poll_id, $poll_text, $poll_multiple, $poll_state, $poll_options;

    if (!isset($poll_id) || cot_error_found() || empty($poll_options)) {
        return null;
    }

    $poll_id = (int) $poll_id;

    if ($poll_id > 0) {
        Cot::$db->update(
            Cot::$db->polls,
            [
                'poll_state' => (int) $poll_state,
                'poll_text' => $poll_text,
                'poll_multiple' => (int) $poll_multiple,
            ],
            'poll_id = ?',
            $poll_id
        );
        $newPollId = $poll_id;

    } else {
        Cot::$db->insert(
            Cot::$db->polls,
            [
                'poll_type' => $type,
                'poll_state' => COT_POLL_ACTIVE,
                'poll_creationdate' => (int) Cot::$sys['now'],
                'poll_text' => $poll_text,
                'poll_multiple' => (int) $poll_multiple,
                'poll_code' => $code,
            ]
        );
        $newPollId = Cot::$db->lastInsertId();
    }

    $ids = [];
    foreach ($poll_options as $key => $val) {
        if (!empty($val)) {
            $key = (int) mb_substr($key, 2);
            if ($key > 0 && $poll_id > 0) {
                Cot::$db->update(Cot::$db->polls_options, ['po_text' => $val,], 'po_id = ?', $key);
                $ids[] = $key;
            } else {
                Cot::$db->insert(
                    Cot::$db->polls_options,
                    [
                        'po_pollid' => $newPollId,
                        'po_text' => $val,
                        'po_count' => 0,
                    ]
                );
                $ids[] = Cot::$db->lastInsertId();
            }
        }
    }

    if ($poll_id > 0 && count($ids) > 0) {
        Cot::$db->delete(
            Cot::$db->polls_options,
            "po_pollid = {$newPollId} AND po_id NOT IN (" . implode(", ", $ids) . ")");
    }

    return $newPollId;
}

/**
 * Poll function
 * @global CotDB $db
 */
function cot_poll_vote()
{
	global $db, $cfg, $db_polls, $db_polls_options, $db_polls_voters, $usr;

	$vote = cot_import('vote', 'P', 'ARR');
	$id = (int) cot_import('poll_id', 'P', 'INT');

	if (count($vote) > 0)
	{
		$alreadyvoted = 0;
		$sql = $db->query("SELECT * FROM $db_polls WHERE poll_id = $id");
		if ($row = $sql->fetch())
		{
			if ($cfg['polls']['ip_id_polls'] == 'id' && $usr['id'] > 0)
			{
				$where = "pv_userid = '" . $usr['id'] . "'";
			}
			else
			{
				$where = ($usr['id'] > 0) ? "(pv_userid = '" . $usr['id'] . "' OR pv_userip = '" . $usr['ip'] . "')" : "pv_userip = '" . $usr['ip'] . "'";
			}
			$sql2 = $db->query("SELECT pv_id FROM $db_polls_voters WHERE pv_pollid = $id AND $where LIMIT 1");
			$alreadyvoted = ($sql2->rowCount() == 1) ? 1 : 0;

			if ($alreadyvoted != 1 && !($cfg['polls']['ip_id_polls'] == 'id' && $usr['id'] == 0))
			{
				foreach ($vote as $val)
				{
					$sql2 = $db->query("UPDATE $db_polls_options SET po_count = po_count+1 WHERE po_pollid = $id AND po_id = '" . (int) $val . "'");
				}
				if ($db->affectedRows > 0)
				{
					$db->insert($db_polls_voters, array(
						'pv_pollid' => $id,
						'pv_userid' => (int) $usr['id'],
						'pv_userip' => $usr['ip']
					));
				}
			}
		}
	}
}

/**
 * Generates Poll form
 *
 * @param int $id Poll ID or Poll Code if $type is not epmty
 * @param string $formlink Poll form url
 * @param string $theme Poll template name
 * @param string $type Poll type
 * @return bool|array
 */
function cot_poll_form($id, $formlink = '', $theme = '', $type = '')
{
	$canvote = false;

	if (!is_array($id))
	{
		$id = (int) $id;
		$where = (!$type) ? "poll_id = $id" : "poll_type = '" . Cot::$db->prep($type) . "' AND poll_code = '$id'";
		$sql = Cot::$db->query("SELECT * FROM ".Cot::$db->polls." WHERE $where LIMIT 1");
		if (!$row = $sql->fetch())
		{
			return false;
		}
	}
	else
	{
		$row = $id;
	}
	$id = $row['poll_id'];

	$alreadyvoted = 0;
	if (Cot::$cfg['polls']['ip_id_polls'] == 'id' && Cot::$usr['id'] > 0)
	{
		$where = "pv_userid = '" . Cot::$usr['id'] . "'";
		$canvote = true;
	}
	else
	{
		$where = (Cot::$usr['id'] > 0) ? "(pv_userid = '" . Cot::$usr['id'] . "' OR pv_userip = '" .
            Cot::$usr['ip'] . "')" : "pv_userip = '" . Cot::$usr['ip'] . "'";
		$canvote = true;
	}
	$sql2 = Cot::$db->query("SELECT pv_id FROM ".Cot::$db->polls_voters." WHERE pv_pollid = $id AND $where LIMIT 1");
	$alreadyvoted = ($sql2->rowCount() == 1) ? 1 : 0;

	$themefile = cot_tplfile(array('polls', $theme), 'module');
	$t = new XTemplate($themefile);

	if ($alreadyvoted) {
        $poll_block = 'POLL_VIEW_VOTED';
    }
	elseif (!$canvote)
    {
        $poll_block = 'POLL_VIEW_DISABLED';
    }
	elseif ($row['poll_state'])
    {
        $poll_block = 'POLL_VIEW_LOCKED';
    }
	else
	{
        $poll_block = 'POLL_VIEW';
    }

    $totalVotes = $maxVotes = 0;

	$sql2 = Cot::$db->query("SELECT SUM(po_count) as total_votes, MAX(po_count) as max_votes FROM ".
        Cot::$db->polls_options." WHERE po_pollid = $id")->fetch();

	if(!empty($sql2))
    {
        // Total votes in the poll
        $totalVotes = $sql2['total_votes'];

        // Max votes count for one option
        $maxVotes   = $sql2['max_votes'];
    }

	$sql1 = Cot::$db->query("SELECT po_id, po_text, po_count FROM ".Cot::$db->polls_options." WHERE po_pollid = $id ORDER by po_id ASC");

	/* === Hook === */
    foreach (cot_getextplugins('polls.form.options.first') as $pl)
    {
        include $pl;
    }
    /* ===== */

    /* === Hook - Part1 : Set === */
    $extp = cot_getextplugins('polls.form.options.loop');
    /* ===== */
	while ($row1 = $sql1->fetch())
	{
		$po_id = $row1['po_id'];
		$po_count = $row1['po_count'];

        // Percentage from the total votes count
		$percentTotal = ($totalVotes > 0) ? round(100 * ($po_count / $totalVotes), 1) : 0;

        // Percentage from the option with maximum votes count
        $percentMax   = ($maxVotes   > 0) ? round(100 * ($po_count / $maxVotes),   1) : 0;

        $polloption = cot_parse($row1['po_text'], Cot::$cfg['polls']['markup']);

		$input_type = $row['poll_multiple'] ? 'checkbox' : 'radio';
        $polloptions_input = '';
        if (!$alreadyvoted && $canvote)
        {
            $polloptions_input = '<input type="' . $input_type . '" name="vote[]" value="' . $po_id . '" />'; // TODO - to resorses
        }

		$t->assign(array(
            'POLL_OPTION'       => $polloption,
            'POLL_VOTES_COUNT'  => $po_count,
            'POLL_VOTES_TOTAL'  => $totalVotes,
            'POLL_VOTES_MAX'    => $maxVotes,
            'POLL_INPUT'        => $polloptions_input,
            'POLL_PERCENT_FROM_TOTAL' => $percentTotal,
            'POLL_PERCENT_FROM_MAX'   => $percentMax,

            // Deprecated tags. May be removed in future versions.
            'POLL_PER'     => $percentTotal, // Deprecated. Use POLL_PERCENT_FROM_TOTAL instead
            'POLL_COUNT'   => $po_count,     // Deprecated. Use POLL_VOTES_COUNT instead
            'POLL_OPTIONS' => $polloption,   // Deprecated. Use POLL_OPTION instead
		));

        /* === Hook - Part2 : Include === */
        foreach ($extp as $pl)
        {
            include $pl;
        }
        /* ===== */

		$t->parse($poll_block . ".POLLTABLE");
	}
	$sql1->closeCursor();

	$t->assign(array(
		'POLL_VOTERS' => $totalVotes,
		'POLL_SINCE' => cot_date('datetime_medium', $row['poll_creationdate']),
		'POLL_SINCE_STAMP' => $row['poll_creationdate'],
		'POLL_SINCE_SHORT' => cot_date('date_short', $row['poll_creationdate']),
		'POLL_TITLE' => cot_parse($row['poll_text'], Cot::$cfg['polls']['markup']),
		'POLL_ID' => $id,
		'POLL_FORM_URL' => (empty($formlink)) ? cot_url('polls', 'id=' . $id) : $formlink,
	));
	$t->parse($poll_block);

	$row['poll_alreadyvoted'] = $alreadyvoted;
	$row['poll_count'] = $totalVotes;
	$row['poll_block'] = $t->text($poll_block);

    /* === Hook === */
    foreach (cot_getextplugins('polls.form.tags') as $pl)
    {
        include $pl;
    }
    /* ===== */

	return($row);
}

/**
 * Delete Poll
 *
 * @param int $id Poll ID or Poll Code if $type is not epmty
 * @param string $type Poll type
 * @return bool
 * @global CotDB $db
 */
function cot_poll_delete($id, $type = '')
{
	global $db, $db_polls, $db_polls_options, $db_polls_voters;

	if ($type)
	{
		$sql = $db->query("SELECT poll_id FROM $db_polls WHERE poll_type = '" . $db->prep($type) . "' AND poll_code = '$id' LIMIT 1");
		$id = ($row = $sql->fetch()) ? $row['poll_id'] : 0;
	}
	if ((int) $id > 0)
	{
		$db->delete($db_polls, "poll_id = " . $id);
		$db->delete($db_polls_options, "po_pollid = " . $id);
		$db->delete($db_polls_voters, "pv_pollid = " . $id);

		/* === Hook === */
		foreach (cot_getextplugins('polls.functions.delete') as $pl)
		{
			include $pl;
		}
		/* ===== */

		return (true);
	}
	else
	{
		return (false);
	}
}

/**
 * Lock Poll
 *
 * @param int $id Poll ID or Poll code if $type is not epmty
 * @param int $state Poll lock status: 0 - unlocked, 1 - locked, 3 - toggle lock status
 * @param string $type Poll type
 * @return bool
 * @global CotDB $db
 */
function cot_poll_lock($id, $state, $type = '')
{
	$id = (int) $id;
	$where = (!$type) ? "poll_id = $id" : "poll_type = " . Cot::$db->quote($type) . " AND poll_code = '$id'";
	if ($state == 3) {
		$sql = Cot::$db->query("SELECT poll_state FROM " . Cot::$db->quoteTableName(Cot::$db->polls) .
            " WHERE  $where LIMIT 1");
		$rstate = ($row = $sql->fetch()) ? $row['poll_state'] : 0;
		$state = ($rstate) ? COT_POLL_ACTIVE : COT_POLL_LOCKED;
	}
	if ($id > 0) {
        Cot::$db->update(Cot::$db->polls, array('poll_state' => (int) $state), $where);
	}

	return (Cot::$db->affectedRows > 0) ? true : false;
}

/**
 * Reset Poll votes
 *
 * @param int $id Poll ID or Poll Code if $type is not epmty
 * @param string $type Poll type
 * @return bool
 * @global CotDB $db
 */
function cot_poll_reset($id, $type = '')
{
	global $db, $db_polls, $db_polls_options, $db_polls_voters;
	$id = (int) $id;
	if ($type)
	{
		$sql = $db->query("SELECT poll_id FROM $db_polls WHERE poll_type = '" . $db->prep($type) . "' AND poll_code = '$id' LIMIT 1");
		$id = ($row = $sql->fetch()) ? $row['poll_id'] : 0;
	}
	if ((int) $id > 0)
	{
		$db->delete($db_polls_voters, "pv_pollid = " . $id);
		$db->update($db_polls_options, array('po_count' => 0), "po_pollid = $id");
	}

	return (($db->affectedRows > 0) ? true : false);
}

/**
 * Checks if Poll exists
 *
 * @param int $id Poll ID or Poll Code if $type is not epmty
 * @param string $type Poll type
 * @return bool true if Poll exists
 * @global CotDB $db
 */
function cot_poll_exists($id, $type = '')
{
	global $db, $db_polls;

	$id = (int) $id;
	$where = (!$type) ? "poll_id = $id" : "poll_type = '" . $db->prep($type) . "' AND poll_code = '$id'";
	$sql = $db->query("SELECT COUNT(*)  FROM $db_polls WHERE $where LIMIT 1");

	return ($sql->fetchColumn());
}
