<?php
/**
 * Seditio Compatibility uninstaller
 *
 * @package seditio
 * @version 0.7.0
 * @author Trustmaster, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

// Remove plugin bbcodes
sed_bbcode_remove(0, 'seditio')
?>
