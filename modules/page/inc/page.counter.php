<?php
/**
 * Page views counter. For cached pages.
 *
 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $a
 */

defined('COT_CODE') or die('Wrong URL');

list(\Cot::$usr['auth_read'], \Cot::$usr['auth_write'], \Cot::$usr['isadmin']) = cot_auth('page', 'any');
cot_block(\Cot::$usr['auth_read']);

$id = cot_import('id', 'G', 'INT');
cot_die(empty($id) || empty($a), true);

switch ($a) {
    case 'views':
        \Cot::$db->query(
            'UPDATE ' . \Cot::$db->pages . ' SET page_count = page_count + 1 WHERE page_id = ?',
            $id
        );
}

exit();