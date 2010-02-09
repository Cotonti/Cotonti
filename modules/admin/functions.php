<?php
/**
 * Admin function library.
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') && defined('SED_ADMIN') or die('Wrong URL.');

/* ======== Defaulting the admin variables ========= */

unset($adminmain, $adminhelp, $admin_icon, $plugin_body, $plugin_title, $plugin_help);
$adminpath = array();

/* ------------------ */

function sed_auth_getvalue($mask)
{
    $mn['0'] = 0;
    $mn['R'] = 1;
    $mn['W'] = 2;
    $mn['1'] = 4;
    $mn['2'] = 8;
    $mn['3'] = 16;
    $mn['4'] = 32;
    $mn['5'] = 64;
    $mn['A'] = 128;

    $masks = str_split($mask);

    foreach ($mn as $k => $v)
    {
        if (in_array($k, $masks))
        {
        	$res += $mn[$k];
        }
    }
    return($res);
}

/* ------------------ */

function sed_auth_reorder()
{
    global $db_auth;

    $sql = sed_sql_query("ALTER TABLE $db_auth ORDER BY auth_code ASC, auth_option ASC, auth_groupid ASC, auth_code ASC");
    return(TRUE);
}

/* ------------------ */

function sed_build_admrights($rn)
{
    $res = ($rn & 1) ? 'R' : '';
    $res .= (($rn & 2) == 2) ? 'W' : '';
    $res .= (($rn & 4) == 4) ? '1' : '';
    $res .= (($rn & 8) == 8) ? '2' : '';
    $res .= (($rn & 16) == 16) ? '3' : '';
    $res .= (($rn & 32) == 32) ? '4' : '';
    $res .= (($rn & 64) == 64) ? '5' : '';
    $res .= (($rn & 128) == 128) ? 'A' : '';
    return($res);
}

/* ------------------ */

function sed_build_adminsection($adminpath)
{
    global $cfg, $L;

    $result = array();
    $result[] = "<a href=\"".sed_url('admin')."\">".$L['Adminpanel']."</a>";
    foreach ($adminpath as $i => $k)
    {
    	$result[] = "<a href=\"".$k[0]."\">".$k[1]."</a>";
    }
    $result = implode(" ".$cfg['separator']." ", $result);

    return($result);
}

/* ------------------ */

function sed_forum_deletesection($id)
{
    global $db_forum_topics, $db_forum_posts, $db_forum_sections, $db_auth;

    $sql = sed_sql_query("SELECT fs_masterid FROM $db_forum_sections WHERE fs_id='$id' ");
    $row = sed_sql_fetcharray($sql);

    if ($row['fs_masterid'] > 0)
    {
        $sqql = sed_sql_query("SELECT fs_masterid, fs_topiccount, fs_postcount FROM $db_forum_sections WHERE fs_id='$id' ");
        $roww = sed_sql_fetcharray($sqql);

        $sc_posts = $roww['fs_postcount'];
        $sc_topics = $roww['fs_topiccount'];

        $sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount=fs_postcount-".$sc_posts." WHERE fs_id='".$roww['fs_masterid']."' ");
        $sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount=fs_topiccount-".$sc_topics." WHERE fs_id='".$roww['fs_masterid']."' ");

        sed_forum_sectionsetlast($row['fs_masterid']);
    }

    $sql = sed_sql_query("DELETE FROM $db_forum_posts WHERE fp_sectionid='$id'");
    $num = sed_sql_affectedrows();
    $sql = sed_sql_query("DELETE FROM $db_forum_topics WHERE ft_sectionid='$id'");
    $num = $num + sed_sql_affectedrows();
    $sql = sed_sql_query("DELETE FROM $db_forum_sections WHERE fs_id='$id'");
    $num = $num + sed_sql_affectedrows();
    $sql = sed_sql_query("DELETE FROM $db_auth WHERE auth_code='forums' AND auth_option='$id'");
    $num = $num + sed_sql_affectedrows();
    return($num);
}

/* ------------------ */

