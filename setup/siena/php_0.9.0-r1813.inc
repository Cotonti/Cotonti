<?php
/**
 * r1813: Dissolution of the Netherlands Antilles
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

global $db_users;

$sql = $db->query("UPDATE $db_users SET user_country = 'cw' WHERE user_country = 'an'");

?>