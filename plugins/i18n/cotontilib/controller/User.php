<?php
defined('COT_CODE') or die('Wrong URL.');

/**
 *  Cotonti Lib plugin for Cotonti Siena
 *    User Controller
 *
 * @package Cotonti Lib
 * @author  Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright © Portal30 Studio http://portal30.ru
 */
class cotontilib_controller_User
{

    /**
     * Main (index) Action.
     */
    public function indexAction()
    {
        cot_die_message(404);
        return null;
    }

    /**
     * Ajax users suggest for Select2
     *
     * @return null
     *
     * @todo  with avatar
     */
    public function suggestAction()
    {
        if(cot::$usr['id'] == 0) cot_die_message(404, false);
        if(!cot_check_xp() && !cot_check_xg(false)) cot_die_message(404, false);

        $var = 'term';
        if(isset($GET['q'])) $var = 'q';
        $q = cot_import($var, 'G', 'TXT');

        $res = array(
            'total' => 0,
            'results' => array()
        );

        if($q == '') {
            cot_sendheaders('application/json');
            echo json_encode($res);
            exit();
        }

        $page = cot_import('page', 'G', 'INT');
        if(!$page) $page = 1;
        $limit = cot_import('page_limit', 'G', 'INT');

        $showLogin = cot_import('show_login', 'G', 'INT', 1);

        $offset = ($page - 1) * $limit;

        $limit = ($limit) ? "LIMIT $limit OFFSET $offset" : '';
        $order = '`user_name` ASC';
        $where = array("`user_name` LIKE ".cot::$db->quote($q.'%'));

        $fields = array();

        if(!empty(cot::$extrafields[cot::$db->users])) {
            $haveLastName = false;
            if(isset(cot::$extrafields[cot::$db->users]['lastname'])) {
                $fields[] = '`user_lastname`';
                $where[]  = "`user_lastname` LIKE ".cot::$db->quote($q.'%');
                $order = '`user_lastname` ASC';
                $haveLastName = true;
            }
            if(isset(cot::$extrafields[cot::$db->users]['last_name'])) {
                $fields[] = '`user_last_name`';
                $where[]  = "`user_last_name` LIKE ".cot::$db->quote($q.'%');
                $order = '`user_last_name` ASC';
                $haveLastName = true;
            }
            if(isset(cot::$extrafields[cot::$db->users]['firstname'])) {
                $fields[] = '`user_firstname`';
                $where[]  = "`user_firstname` LIKE ".cot::$db->quote($q.'%');
                $order = ($haveLastName) ? $order.', ' : '';
                $order .=  '`user_firstname` ASC';
            }
            if(isset(cot::$extrafields[cot::$db->users]['first_name'])) {
                $fields[] = '`user_first_name`';
                $where[]  = "`user_first_name` LIKE ".cot::$db->quote($q.'%');
                $order = ($haveLastName) ? $order.', ' : '';
                $order .=  '`user_first_name` ASC';
            }
            if(isset(cot::$extrafields[cot::$db->users]['middlename'])) {
                $fields[] = '`user_middlename`';
                $where[]  = "`user_middlename` LIKE ".cot::$db->quote($q.'%');
            }
            if(isset(cot::$extrafields[cot::$db->users]['middle_name'])) {
                $fields[] = '`user_middle_name`';
                $where[]  = "`user_middle_name` LIKE ".cot::$db->quote($q.'%');
            }
        }

        if($where > 1 && !$showLogin) unset($where[0]);

        $where = "WHERE ".implode(' OR ', $where);
        if(!empty($fields)) {
            $fields = ', '.implode(', ', $fields);
        } else {
            $fields = '';
        }

        $order = "ORDER BY {$order}";

        $res['total'] = cot::$db->query("SELECT COUNT(*) FROM ".cot::$db->users." $where\n")->fetchColumn();

        if($res['total'] > 0) {
            $sql_users = cot::$db->query("SELECT `user_id`, `user_name` {$fields} FROM " . cot::$db->users . " $where\n $order\n $limit");

            while ($row = $sql_users->fetch()) {
                $fullName = $userName = cot_user_full_name($row);
                if ($showLogin && $userName != $row['user_name']) {
                    $userName .= " ({$row['user_name']})";
                }

                $tmp = array(
                    'id' => $row['user_id'],
                    'text' => htmlspecialchars($userName),
                    'fullname' => $fullName
                );
                if ($showLogin) {
                    $tmp['login'] = htmlspecialchars($row['user_name']);
                }

                $res['results'][] = $tmp;
            }
            $sql_users->closeCursor();
        }

        cot_sendheaders('application/json');

        echo json_encode($res);
        exit();
    }
}