function sed_forum_resync($id)
{
    global $db_forum_topics, $db_forum_posts, $db_forum_sections;

    $sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_sections WHERE fs_masterid='$id' ");
    $result = sed_sql_result($sql, 0, "COUNT(*)");

    if (!$result)
    {
        $sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_sectionid='$id'");
        $num = sed_sql_result($sql,0,"COUNT(*)");
        $sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount='$num' WHERE fs_id='$id'");
        $sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_sectionid='$id'");
        $num = sed_sql_result($sql, 0, "COUNT(*)");
        $sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount='$num' WHERE fs_id='$id'");
    }
    else
    {
        $sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_sectionid='$id'");
        $num = sed_sql_result($sql, 0, "COUNT(*)");
        $sql = sed_sql_query("SELECT SUM(fs_topiccount) FROM $db_forum_sections WHERE fs_masterid='$id'");
        $num = $num + sed_sql_result($sql, 0, "SUM(fs_topiccount)");
        $sql = sed_sql_query("UPDATE $db_forum_sections SET fs_topiccount='$num' WHERE fs_id='$id'");
        $sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_sectionid='$id'");
        $num = sed_sql_result($sql, 0, "COUNT(*)");
        $sql = sed_sql_query("SELECT SUM(fs_postcount) FROM $db_forum_sections WHERE fs_masterid='$id'");
        $num = $num + sed_sql_result($sql, 0, "SUM(fs_postcount)");
        $sql = sed_sql_query("UPDATE $db_forum_sections SET fs_postcount='$num' WHERE fs_id='$id'");
    }

    return;
}

/* ------------------ */

