<?php
/**
 * Removes all implanted configs
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

global $db, $db_config;
$db->delete($db_config, "config_donor = 'comments'");
