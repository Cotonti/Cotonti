<?php
/**
 * markItUp! uninstall handler
 *
 * @package markitup
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

// Remove plugin bbcodes
sed_bbcode_remove(0, 'markitup');
sed_bbcode_clearcache();
?>