function sed_forum_resynctopic($id)
{
    global $db_forum_topics, $db_forum_posts;

    $sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_topicid='$id'");
    $num = sed_sql_result($sql, 0, "COUNT(*)");
    $sql = sed_sql_query("UPDATE $db_forum_topics SET ft_postcount='$num' WHERE ft_id='$id'");

    $sql = sed_sql_query("SELECT fp_posterid, fp_postername, fp_updated
    FROM $db_forum_posts
    WHERE fp_topicid='$id'
    ORDER BY fp_id DESC LIMIT 1");

    if ($row = sed_sql_fetcharray($sql))
    {
        $sql = sed_sql_query("UPDATE $db_forum_topics
        SET ft_lastposterid='".(int)$row['fp_posterid']."',
            ft_lastpostername='".sed_sql_prep($row['fp_last_postername'])."',
            ft_updated='".(int)$row['fp_last_updated']."'
        WHERE ft_id='$id'");

    }
    return;
}

/* ------------------ */

function sed_forum_resyncall()
{
    global $db_forum_sections;

    $sql = sed_sql_query("SELECT fs_id FROM $db_forum_sections");
    while ($row = sed_sql_fetcharray($sql))
    {
    	sed_forum_resync($row['fs_id']);
    }
    return;
}

/* ------------------ */

function sed_linkif($url, $text, $cond)
{
    if ($cond)
    {
    	$res = "<a href=\"".$url."\">".$text."</a>";
    }
    else
    {
    	$res = $text;
    }

    return($res);
}

/* ------------------ */

function sed_loadcharsets()
{
    $result = array();
    $result[] = array('ISO-10646-UTF-1', 'ISO-10646-UTF-1 / Universal Transfer Format');
    $result[] = array('UTF-8', 'UTF-8 / Standard Unicode');
    $result[] = array('ISO-8859-1', 'ISO-8859-1 / Western Europe');
    $result[] = array('ISO-8859-2', 'ISO-8859-2 / Middle Europe');
    $result[] = array('ISO-8859-3', 'ISO-8859-3 / Maltese');
    $result[] = array('ISO-8859-4', 'ISO-8859-4 / Baltic');
    $result[] = array('ISO-8859-5', 'ISO-8859-5 / Cyrillic');
    $result[] = array('ISO-8859-6', 'ISO-8859-6 / Arabic');
    $result[] = array('ISO-8859-7', 'ISO-8859-7 / Greek');
    $result[] = array('ISO-8859-8', 'ISO-8859-8 / Hebrew');
    $result[] = array('ISO-8859-9', 'ISO-8859-9 / Turkish');
    $result[] = array('ISO-2022-KR', 'ISO-2022-KR / Korean');
    $result[] = array('ISO-2022-JP', 'ISO-2022-JP / Japanese');
    $result[] = array('windows-1250', 'windows-1250 / Central European');
    $result[] = array('windows-1251', 'windows-1251 / Russian');
    $result[] = array('windows-1252', 'windows-1252 / Western Europe');
    $result[] = array('windows-1254', 'windows-1254 / Turkish');
    $result[] = array('EUC-JP', 'EUC-JP / Japanese');
    $result[] = array('GB2312', 'GB2312 / Chinese simplified');
    $result[] = array('BIG5', 'BIG5 / Chinese traditional');
    $result[] = array('tis-620', 'Tis-620 / Thai');
    return($result);
}

/* ------------------ */

function sed_loaddoctypes()
{
    $result = array();
    $result[] = array(0, 'HTML 4.01');
    $result[] = array(1, 'HTML 4.01 Transitional');
    $result[] = array(2, 'HTML 4.01 Frameset');
    $result[] = array(3, 'XHTML 1.0 Strict');
    $result[] = array(4, 'XHTML 1.0 Transitional');
    $result[] = array(5, 'XHTML 1.0 Frameset');
    $result[] = array(6, 'XHTML 1.1');
    $result[] = array(7, 'XHTML 2');
    return($result);
}

/* ------------------ */

function sed_structure_delcat($id, $c)
{
    global $db_structure, $db_auth, $cfg, $cot_cache;

    $sql = sed_sql_query("DELETE FROM $db_structure WHERE structure_id='$id'");
    $sql = sed_sql_query("DELETE FROM $db_auth WHERE auth_code='page' AND auth_option='$c'");
    sed_auth_clear('all');
    $cfg['cache'] && $cot_cache->db_unset('sed_cat', 'system');
}

/* ------------------ */

/* ------------------ */

function sed_structure_newcat($code, $path, $title, $desc, $icon, $group, $order, $way, $extra_array = array())
{
    global $db_structure, $db_auth, $sed_groups, $usr, $cfg, $cot_cache;

    $res = FALSE;

    if (!empty($title) && !empty($code) && !empty($path) && $code != 'all')
    {
        $sql = sed_sql_query("SELECT structure_code FROM $db_structure WHERE structure_code='".sed_sql_prep($code)."' LIMIT 1");
        if (sed_sql_numrows($sql) == 0)
        {
			$colname = '';
			$colvalue = '';
			if (is_array($extra_array))
			{
				while (list($i, $x) = each($extra_array))
				{
					$colname .= ", structure_".$i;
					$colvalue .= ", '".sed_sql_prep($x)."'";
				}
			}
            $sql = sed_sql_query("INSERT INTO $db_structure (structure_code, structure_path, structure_title, structure_desc, structure_icon, structure_group, structure_order".$colname.") VALUES ('".sed_sql_prep($code)."', '".sed_sql_prep($path)."', '".sed_sql_prep($title)."', '".sed_sql_prep($desc)."', '".sed_sql_prep($icon)."', ".(int)$group.", '".sed_sql_prep($order.'.'.$way)."'".$colvalue.")");

            foreach ($sed_groups as $k => $v)
            {
                if ($v['id'] == 1)
                {
                    $ins_auth = 5;
                    $ins_lock = 250;
                }
                elseif ($v['id'] == 2)
                {
                    $ins_auth = 1;
                    $ins_lock = 254;
                }
                elseif ($v['id'] == 3)
                {
                    $ins_auth = 0;
                    $ins_lock = 255;
                }
                elseif ($v['id'] == 5)
                {
                    $ins_auth = 255;
                    $ins_lock = 255;
                }
                else
                {
                    $ins_auth = 7;
                    $ins_lock = ($k == 4) ? 128 : 0;
                }
                $sql = sed_sql_query("INSERT INTO $db_auth (auth_groupid, auth_code, auth_option, auth_rights, auth_rights_lock, auth_setbyuserid) VALUES (".(int)$v['id'].", 'page', '".sed_sql_prep($code)."', ".(int)$ins_auth.", ".(int)$ins_lock.", ".(int)$usr['id'].")");
                $res = TRUE;
            }
            sed_auth_reorder();
            sed_auth_clear('all');
            $cfg['cache'] && $cot_cache->db_unset('sed_cat', 'system');
        }
    }
    return $res;
}

function sed_structure_resync($id)
{
	global $db_structure, $db_pages;

	$sql = sed_sql_query("SELECT structure_code FROM $db_structure WHERE structure_id='".$id."' ");
	$row = sed_sql_fetcharray($sql);
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pages
		WHERE page_cat='".$row['structure_code']."' AND (page_state = 0 OR page_state=2)");
	$num = (int) sed_sql_result($sql, 0, 0);
	return (bool) sed_sql_query("UPDATE $db_structure SET structure_pagecount=$num WHERE structure_id='$id'");
}

function sed_structure_resyncall()
{
	global $db_structure;

	$res = TRUE;
	$sql = sed_sql_query("SELECT structure_id FROM $db_structure");
	while ($row = sed_sql_fetchassoc($sql))
	{
		$res &= sed_structure_resync($row['structure_id']);
	}
	sed_sql_freeresult($sql);
	return $res;
}

/* ------------------ */

function sed_trash_delete($id)
{
    global $db_trash;

    $sql = sed_sql_query("DELETE FROM $db_trash WHERE tr_id='$id'");
    return (sed_sql_affectedrows());
}

/* ------------------ */

function sed_trash_get($id)
{
    global $db_trash;

    $sql = sed_sql_query("SELECT * FROM $db_trash WHERE tr_id='$id' LIMIT 1");
    if ($res = sed_sql_fetchassoc($sql))
    {
        $res['tr_datas'] = unserialize($res['tr_datas']);
        return ($res);
    }
    else
    {
    	return (FALSE);
    }
}

/* ------------------ */

function sed_trash_insert($dat, $db)
{
    foreach ($dat as $k => $v)
    {
        $columns[] = $k;
        $datas[] = "'".sed_sql_prep($v)."'";
    }
    $sql = sed_sql_query("INSERT INTO $db (".implode(', ', $columns).") VALUES (".implode(', ', $datas).")");
    return (TRUE);
}

/* ------------------ */

function sed_trash_restore($id)
{
    global $db_forum_topics, $db_forum_posts, $db_trash;

    $columns = array();
    $datas = array();

    $res = sed_trash_get($id);

    switch($res['tr_type'])
    {
        case 'comment':
            global $db_com;
            sed_trash_insert($res['tr_datas'], $db_com);
            sed_log("Comment #".$res['tr_itemid']." restored from the trash can.", 'adm');
            return (TRUE);
        break;

        case 'forumpost':
            global $db_forum_posts;
            $sql = sed_sql_query("SELECT ft_id FROM $db_forum_topics WHERE ft_id='".$res['tr_datas']['fp_topicid']."'");

            if ($row = sed_sql_fetcharray($sql))
            {
                sed_trash_insert($res['tr_datas'], $db_forum_posts);
                sed_log("Post #".$res['tr_itemid']." restored from the trash can.", 'adm');
                sed_forum_resynctopic($res['tr_datas']['fp_topicid']);
                sed_forum_sectionsetlast($res['tr_datas']['fp_sectionid']);
                sed_forum_resync($res['tr_datas']['fp_sectionid']);
                return (TRUE);
            }
            else
            {
                $sql1 = sed_sql_query("SELECT tr_id FROM $db_trash WHERE tr_type='forumtopic' AND tr_itemid='q".$res['tr_datas']['fp_topicid']."'");
                if ($row1 = sed_sql_fetcharray($sql1))
                {
                    sed_trash_restore($row1['tr_id']);
                    sed_trash_delete($row1['tr_id']);
                }
            }
		break;

        case 'forumtopic':
            global $db_forum_topics;
            sed_trash_insert($res['tr_datas'], $db_forum_topics);
            sed_log("Topic #".$res['tr_datas']['ft_id']." restored from the trash can.", 'adm');

            $sql = sed_sql_query("SELECT tr_id FROM $db_trash WHERE tr_type='forumpost' AND tr_itemid LIKE '%-".$res['tr_itemid']."'");

            while ($row = sed_sql_fetcharray($sql))
            {
                $res2 = sed_trash_get($row['tr_id']);
                sed_trash_insert($res2['tr_datas'], $db_forum_posts);
                sed_trash_delete($row['tr_id']);
                sed_log("Post #".$res2['tr_datas']['fp_id']." restored from the trash can (belongs to topic #".$res2['tr_datas']['fp_topicid'].").", 'adm');
            }

            sed_forum_resynctopic($res['tr_itemid']);
            sed_forum_sectionsetlast($res['tr_datas']['ft_sectionid']);
            sed_forum_resync($res['tr_datas']['ft_sectionid']);
            return (TRUE);
		break;

        case 'page':
            global $db_pages, $db_structure;
            sed_trash_insert($res['tr_datas'], $db_pages);
            sed_log("Page #".$res['tr_itemid']." restored from the trash can.", 'adm');
            $sql = sed_sql_query("SELECT page_cat FROM $db_pages WHERE page_id='".$res['tr_itemid']."'");
            $row = sed_sql_fetcharray($sql);
            $sql = sed_sql_query("SELECT structure_id FROM $db_structure WHERE structure_code='".$row['page_cat']."'");
            if (sed_sql_numrows($sql) == 0)
            {
                sed_structure_newcat('restored', 999, 'RESTORED', '', '', 0);
                $sql = sed_sql_query("UPDATE $db_pages SET page_cat='restored' WHERE page_id='".$res['tr_itemid']."'");
            }
            return (TRUE);
		break;

        case 'pm':
            global $db_pm;
            sed_trash_insert($res['tr_datas'], $db_pm);
            sed_log("Private message #".$res['tr_itemid']." restored from the trash can.", 'adm');
            return (TRUE);
		break;

        case 'user':
            global $db_users;
            sed_trash_insert($res['tr_datas'], $db_users);
            sed_log("User #".$res['tr_itemid']." restored from the trash can.", 'adm');
            return (TRUE);
		break;

        default:
            return (FALSE);
		break;
    }
}
// =========== Extra fields ================================
/**
 * Extra fields - Return default base html-construction for various types of fields (without value= and name=)
 *
 * @access private
 * @param string $type Type of field (input, textarea etc)
 * @return string
 *
 */
function get_default_html_construction($type)
{
	$html = "";
	switch($type)
	{
		case "input":
			$html = '<input class="text" type="text" maxlength="255" size="56" />';
		break;

		case "textarea":
			$html = '<textarea cols="80" rows="6" ></textarea>';
		break;

		case "select":
			$html = '<select></select>';
		break;

		case "checkbox":
			$html = '<input type="checkbox" />';
		break;

		case "radio":
			$html = '<input type="radio" />';
		break;
	}
	return $html;
}

/**
 * Add extra field for pages
 *
 * @param string $sql_table Table for adding extrafield (without sed_)
 * @param string $name Field name (unique)
 * @param string $type Field type (input, textarea etc)
 * @param string $html HTML display of element without parameter "name="
 * @param string $variants Variants of values (for radiobuttons, selectors etc)
 * @param string $description Description of field (optional, for admin)
 * @param bool $noalter Do not ALTER the table, just register the extra field
 * @return bool
 *
 */
function sed_extrafield_add($sql_table, $name, $type, $html, $variants="", $description="", $noalter = FALSE)
{
	global $db_extra_fields, $db_x;
	$fieldsres = sed_sql_query("SELECT field_name FROM $db_extra_fields WHERE field_location='$sql_table'");
	while($row = sed_sql_fetchassoc($fieldsres))
	{
		$extrafieldsnames[] = $row['field_name'];
	}
	if(count($extrafieldsnames)>0) if (in_array($name,$extrafieldsnames)) return 0; // No adding - fields already exist

	// Check table sed_$sql_table - if field with same name exists - exit.
	if (sed_sql_numrows(sed_sql_query("SHOW COLUMNS FROM $db_x$sql_table LIKE '%\_$name'")) > 0 && !$noalter)
	{
		return FALSE;
	}
	$fieldsres = sed_sql_query("SELECT * FROM $db_x$sql_table LIMIT 1");
	while ($i < mysql_num_fields($fieldsres))
	{
		$column = mysql_fetch_field($fieldsres, $i);
		// get column prefix in this table
		$column_prefix = substr($column->name, 0, strpos($column->name, "_"));
		preg_match("#.*?_$name$#",$column->name,$match);
		if($match[1]!="" && !$noalter) return false; // No adding - fields already exist
		$i++;
	}

	$extf['location'] = $sql_table;
	$extf['name'] = $name;
	$extf['type'] = $type;
	$extf['html'] = $html;
	$extf['variants'] = $variants;
	$extf['description'] = $description;
	$step1 = sed_sql_insert($db_extra_fields, $extf, 'field_') == 1;
	if ($noalter)
	{
		return $step1;
	}
	switch($type)
	{
		case "input": $sqltype = "VARCHAR(255)"; break;
		case "textarea": $sqltype = "TEXT"; break;
		case "select": $sqltype = "VARCHAR(255)"; break;
		case "checkbox": $sqltype = "BOOL"; break;
		case "radio": $sqltype = "VARCHAR(255)"; break;
	}
	$sql = "ALTER TABLE $db_x$sql_table ADD ".$column_prefix."_$name $sqltype ";
	$step2 = sed_sql_query($sql);
	return $step1&&$step2;
}

/**
 * Update extra field for pages
 *
 * @param string $sql_table Table contains extrafield (without sed_)
 * @param string $oldname Exist name of field
 * @param string $name Field name (unique)
 * @param string $type Field type (input, textarea etc)
 * @param string $html HTML display of element without parameter "name="
 * @param string $variants Variants of values (for radiobuttons, selectors etc)
 * @param string $description Description of field (optional, for admin)
 * @return bool
 *
 */
function sed_extrafield_update($sql_table, $oldname, $name, $type, $html, $variants="", $description="")
{
	global $db_extra_fields, $db_x;
	$fieldsres = sed_sql_query("SELECT COUNT(*) FROM $db_extra_fields
			WHERE field_name = '$oldname' AND field_location='$sql_table'");
	if (sed_sql_numrows($fieldsres) <= 0
		|| $name != $oldname
			&& sed_sql_numrows(sed_sql_query("SHOW COLUMNS FROM $db_x$sql_table LIKE '%\_$name'")) > 0)
	{
		// Attempt to edit non-extra field or override an existing field
		return FALSE;
	}
	$field = sed_sql_fetchassoc($fieldsres);
	$fieldsres = sed_sql_query("SELECT * FROM $db_x$sql_table LIMIT 1");
	$column = mysql_fetch_field($fieldsres, 0);
	$column_prefix = substr($column->name, 0, strpos($column->name, "_"));
	$alter = FALSE;
	if ($name != $field['field_name'])
	{
		$extf['name'] = $name;
		$alter = TRUE;
	}
	if ($type != $field['field_type'])
	{
		$extf['type'] = $type;
		$alter = TRUE;
	}
	if ($html != $field['field_html'])
		$extf['html'] = $html;
	if ($variants != $field['field_variants'])
		$extf['variants'] = $variants;
	if ($description != $field['field_description'])
		$extf['description'] = $description;
	$step1 = sed_sql_update($db_extra_fields, "field_name = '$oldname' AND field_location='$sql_table'", $extf, 'field_') == 1;

	if (!$alter) return $step1;

	switch ($type)
	{
		case "input": $sqltype = "VARCHAR(255)"; break;
		case "textarea": $sqltype = "TEXT"; break;
		case "select": $sqltype = "VARCHAR(255)"; break;
		case "checkbox": $sqltype = "BOOL"; break;
		case "radio": $sqltype = "VARCHAR(255)"; break;
	}
	$sql = "ALTER TABLE $db_x$sql_table CHANGE ".$column_prefix."_$oldname ".$column_prefix."_$name $sqltype ";
	$step2 = sed_sql_query($sql);

	return $step1&&$step2;
}

/**
 * Delete extra field
 *
 * @param string $sql_table Table contains extrafield (without sed_)
 * @param string $name Name of extra field
 * @return bool
 *
 */
function sed_extrafield_remove($sql_table, $name)
{
	global $db_extra_fields, $db_x;
	if ((int) sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM $db_extra_fields
		WHERE field_name = '$name' AND field_location='$sql_table'"), 0, 0) <= 0)
	{
		// Attempt to remove non-extra field
		return FALSE;
	}
	$fieldsres = sed_sql_query("SELECT * FROM $db_x$sql_table LIMIT 1");
	$column = mysql_fetch_field($fieldsres, 0);
	$column_prefix = substr($column->name, 0, strpos($column->name, "_"));
	$step1 = sed_sql_delete($db_extra_fields, "field_name = '$name' AND field_location='$sql_table'") == 1;
	$sql = "ALTER TABLE $db_x$sql_table DROP ".$column_prefix."_".$name;
	$step2 = sed_sql_query($sql);
	return $step1&&$step2;
}

/**
 * Extract info from SED file headers
 *
 * @param string $file File path
 * @param string $limiter Tag name
 * @param int $maxsize Max header size
 * @return array
 */
function sed_infoget($file, $limiter='SED', $maxsize=32768)
{
	$result = array();

	if($fp = @fopen($file, 'r'))
	{
		$limiter_begin = "[BEGIN_".$limiter."]";
		$limiter_end = "[END_".$limiter."]";
		$data = fread($fp, $maxsize);
		$begin = mb_strpos($data, $limiter_begin);
		$end = mb_strpos($data, $limiter_end);

		if ($end>$begin && $begin>0)
		{
			$lines = mb_substr($data, $begin+8+mb_strlen($limiter), $end-$begin-mb_strlen($limiter)-8);
			$lines = explode ("\n",$lines);

			foreach ($lines as $k => $line)
			{
				$linex = explode ("=", $line);
				$ii=1;
				while (!empty($linex[$ii]))
				{
					$result[$linex[0]] .= trim($linex[$ii]);
					$ii++;
				}
			}
		}
		else
		{ $result['Error'] = 'Warning: No tags found in '.$file; }
	}
	else
	{ $result['Error'] = 'Error: File '.$file.' is missing!'; }
	@fclose($fp);
	return ($result);
}

/**
 * Returns forum category dropdown code
 *
 * @param int $check Selected category
 * @param string $name Dropdown name
 * @return string
 */
function sed_selectbox_forumcat($check, $name)
{
	global $usr, $sed_forums_str, $L;

	$result =  "<select name=\"$name\" size=\"1\">";
	if (is_array($sed_forums_str))
	foreach($sed_forums_str as $i => $x)
	{
		$selected = ($i==$check) ? "selected=\"selected\"" : '';
		$result .= "<option value=\"".$i."\" $selected> ".$x['tpath']."</option>";
	}
	$result .= "</select>";
	return($result);
}

/**
 * Returns group selection dropdown code
 *
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param array $skip Hidden groups
 * @return string
 */
function sed_selectbox_groups($check, $name, $skip=array(0))
{
	global $sed_groups;

	$res = "<select name=\"$name\" size=\"1\">";

	foreach($sed_groups as $k => $i)
	{
		$selected = ($k==$check) ? "selected=\"selected\"" : '';
		$res .= (in_array($k, $skip)) ? '' : "<option value=\"$k\" $selected>".$sed_groups[$k]['title']."</option>";
	}
	$res .= "</select>";

	return($res);
}

/**
 * Returns substring position in file
 *
 * @param string $file File path
 * @param string $str Needle
 * @param int $maxsize Search limit
 * @return int
 */
function sed_stringinfile($file, $str, $maxsize=32768)
{
	if ($fp = @fopen($file, 'r'))
	{
		$data = fread($fp, $maxsize);
		$pos = mb_strpos($data, $str);
		$result = !($pos===FALSE);
	}
	else
	{ $result = FALSE; }
	@fclose($fp);
	return ($result);
}
?>